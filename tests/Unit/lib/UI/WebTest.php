<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {


    public function test_entry(): void
    {
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $entry = Web::entry([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ], $headers, $body);
        
        $entry('text/plain')(fn(callable $status) => $status('200 OK', 'Hello World'), Web::skip());
        $entry('text/html')(fn(callable $status) => $status('200 OK', '<!DOCTYPE html></html>'), Web::notAcceptable());
        
        $this->assertTrue(str_starts_with($body(), "<!DOCTYPE html>"));
        $this->assertTrue(str_ends_with($body(), "</html>"));
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/1.1 200 OK', $headers());
        $this->assertContains('Content-Type: text/html', $headers());
    }
    
    public function test_entryMismatchInAcceptedContentTypeResultsIn406(): void
    {
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $entry = Web::entry([
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/plain'
        ], $headers, $body);
        
        $entry('text/html')(fn(callable $status) => $status('200 OK', '<!DOCTYPE html></html>'), Web::notAcceptable());
        
        $this->assertContains('HTTP/1.1 406 Not Acceptable', $headers());
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        
        $entry = Web::entry([
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'appplication/json'
        ], $headers, $body);
        
        $entry('appplication/json')(fn(callable $status) => $status('200 OK', 'Hello World'), Web::notAcceptable());
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: appplication/json', $headers());
    }
    
    public function test_entryAcceptingRelativeQualities(): void
    {
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $entry = Web::entry([
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ], $headers, $body);
        
        
        $entry('text/plain')(fn(callable $status) => $status('200 OK', 'Hello World'), Web::notAcceptable());

        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: text/plain', $headers());
    }
}
