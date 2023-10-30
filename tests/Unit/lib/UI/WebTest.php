<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_entry(): void {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/html'
        ]);

        $response($this->expectResponse('text/html', '200 OK', '<!DOCTYPE html></html>'));
    }

    public function test_entryChildResource(): void {
        $method = uniqid();
        $path = '/test/fubar';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/html'
        ]);

        $response($this->expectResponse('text/html', '200 OK', '<!DOCTYPE html></html>'));
    }

    public function test_entryMultipleChildrenResource(): void {
        $method = uniqid();
        $path = '/test/fuber';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/html'
        ]);

        $response($this->expectResponse('text/html', '200 OK', '<!DOCTYPE html></html>'));
    }

    public function test_entryMissingResourceResultsIn404(): void {
        $method = uniqid();
        $path = '/notexistant';

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/html'
        ]);

        $response($this->expectResponse('text/plain', '404 File Not Found', 'File Not Found'));
    }

    public function test_entryUnsupportedRequestMethodResultsIn405(): void {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');

        $response = Web::entry([
                    'REQUEST_METHOD' => 'POST',
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/html'
        ]);

        $response($this->expectResponse('text/plain', '405 Method Not Allowed', 'Method Not Allowed'));
    }

    public function test_entryMismatchInAcceptedContentTypeResultsIn406(): void {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.html', '<!DOCTYPE html></html>');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'HTTP_ACCEPT' => 'text/plain'
        ]);

        $response($this->expectResponse('text/plain', '406 Not Acceptable', 'Not Acceptable'));
    }

    public function test_entryAcceptingApplicationJson(): void {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.json.php', 'Hello World');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'SERVER_PROTOCOL' => 'HTTP/2',
                    'HTTP_ACCEPT' => 'application/json'
        ]);

        $response($this->expectResponse('application/json', '200 OK', 'Hello World'));
    }

    public function test_entryAcceptingRelativeQualities(): void {
        $method = uniqid();
        $path = '/test';
        $this->prepareTemplate($path, $method . '.txt', 'Hello World');

        $response = Web::entry([
                    'REQUEST_METHOD' => strtoupper($method),
                    'REQUEST_URI' => $path,
                    'SERVER_PROTOCOL' => 'HTTP/2',
                    'HTTP_ACCEPT' => 'text/plain, application/xhtml+xml, application/json;q=0.9, */*;q=0.8'
        ]);

        $response($this->expectResponse('text/plain', '200 OK', 'Hello World'));
    }

    public function test_entryRenderTemplate(): void {
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

        $response($this->expectResponse('text/plain', '200 OK', 'Hello Universe'));
    }

    public function test_entryRenderRootTemplate(): void {
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

        $response($this->expectResponse('text/plain', '200 OK', 'Hello Universe'));
    }
}
