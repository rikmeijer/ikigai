<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    public function test_the_welcome_screen_shows_version(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSeeText("Ikigai v0.1");
    }
    
    public function test_the_welcome_screen_shows_name_and_tagline(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSeeText("Ikigai");
        $response->assertSeeText("Planning your life goals");
    }
    
    
    public function test_the_welcome_screen_asks_what_is_your_dream(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSeeText("What is your dream?");
    }
}
