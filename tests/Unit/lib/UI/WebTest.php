<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_entry(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/plain' => fn(callable $status) => $status('200 OK', 'Hello World'),
                        'text/html' => fn(callable $status) => $status('200 OK', '<!DOCTYPE html></html>')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertTrue(str_starts_with($body(), "<!DOCTYPE html>"));
        $this->assertTrue(str_ends_with($body(), "</html>"));
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/1.1 200 OK', $headers());
        $this->assertContains('Content-Type: text/html', $headers());
    }
    
    
    public function test_entryChildResource(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test/fubar',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $child('fubar', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                    $get(function(callable $negotiate) {
                        $negotiate([
                            'text/plain' => fn(callable $status) => $status('200 OK', 'Hello World'),
                            'text/html' => fn(callable $status) => $status('200 OK', '<!DOCTYPE html></html>')
                        ]);
                    });
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertTrue(str_starts_with($body(), "<!DOCTYPE html>"));
        $this->assertTrue(str_ends_with($body(), "</html>"));
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/1.1 200 OK', $headers());
        $this->assertContains('Content-Type: text/html', $headers());
    }
    
    public function test_entryMissingResourceResultsIn404(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/notexistant',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertEquals('HTTP/1.1 404 File Not Found', $headers()[0]);
    }
    
    
    public function test_entryUnsupportedRequestMethodResultsIn405(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([]);
                });
            });

        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertEquals('HTTP/1.1 405 Method Not Allowed', $headers()[0]);
    }
    
    public function test_entryMismatchInAcceptedContentTypeResultsIn406(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/plain'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/html' => fn(callable $status) => $status('200 OK', '<!DOCTYPE html></html>')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertContains('HTTP/1.1 406 Not Acceptable', $headers());
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'appplication/json'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'appplication/json' => fn(callable $status) => $status('200 OK', 'Hello World')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: appplication/json', $headers());
    }
    
    public function test_entryAcceptingRelativeQualities(): void
    {
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/plain' => fn(callable $status) => $status('200 OK', 'Hello World')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        $response($headers, $body);
        
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: text/plain', $headers());
    }
}
