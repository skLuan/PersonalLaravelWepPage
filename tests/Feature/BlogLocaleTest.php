<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogLocaleTest extends TestCase
{
    use RefreshDatabase;

    private Post $en;

    private Post $es;

    protected function setUp(): void
    {
        parent::setUp();

        $this->en = Post::factory()->create(['slug' => 'hello-squids', 'title' => 'Hello squids', 'body' => '<p>Squid body</p>']);
        $this->es = Post::factory()->translationOf($this->en, 'es')->create(['slug' => 'hola-calamares', 'title' => 'Hola calamares']);

        $enCategory = Category::factory()->create(['slug' => 'dev-notes', 'name' => 'Dev notes']);
        $this->en->categories()->attach($enCategory);
        $esCategory = Category::factory()->create(['slug' => 'notas', 'name' => 'Notas', 'locale' => 'es', 'translation_group' => $enCategory->translation_group]);
        $this->es->categories()->attach($esCategory);
    }

    public function test_each_locale_lists_only_its_posts(): void
    {
        $this->get('/en/blog')->assertOk()->assertSee('Hello squids')->assertDontSee('Hola calamares')
            ->assertSee(url('/en/blog/hello-squids'), false);
        $this->get('/es/blog')->assertOk()->assertSee('Hola calamares')->assertDontSee('Hello squids');
    }

    public function test_blog_index_has_one_h1_and_post_titles_as_h2(): void
    {
        $html = $this->get('/en/blog')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('<h2 class="bg-skl-black-50 w-fit text-2xl">', $html);
    }

    public function test_unprefixed_blog_urls_redirect_to_a_locale(): void
    {
        $this->withHeader('Accept-Language', 'es-CO,es;q=0.9')->get('/blog')->assertRedirect('/es/blog');
        // Old binshops post links go to the post's own language, permanently.
        $this->get('/blog/hola-calamares')->assertStatus(301)->assertRedirect('/es/blog/hola-calamares');
        $this->get('/blog/category/dev-notes')->assertStatus(301)->assertRedirect('/en/blog/category/dev-notes');
        $this->get('/blog/nope')->assertNotFound();
    }

    public function test_single_post_and_category(): void
    {
        $this->get('/en/blog/hello-squids')
            ->assertOk()
            ->assertSee('Hello squids')
            ->assertSee('<p>Squid body</p>', false)
            ->assertSee('hreflang="es"', false)
            ->assertSee(url('/es/blog/hola-calamares'), false);

        $this->get('/en/blog/category/dev-notes')->assertOk()->assertSee(url('/en/blog/hello-squids'), false);
        $this->get('/en/blog/does-not-exist')->assertNotFound();
    }

    public function test_slug_from_another_language_goes_to_the_translation(): void
    {
        $this->get('/en/blog/hola-calamares')->assertRedirect('/en/blog/hello-squids');
        $this->get('/es/blog/category/dev-notes')->assertRedirect('/es/blog/category/notas');
    }

    public function test_drafts_are_hidden_except_for_admins(): void
    {
        $draft = Post::factory()->draft()->create(['slug' => 'secret']);

        $this->get('/en/blog/secret')->assertNotFound();
        $this->get('/en/blog')->assertDontSee($draft->title);
        $this->actingAs(User::factory()->admin()->create())->get('/en/blog/secret')->assertOk();
    }

    public function test_scheduled_posts_are_hidden(): void
    {
        Post::factory()->create(['slug' => 'later', 'title' => 'Future post', 'published_at' => now()->addDay()]);

        $this->get('/en/blog')->assertDontSee('Future post');
        $this->get('/en/blog/later')->assertNotFound();
    }

    public function test_search(): void
    {
        $this->get('/en/blog/search?s=squid')->assertOk()->assertSee('Hello squids');
        $this->get('/en/blog/search?s=zzz')->assertOk()->assertDontSee('Hello squids');
        $this->get('/es/blog/search?s=squid')
            ->assertOk()
            ->assertSee('action="'.url('/es/blog/search').'"', false);
        $this->get('/blog/search?s=squid')->assertRedirect();
    }

    public function test_pagination_links_older_posts(): void
    {
        Post::factory()->count(12)->create();

        $this->get('/en/blog')->assertOk()->assertSee('?page=2', false);
        $this->get('/en/blog?page=2')->assertOk();
    }

    public function test_rss_feed(): void
    {
        $this->get('/es/blog/feed')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<language>es</language>', false)
            ->assertSee(url('/es/blog/hola-calamares'), false)
            ->assertDontSee('Hello squids');

        $this->get('/blog/feed')->assertRedirect();
    }

    public function test_unpublished_posts_are_not_in_the_feed(): void
    {
        $this->en->update(['published_at' => null]);

        $this->get('/en/blog/feed')->assertOk()->assertDontSee('Hello squids');
    }

    public function test_language_switcher_keeps_the_page(): void
    {
        $this->from('/en/blog')->get('/locale/es')->assertRedirect('/es/blog');
        $this->from('/es/blog/hola-calamares?x=1')->get('/locale/en')->assertRedirect('/en/blog/hola-calamares?x=1');
        // …which then lands on the English translation.
        $this->get('/en/blog/hola-calamares')->assertRedirect('/en/blog/hello-squids');
        $this->from('/es')->get('/locale/en')->assertRedirect('/en');
    }

    public function test_language_switcher_keeps_pages_that_have_no_localized_version(): void
    {
        $this->from('/login')->get('/locale/es')->assertRedirect('/login');
    }
}
