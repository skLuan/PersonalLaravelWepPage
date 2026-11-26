<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'posts' => ['/admin/posts'],
            'new post' => ['/admin/posts/create'],
            'categories' => ['/admin/categories'],
            'projects' => ['/admin/projects'],
            'new project' => ['/admin/projects/create'],
            'skills' => ['/admin/skills'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admins_can_open_admin_pages(string $url): void
    {
        $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
    }

    #[DataProvider('adminPages')]
    public function test_guests_are_sent_to_the_login(string $url): void
    {
        $this->get($url)->assertRedirect('/admin/login');
    }

    public function test_non_admin_users_are_refused(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/posts')->assertForbidden();
    }

    public function test_admins_can_open_edit_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $post = Post::factory()->create();
        Post::factory()->translationOf($post, 'es')->create();
        $category = Category::factory()->create();
        $project = Project::create([
            'title' => 'Site', 'short_description' => 'x', 'image_path' => 'https://example.com/a.png', 'body' => 'x',
        ]);

        $this->actingAs($admin);
        $this->get("/admin/posts/{$post->id}/edit")->assertOk()->assertSee($post->title);
        $this->get("/admin/categories/{$category->id}/edit")->assertOk();
        $this->get("/admin/projects/{$project->slug}/edit")->assertOk(); // Project routes by slug
    }

    public function test_old_admin_urls_redirect_to_the_panel(): void
    {
        $this->get('/projects')->assertStatus(301)->assertRedirect('/admin/projects');
        $this->get('/projects/create')->assertRedirect('/admin/projects');
        $this->get('/blog_admin')->assertRedirect('/admin/posts');
        $this->get('/blog_admin/add_post')->assertRedirect('/admin/posts');
    }
}
