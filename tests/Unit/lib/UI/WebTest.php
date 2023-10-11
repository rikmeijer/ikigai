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
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 200 OK', 'Content-Type: text/html']);
        $body = $this->expectBodySent('<!DOCTYPE html></html>');
        $response($headers, $body);
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
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 200 OK', 'Content-Type: text/html']);
        $body = $this->expectBodySent('<!DOCTYPE html></html>');
        $response($headers, $body);
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
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 404 File Not Found', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('');
        $response($headers, $body);
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
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 405 Method Not Allowed', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('');
        $response($headers, $body);
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
                        'text/html' => fn(callable $status) => $status('text/html', '200 OK', '<!DOCTYPE html></html>')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 406 Not Acceptable', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('');
        $response($headers, $body);
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'application/json'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'application/json' => fn(callable $status) => $status('200 OK', 'Hello World')
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent(['HTTP/2 200 OK', 'Content-Type: application/json']);
        $body = $this->expectBodySent('Hello World');
        $response($headers, $body);
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
        
        $headers = $this->expectHeadersSent([
            'HTTP/2 200 OK',
            'Content-Type: text/plain',
        ]);
        $body = $this->expectBodySent('Hello World');
        $response($headers, $body);
    }
}
