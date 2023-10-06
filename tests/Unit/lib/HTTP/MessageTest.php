<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

use \rikmeijer\purposeplan\lib\HTTP\Message;

class MessageTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {


    public function test_HasHeaders(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message(['Host: example.com']);
        $this->assertEquals('Host: example.com', $message->headers[0]);
        $this->assertPropertyIsReadOnly($message, 'headers');
    }
    public function test_HasABody(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message([], '<html>Hello World</html>');
        $this->assertEquals('<html>Hello World</html>', $message->body);
        $this->assertPropertyIsReadOnly($message, 'body');
    }
    
    /**
     * @runInSeparateProcess
     */
    public function test_send(): void {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message([
            'Content-Type: application/json'
            ], '{"Hello" : "World"}');
        
        
        ob_start();
        Message::send($message);
        
        $headers_list = xdebug_get_headers();
        header_remove();
        
        $this->assertEquals('{"Hello" : "World"}', ob_get_clean());
        $this->assertCount(1, $headers_list);
        $this->assertContains('Content-Type: application/json', $headers_list);
    }
}

