<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    
    public function test_entry(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
            ]);
        $headers = $this->expectHeadersSent(['HTTP/1.1 200 OK', 'Content-Type: text/html']);
        $body = $this->expectBodySent('<!DOCTYPE html></html>');
        $response($headers, $body);
    }
    
    
    public function test_entryChildResource(): void
    {
        $method = uniqid();
        $path = '/test/fubar';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 200 OK', 'Content-Type: text/html']);
        $body = $this->expectBodySent('<!DOCTYPE html></html>');
        $response($headers, $body);
    }
    
    public function test_entryMultipleChildrenResource(): void
    {
        $method = uniqid();
        $path = '/test/fuber';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 200 OK', 'Content-Type: text/html']);
        $body = $this->expectBodySent('<!DOCTYPE html></html>');
        $response($headers, $body);
    }
    
    public function test_entryMissingResourceResultsIn404(): void
    {
        $method = uniqid();
        $path = '/notexistant';
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 404 File Not Found', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('File Not Found');
        $response($headers, $body);
    }
    
    
    public function test_entryUnsupportedRequestMethodResultsIn405(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 405 Method Not Allowed', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('Method Not Allowed');
        $response($headers, $body);
    }
    
    public function test_entryMismatchInAcceptedContentTypeResultsIn406(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/plain'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/1.1 406 Not Acceptable', 'Content-Type: text/plain']);
        $body = $this->expectBodySent('Not Acceptable');
        $response($headers, $body);
    }
    
    public function test_entryAcceptingApplicationJson(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.json.php', 'Hello World');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'application/json'
        ]);
        
        $headers = $this->expectHeadersSent(['HTTP/2 200 OK', 'Content-Type: application/json']);
        $body = $this->expectBodySent('Hello World');
        $response($headers, $body);
    }
    
    public function test_entryAcceptingRelativeQualities(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.txt', 'Hello World');
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ]);
        
        $headers = $this->expectHeadersSent([
            'HTTP/2 200 OK',
            'Content-Type: text/plain',
        ]);
        $body = $this->expectBodySent('Hello World');
        $response($headers, $body);
    }
    
    public function test_entryRenderTemplate(): void
    {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.txt', '<block name="content" />');
        $this->prepareLogic($path, "[
                            'content' => fn() => 'Hello Universe'
                        ]");
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ]);
        
        $headers = $this->expectHeadersSent([
            'HTTP/2 200 OK',
            'Content-Type: text/plain',
        ]);
        $body = $this->expectBodySent('Hello Universe');
        $response($headers, $body);
    }
    
    
    public function test_entryRenderRootTemplate(): void
    {
        $method = uniqid();
        $path = '/';
        $this->prepareTemplate($path, $method . '.txt', '<block name="content" />');
        $this->prepareLogic($path, "[
                            'content' => fn() => 'Hello Universe'
                        ]");
        
        $response = Web::entry([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ]);
        
        $headers = $this->expectHeadersSent([
            'HTTP/2 200 OK',
            'Content-Type: text/plain',
        ]);
        $body = $this->expectBodySent('Hello Universe');
        $response($headers, $body);
    }
}
