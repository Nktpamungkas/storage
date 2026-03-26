<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_are_redirected_to_dashboard_from_root(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
