<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    
    public function test_entry(): void
    {
        $template_identifier = $this->prepareTemplate('get', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
            ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                    $get(function(callable $negotiate) {
                        $negotiate([
                            'text/plain' => fn(callable $template) => $template('missing'),
                            'text/html' => fn(callable $template) => $template()
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
        $template_identifier = $this->prepareTemplate('get', '<!DOCTYPE html></html>');
        
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
                            'text/plain' => fn(callable $template) => $template('missing'),
                            'text/html' => fn(callable $template) => $template()
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
        $body = $this->expectBodySent('File Not Found');
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
        $body = $this->expectBodySent('Method Not Allowed');
        $response($headers, $body);
    }
    
    public function test_entryMismatchInAcceptedContentTypeResultsIn406(): void
    {
        $template_identifier = $this->prepareTemplate('get', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/plain'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/html' => fn(callable $template) => $template()
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 406 Not Acceptable', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('Not Acceptable');
        $response($headers, $body);
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $template_identifier = $this->prepareTemplate('get', 'Hello World');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'application/json'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'application/json' => fn(callable $template) => $template()
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
        $template_identifier = $this->prepareTemplate('get', 'Hello World');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/plain' => fn(callable $template) => $template()
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
    
    public function test_entryRenderTemplate(): void
    {
        $template_identifier = $this->prepareTemplate('get', '<block name="content" />');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ], function(callable $route) {
            $route('test', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
                $get(function(callable $negotiate) {
                    $negotiate([
                        'text/plain' => fn(callable $template) => $template(...[
                            'content' => fn() => 'Hello Universe'
                        ])
                    ]);
                });
            });
        });
        
        $headers = $this->expectHeadersSent([
            'HTTP/2 200 OK',
            'Content-Type: text/plain',
        ]);
        $body = $this->expectBodySent('Hello Universe');
        $response($headers, $body);
    }
}
