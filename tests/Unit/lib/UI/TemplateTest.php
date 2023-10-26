<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use rikmeijer\purposeplan\lib\UI\Template;

class TemplateTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    
    
    public function test_replaceBlockElement(): void
    {
        $this->assertEquals('<html>Hello World</html>', Template::render('<html><block name="world" /></html>', ...['world' => fn() => 'Hello World']));
    }
    
    public function test_replaceBlockElements(): void
    {
        $this->assertEquals('<html>Hello WorldHello Universe</html>', Template::render('<html><block name="world" /><block name="universe" /></html>', ...[
            'world' => fn() => 'Hello World',
            'universe' => fn() => 'Hello Universe'
        ]));
    }
    
    
    public function test_selectByAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplates($method, [
            'text/html' => '<html>Hello World</html>',
            'text/plain' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html', 'text/plain'], '/', $method, fn(string $type, callable $contents) => $this->assertEquals('<html>Hello World</html>', $contents()), fn() => false, fn() => false, fn() => false);
    }
    
    
    public function test_selectByMissingTemplateDirectory(): void
    {
        $method = uniqid();
        $this->prepareTemplates($method, [
            'text/html' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html'], '/blabla', 'post', fn(string $type, string $contents) => $this->assertNull(true), fn() => $this->assertTrue(true), fn() => $this->assertFalse(true), fn() => $this->assertFalse(true));
    }
    
    public function test_selectByMissingTemplateIdentifier(): void
    {
        $method = uniqid();
        $this->prepareTemplates($method, [
            'text/html' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html'], '/', 'post', fn(string $type, string $contents) => $this->assertNull(true), fn() => $this->assertFalse(true), fn() => $this->assertTrue(true), fn() => $this->assertFalse(true));
    }
    
    public function test_selectByUnselectableAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplates($method, [
            'text/plain' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html'], '/', $method, fn(string $type, string $contents) => $this->assertFalse(true), fn() => $this->assertFalse(true), fn() => $this->assertNull(true), fn() => $this->assertTrue(true));
    }
}
