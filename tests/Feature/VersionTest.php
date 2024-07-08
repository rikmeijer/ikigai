<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VersionTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_version_in_html_head(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSeeText("Ikigai v0.1");
    }
}
