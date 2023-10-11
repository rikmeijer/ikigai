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
        
        
        $headers = $this->expectHeadersSent(['HTTP/2 200 OK', 'Content-Type: application/json']);
        $body = $this->expectBodySent('{"Hello" : "World"}');
        
        Response::send($response, $headers, $body);
    }
}
