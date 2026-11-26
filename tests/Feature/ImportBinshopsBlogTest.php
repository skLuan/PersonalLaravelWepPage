<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ImportBinshopsBlogTest extends TestCase
{
    use RefreshDatabase;

    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userId = User::factory()->create()->id;
        $now = now();

        DB::table('binshops_blog_categories')->insert([
            ['id' => 1, 'category_name' => 'Reflexiones', 'slug' => 'reflexiones', 'created_at' => $now, 'updated_at' => $now],
        ]);
        DB::table('binshops_blog_posts')->insert([
            [
                'id' => 10, 'user_id' => $this->userId, 'slug' => 'existir-como-humano', 'title' => 'Existir como humano',
                'subtitle' => 'Una reflexión', 'post_body' => '<p>Esta es una de las reflexiones que tengo sobre el tiempo y la materia, y es para ti.</p>',
                'short_description' => 'Corto', 'meta_desc' => 'Meta en español', 'seo_title' => 'SEO título',
                'is_published' => true, 'posted_at' => '2025-05-01 10:00:00', 'image_large' => 'existir-1000x700.jpg',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 11, 'user_id' => 999, 'slug' => 'being-human', 'title' => 'Being human',
                'subtitle' => '', 'post_body' => '<p>This is one of the thoughts that I have about the time and the matter, and it is for you.</p>',
                'short_description' => null, 'meta_desc' => null, 'seo_title' => null,
                'is_published' => true, 'posted_at' => '2025-05-02 10:00:00', 'image_large' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'id' => 12, 'user_id' => $this->userId, 'slug' => 'borrador', 'title' => 'Borrador',
                'subtitle' => null, 'post_body' => '<p>Esto no está listo para la web.</p>',
                'short_description' => null, 'meta_desc' => null, 'seo_title' => null,
                'is_published' => false, 'posted_at' => null, 'image_large' => null,
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
        DB::table('binshops_blog_post_categories')->insert([
            ['binshops_blog_post_id' => 10, 'binshops_blog_category_id' => 1],
            ['binshops_blog_post_id' => 11, 'binshops_blog_category_id' => 1],
        ]);
    }

    public function test_import_guesses_languages_and_pairs_translations(): void
    {
        $this->artisan('blog:import-binshops', ['--pair' => ['10:11']])->assertSuccessful();

        $es = Post::where('legacy_id', 10)->firstOrFail();
        $en = Post::where('legacy_id', 11)->firstOrFail();

        $this->assertSame('es', $es->locale);
        $this->assertSame('en', $en->locale);
        $this->assertSame($es->translation_group, $en->translation_group);
        $this->assertSame('2025-05-01 10:00:00', $es->published_at->format('Y-m-d H:i:s'));
        $this->assertSame('existir-1000x700.jpg', $es->cover_image);
        $this->assertSame('Existir como humano', $es->cover_alt);
        $this->assertSame($this->userId, $es->author_id);
        $this->assertNull($en->author_id, 'unknown user ids are dropped');
        $this->assertNull($en->subtitle);
        $this->assertSame('SEO título', $es->seo->title);
        $this->assertSame('Meta en español', $es->seo->description);
        $this->assertNull($en->seo);

        $this->assertNull(Post::where('legacy_id', 12)->value('published_at'), 'unpublished → draft');

        // One category per language, sharing a translation group.
        $categories = Category::where('legacy_id', 1)->get();
        $this->assertEqualsCanonicalizing(['es', 'en'], $categories->pluck('locale')->all());
        $this->assertCount(1, $categories->pluck('translation_group')->unique());
        $this->assertSame('es', $es->categories->first()->locale);

        // Old URL keeps working.
        $this->get('/blog/existir-como-humano')->assertRedirect('/es/blog/existir-como-humano');
    }

    public function test_import_can_run_twice(): void
    {
        $this->artisan('blog:import-binshops')->assertSuccessful();
        $this->artisan('blog:import-binshops', ['--set' => ['11:es']])->assertSuccessful();

        $this->assertSame(3, Post::count());
        $this->assertSame('es', Post::where('legacy_id', 11)->value('locale'));
    }

    public function test_dry_run_writes_nothing(): void
    {
        $this->artisan('blog:import-binshops', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame(0, Post::count());
    }

    public function test_pairing_two_posts_of_the_same_language_fails(): void
    {
        $this->artisan('blog:import-binshops', ['--pair' => ['10:12']])->assertFailed();

        $this->assertSame(0, Post::count());
    }
}
