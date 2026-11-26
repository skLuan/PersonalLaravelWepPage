<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Copies binshops_blog_* rows into posts / categories / seo_meta.
 *
 * Safe to run more than once: rows are matched on legacy_id and updated.
 * The binshops tables are only read, never changed.
 */
class ImportBinshopsBlog extends Command
{
    protected $signature = 'blog:import-binshops
                            {--locale= : Force this locale for every post (otherwise guessed from the text)}
                            {--set=* : Override one post\'s locale, as <binshops id>:<locale> (repeatable)}
                            {--pair=* : Mark two binshops posts as translations, as <id>:<id> (repeatable)}
                            {--dry-run : Show what would be imported, write nothing}';

    protected $description = 'Import posts and categories from the old binshops blog tables';

    private const STOPWORDS = [
        'es' => ['de', 'la', 'que', 'el', 'en', 'y', 'los', 'del', 'las', 'por', 'un', 'una', 'para', 'con', 'es', 'se', 'no', 'más', 'como', 'su', 'lo', 'al', 'pero', 'sus', 'este', 'esta'],
        'en' => ['the', 'and', 'of', 'to', 'is', 'in', 'that', 'it', 'for', 'you', 'with', 'on', 'are', 'this', 'as', 'be', 'was', 'but', 'not', 'have', 'by', 'from', 'or', 'an', 'we', 'they'],
    ];

    public function handle(): int
    {
        if (! Schema::hasTable('binshops_blog_posts')) {
            $this->error('Table binshops_blog_posts does not exist: nothing to import.');

            return self::FAILURE;
        }

        $locales = config('app.available_locales', ['en']);
        $forced = $this->option('locale');
        if ($forced && ! in_array($forced, $locales, true)) {
            $this->error("Unknown locale [{$forced}]. Use one of: ".implode(', ', $locales));

            return self::FAILURE;
        }
        $overrides = $this->parsePairs($this->option('set'), 'set');
        $pairs = $this->parsePairs($this->option('pair'), 'pair');
        if ($overrides === null || $pairs === null) {
            return self::FAILURE;
        }

        $rows = DB::table('binshops_blog_posts')->orderBy('id')->get();
        $categoryRows = DB::table('binshops_blog_categories')->get()->keyBy('id');
        $postCategories = DB::table('binshops_blog_post_categories')->get()->groupBy('binshops_blog_post_id');
        $userIds = User::query()->pluck('id')->flip();

        // 1. Decide the locale of each post.
        $plan = $rows->map(function ($row) use ($forced, $overrides, $locales) {
            $locale = $overrides[$row->id] ?? $forced ?? $this->guessLocale($row->title.' '.$row->subtitle.' '.strip_tags((string) $row->post_body));

            return ['row' => $row, 'locale' => in_array($locale, $locales, true) ? $locale : config('app.locale')];
        })->keyBy(fn ($item) => $item['row']->id);

        // 2. Translation groups: paired posts share one; must be different languages.
        $groups = [];
        foreach ($pairs as $a => $b) {
            if (! isset($plan[$a], $plan[$b])) {
                $this->error("--pair {$a}:{$b}: unknown binshops post id.");

                return self::FAILURE;
            }
            if ($plan[$a]['locale'] === $plan[$b]['locale']) {
                $this->error("--pair {$a}:{$b}: both posts are [{$plan[$a]['locale']}]. Fix one with --set.");

                return self::FAILURE;
            }
            $groups[$a] = $groups[$b] = $groups[$a] ?? $groups[$b] ?? (string) Str::uuid();
        }

        $this->table(
            ['binshops id', 'locale', 'published', 'slug', 'title'],
            $plan->map(fn ($item) => [
                $item['row']->id,
                $item['locale'],
                $item['row']->is_published ? 'yes' : 'no',
                $item['row']->slug,
                Str::limit((string) $item['row']->title, 50),
            ])->values()->all()
        );

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$plan->count()} post(s) would be imported. Nothing was written.");

            return self::SUCCESS;
        }

        DB::transaction(function () use ($plan, $groups, $categoryRows, $postCategories, $userIds) {
            foreach ($plan as $id => $item) {
                $row = $item['row'];
                $locale = $item['locale'];

                $post = Post::firstOrNew(['legacy_id' => $id]);
                $post->fill([
                    'locale' => $locale,
                    'slug' => $row->slug,
                    'title' => $row->title ?: 'Untitled',
                    'subtitle' => $row->subtitle ?: null,
                    'excerpt' => ($row->short_description ?? null) ?: null,
                    // config('binshopsblog.echo_html') was true: post_body was printed as-is.
                    'body' => $row->post_body,
                    'cover_image' => $row->image_large ?: ($row->image_medium ?: ($row->image_thumbnail ?: null)),
                    'author_id' => $row->user_id !== null && $userIds->has($row->user_id) ? $row->user_id : null,
                    'published_at' => $row->is_published ? Carbon::parse($row->posted_at ?? $row->created_at) : null,
                ]);
                // binshops had no alt text; the title is a better default than nothing.
                if ($post->cover_image && blank($post->cover_alt)) {
                    $post->cover_alt = $post->title;
                }
                if (isset($groups[$id])) {
                    $post->translation_group = $groups[$id];
                }
                $post->created_at = $row->created_at;
                $post->save();

                $seoTitle = $row->seo_title ?? null;
                $metaDesc = $row->meta_desc ?? null;
                if (filled($seoTitle) || filled($metaDesc)) {
                    $post->seo()->updateOrCreate([], [
                        'title' => filled($seoTitle) && $seoTitle !== $post->title ? $seoTitle : null,
                        'description' => $metaDesc ? Str::limit(trim(strip_tags($metaDesc)), 317) : null,
                    ]);
                }

                $categoryIds = collect($postCategories->get($id, []))
                    ->map(fn ($link) => $categoryRows->get($link->binshops_blog_category_id))
                    ->filter()
                    ->map(fn ($legacy) => $this->importCategory($legacy, $locale)->id);
                $post->categories()->sync($categoryIds);
            }
        });

        $this->info("Imported {$plan->count()} post(s). Check them at /admin/posts, then link translations there.");

        return self::SUCCESS;
    }

    /** One category per (binshops category, locale); locales of the same one share a group. */
    private function importCategory(object $legacy, string $locale): Category
    {
        $existing = Category::where('legacy_id', $legacy->id)->get();

        if ($category = $existing->firstWhere('locale', $locale)) {
            return $category;
        }

        return Category::create([
            'legacy_id' => $legacy->id,
            'locale' => $locale,
            'translation_group' => $existing->first()?->translation_group ?? (string) Str::uuid(),
            'slug' => $legacy->slug,
            'name' => $legacy->category_name ?: $legacy->slug,
            'description' => $legacy->category_description ?: null,
        ]);
    }

    /** Count common short words of each language; highest share wins. */
    public function guessLocale(string $text): string
    {
        $words = preg_split('/[^\p{L}]+/u', mb_strtolower($text), -1, PREG_SPLIT_NO_EMPTY);
        if (! $words) {
            return config('app.locale');
        }

        $counts = array_count_values($words);
        $scores = collect(self::STOPWORDS)->map(
            fn (array $stopwords) => array_sum(array_intersect_key($counts, array_flip($stopwords)))
        );

        return $scores->max() > 0 ? $scores->sortDesc()->keys()->first() : config('app.locale');
    }

    /**
     * Parse "<a>:<b>" options. Returns [a => b] or null after printing an error.
     *
     * @param  array<int, string>  $values
     */
    private function parsePairs(array $values, string $option): ?array
    {
        $parsed = [];
        foreach ($values as $value) {
            if (! preg_match('/^(\d+):(\w+)$/', $value, $m)) {
                $this->error("--{$option}={$value}: expected <id>:<value>.");

                return null;
            }
            $parsed[(int) $m[1]] = ctype_digit($m[2]) ? (int) $m[2] : $m[2];
        }

        return $parsed;
    }
}
