<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Feature;

class IndexTest extends \rikmeijer\purposeplan\Tests\Feature\TestCase {
    
    public function testIndexPageContainsHTMLtags() {
        $this->open('/');
        
        $this->assertResponseCode('200');
        $this->assertContentType('text/html');
        $this->assertBodyContains('<!DOCTYPE html>');
        $this->assertBodyContains('<html>');
        $this->assertBodyContains('</html>');
    }
    
    
    public function testIndexPageContainsApplicationVersion() {
        $this->open('/');
        
        $this->assertResponseCode('200');
        $this->assertContentType('text/html');
        $this->assertBodyContains(file_get_contents(dirname(dirname(__DIR__)) . '/VERSION'));
    }
}
