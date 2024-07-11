<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    public function test_the_welcome_screen_redirects_to_dashboard(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('dashboard');
    }
}
