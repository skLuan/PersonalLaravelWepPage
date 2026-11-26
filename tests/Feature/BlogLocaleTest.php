<?php

namespace Tests\Feature;

use App\Models\User;
use BinshopsBlog\Models\BinshopsBlogCategory;
use BinshopsBlog\Models\BinshopsBlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogLocaleTest extends TestCase
{
    use RefreshDatabase;

    private BinshopsBlogPost $post;

    protected function setUp(): void
    {
        parent::setUp();

        $category = BinshopsBlogCategory::create([
            'category_name' => 'Dev notes',
            'slug' => 'dev-notes',
        ]);

        $post = new BinshopsBlogPost();
        $post->forceFill([
            'user_id' => User::factory()->create()->id,
            'title' => 'Hello squids',
            'slug' => 'hello-squids',
            'short_description' => 'A first post',
            'post_body' => '<p>Body</p>',
            'is_published' => true,
            'posted_at' => now()->subDay(),
        ])->save();
        $post->categories()->attach($category->id);

        $this->post = $post;
    }

    public static function blogIndexes(): array
    {
        return [
            'fallback' => ['/blog'],
            'spanish' => ['/es/blog'],
            'english' => ['/en/blog'],
        ];
    }

    /** @dataProvider blogIndexes */
    public function test_blog_index_lists_posts(string $url): void
    {
        $this->get($url)->assertOk()->assertSee('Hello squids');
    }

    public function test_localized_index_links_stay_in_the_locale(): void
    {
        $this->get('/es/blog')
            ->assertOk()
            ->assertSee(url('/es/blog/hello-squids'), false);
    }

    public function test_localized_single_post_and_category(): void
    {
        $this->get('/es/blog/hello-squids')->assertOk()->assertSee('Hello squids');
        $this->get('/es/blog/category/dev-notes')
            ->assertOk()
            ->assertSee(url('/es/blog/hello-squids'), false);
        $this->get('/es/blog/does-not-exist')->assertNotFound();
    }

    public function test_search_renders_in_every_locale(): void
    {
        $this->get('/blog/search?s=squids')->assertOk();
        // search_form.blade.php calls route('binshopsblog.search') without a locale
        $this->get('/es/blog/search?s=squids')
            ->assertOk()
            ->assertSee("action='".url('/es/blog/search')."'", false);
    }

    public function test_rss_feed(): void
    {
        $this->get('/es/blog/feed')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<language>es</language>', false)
            ->assertSee(url('/es/blog/hello-squids'), false);

        $this->get('/blog/feed')->assertOk()->assertSee('Hello squids');
    }

    public function test_unpublished_posts_are_not_in_the_feed(): void
    {
        $this->post->forceFill(['is_published' => false])->save();

        $this->get('/blog/feed')->assertOk()->assertDontSee('Hello squids');
    }

    public function test_language_switcher_keeps_the_page(): void
    {
        $this->from('/blog')->get('/locale/es')->assertRedirect('/es/blog');
        $this->from('/es/blog/hello-squids?x=1')->get('/locale/en')->assertRedirect('/en/blog/hello-squids?x=1');
        $this->from('/es')->get('/locale/en')->assertRedirect('/en');
    }

    public function test_language_switcher_keeps_pages_that_have_no_localized_version(): void
    {
        $this->from('/projects')->get('/locale/es')->assertRedirect('/projects');
        $this->from('/login')->get('/locale/es')->assertRedirect('/login');
    }
}
