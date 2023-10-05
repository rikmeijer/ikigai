<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

class MessageTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {


    public function test_HasHeaders(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message(['Host' => 'example.com']);
        $this->assertEquals('example.com', $message->headers['Host']);
        $this->assertPropertyIsReadOnly($message, 'headers');
    }
    public function test_HasABody(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message(['Host' => 'example.com'], '<html>Hello World</html>');
        $this->assertEquals('<html>Hello World</html>', $message->body);
        $this->assertPropertyIsReadOnly($message, 'body');
    }
}
