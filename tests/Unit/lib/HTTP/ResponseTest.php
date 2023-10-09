<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

use \rikmeijer\purposeplan\lib\HTTP\Response;
use \rikmeijer\purposeplan\lib\HTTP\Message;

class ResponseTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_send(): void {
        $response = new \rikmeijer\purposeplan\lib\HTTP\Response('HTTP/2 200 OK', new Message([
            'Content-Type: application/json'
            ], '{"Hello" : "World"}'
        ));
        
        
        $sent = null;
        $headers_list = array();
        Response::send($response, function(string $header) use (&$headers_list) { $headers_list[] = $header; }, function(string $body) use (&$sent) { $sent = $body; });
        
        $this->assertEquals('{"Hello" : "World"}', $sent);
        $this->assertCount(2, $headers_list);
        $this->assertContains('HTTP/2 200 OK', $headers_list);
        $this->assertContains('Content-Type: application/json', $headers_list);
    }
}
