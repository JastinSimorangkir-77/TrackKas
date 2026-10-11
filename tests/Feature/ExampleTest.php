<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The home page sends guests to the login screen.
     */
    public function test_guests_are_redirected_to_the_login_screen(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    /**
     * The home page sends authenticated users to the dashboard.
     */
    public function test_authenticated_users_are_redirected_to_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }
}
