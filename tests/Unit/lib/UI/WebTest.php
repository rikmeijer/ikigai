<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use \rikmeijer\purposeplan\lib\UI\Web;

class WebTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {


    public function test_entry(): void
    {
        $response = Web::entry();
        
        $headers = $this->expectHeadersSent();
        $body = $this->expectBodySent();
        
        $response($headers, $body);
        
        $this->assertEquals('Hello World', $body());
        $this->assertCount(2, $headers());
        $this->assertContains('HTTP/2 200 OK', $headers());
        $this->assertContains('Content-Type: text/html', $headers());
    }
}
