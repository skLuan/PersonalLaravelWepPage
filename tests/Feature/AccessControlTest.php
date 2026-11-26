<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_closed(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Mallory',
            'email' => 'mallory@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'mallory@example.com']);
    }

    public function test_guests_cannot_manage_projects(): void
    {
        foreach (['/projects', '/projects/create'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }

        $this->post('/projects', ['title' => 'Spam'])->assertRedirect('/login');
        $this->assertSame(0, Project::count());
    }

    public function test_logged_in_users_can_open_the_project_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/projects/create')
            ->assertOk();
    }

    public function test_home_aliases_redirect_to_the_home_page(): void
    {
        $this->get('/home')->assertRedirect('/');
        $this->get('/es/home')->assertRedirect('/es');
    }

    public function test_portfolio_only_exposes_index(): void
    {
        $this->get('/portfolio')->assertOk();
        $this->get('/es/portfolio')->assertOk();
        $this->get('/es/portfolio/create')->assertNotFound();
    }

    public function test_route_names_are_unique(): void
    {
        // Duplicate names make `php artisan route:cache` (and so `optimize`) fail.
        $names = collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter();

        $this->assertSame([], $names->duplicates()->values()->all());
    }

    public function test_user_create_command(): void
    {
        $this->artisan('user:create', ['email' => 'admin@example.com', '--password' => 'secret-password'])
            ->assertSuccessful();

        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertSame('admin', $user->name);
        $this->assertNotNull($user->email_verified_at);

        // Duplicate email is rejected
        $this->artisan('user:create', ['email' => 'admin@example.com', '--password' => 'secret-password'])
            ->assertFailed();
    }
}
