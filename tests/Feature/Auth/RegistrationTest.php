<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0712345678',
            'password' => 'New-password123!',
            'confirm_password' => 'New-password123!',
            'accept_terms' => '1',
            'not_a_robot' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/student');
    }
}
