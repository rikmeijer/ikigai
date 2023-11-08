<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Feature;

class IndexTest extends \rikmeijer\purposeplan\Tests\Feature\TestCase {
    
    public function testIndexPageContainsHTMLtags() {
        $this->open('/', 'text/html');
        
        $this->assertResponseCode('200');
        $this->assertContentType('text/html');
        $this->assertBodyContains('<!DOCTYPE html>');
        $this->assertBodyContains('<html>');
        $this->assertBodyContains('</html>');
    }
    
    public function testIndexPageContainsHTMLtags_WhenUsingRealAcceptHeader() {
        $this->open('/', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8');
        
        $this->assertResponseCode('200');
        $this->assertContentType('text/html');
        $this->assertBodyContains('<!DOCTYPE html>');
        $this->assertBodyContains('<html>');
        $this->assertBodyContains('</html>');
    }
    
    
    public function testIndexPageContainsApplicationVersion() {
        $this->open('/', 'text/html');
        
        $this->assertResponseCode('200');
        $this->assertContentType('text/html');
        $this->assertBodyContains(file_get_contents(dirname(dirname(__DIR__)) . '/VERSION'));
    }
}
