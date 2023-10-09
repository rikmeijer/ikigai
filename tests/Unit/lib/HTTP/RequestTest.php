<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

use \rikmeijer\purposeplan\lib\HTTP\Request;

class RequestTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_fromCurrent(): void {
        
        $request = Request::fromCurrent([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/index.php',
            'SERVER_PROTOCOL' => 'HTTP/2',
            'HTTP_HOST' => 'example.com',
            'HTTP_CONTENT_TYPE' => 'application\json'
        ]);
        
        $this->assertEquals('GET', $request->method);
        $this->assertEquals('/index.php', $request->path);
        $this->assertEquals('HTTP/2', $request->protocol);
        $this->assertContains('Host: example.com', $request->message->headers);
        $this->assertContains('Content-Type: application\json', $request->message->headers);
        
        $this->assertPropertyIsReadOnly($request, 'method');
        $this->assertPropertyIsReadOnly($request, 'path');
        $this->assertPropertyIsReadOnly($request, 'protocol');
        $this->assertPropertyIsReadOnly($request, 'message');
    }
}
