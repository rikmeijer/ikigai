<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\HTTP;

class MessageTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_HasStatusCode(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message(200, []);
        $this->assertEquals(200, $message->code);
        $this->assertPropertyIsReadOnly($message, 'code');
    }
    public function test_HasHeaders(): void
    {
        $message = new \rikmeijer\purposeplan\lib\HTTP\Message(200, ['Host' => 'example.com']);
        $this->assertEquals('example.com', $message->headers['Host']);
        $this->assertPropertyIsReadOnly($message, 'headers');
    }
}
