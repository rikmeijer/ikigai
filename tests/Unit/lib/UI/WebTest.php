<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {


    public function test_entry(): void
    {
        $entry = Web::entry([
            'HTTP_ACCEPT' => 'text/html'
        ]);
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $response = $entry(function(array $contentTypes, callable $status, callable $headers) {
            $status('200 OK');
            $headers('Content-Type: ' . key($contentTypes));
            return '<!DOCTYPE html></html>';
        });
        $response($headers, $body);
        
        $this->assertTrue(str_starts_with($body(), "<!DOCTYPE html>"));
        $this->assertTrue(str_ends_with($body(), "</html>"));
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: text/html', $headers());
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $entry = Web::entry([
            'HTTP_ACCEPT' => 'appplication/json'
        ]);
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $response = $entry(function(array $contentTypes, callable $status, callable $headers) {
            $status('200 OK');
            $headers('Content-Type: ' . key($contentTypes));
            return 'Hello World';
        });
        $response($headers, $body);
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: appplication/json', $headers());
    }
    
    public function test_entryAcceptingRelativeQualities(): void
    {
        $entry = Web::entry([
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ]);
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $response = $entry(function(array $contentTypes, callable $status, callable $headers) {
            $status('200 OK');
            $headers('Content-Type: ' . key($contentTypes));
            return 'Hello World';
        });
        $response($headers, $body);
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: text/plain', $headers());
    }
}
