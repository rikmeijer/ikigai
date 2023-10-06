<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

use \rikmeijer\purposeplan\lib\HTTP\Request;

/**
 * Description of RequestTest
 *
 * @author rmeijer
 */
class RequestTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_fromCurrent(): void {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/index.php';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/2';

        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application\json';
        
        $request = Request::fromCurrent();
        
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
