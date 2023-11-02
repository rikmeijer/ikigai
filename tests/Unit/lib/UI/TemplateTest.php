<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use rikmeijer\purposeplan\lib\UI\Template;

class TemplateTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    
    
    public function test_replaceBlockElement(): void
    {
        $this->assertEquals('<html>Hello World</html>', Template::render('<html><block name="world" /></html>', fn(string $identifier) => match($identifier) {'world' => 'Hello World'}));
    }
    
    public function test_replaceBlockElements(): void
    {
        $this->assertEquals('<html>Hello WorldHello Universe</html>', Template::render('<html><block name="world" /><block name="universe" /></html>', fn(string $identifier) => match($identifier) {
            'world' => 'Hello World',
            'universe' => 'Hello Universe'
        }));
    }
    
    
    public function test_selectByAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', '<html>Hello World</html>');
        $this->prepareTemplate('/', $method . '.txt', 'Hello World');
        
        Template::negotiate(['text/html', 'text/plain'], Template::path('/'), $method, fn(callable $contents) => $contents(function(string $type, string $body) {
            $this->assertEquals('text/html', $type);
            $this->assertEquals('<html>Hello World</html>', $body);
        }), fn() => false, fn() => false, fn() => false);
    }
    
    
    public function test_selectByMissingTemplateDirectory(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', 'Hello World');
        
        Template::negotiate(['text/html'], Template::path('/blabla'), 'post', fn(callable $contents) => $this->assertNull(true), fn() => $this->assertTrue(true), fn() => $this->assertFalse(true), fn() => $this->assertFalse(true));
    }
    
    public function test_selectByMissingTemplateIdentifier(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', 'Hello World');
        
        Template::negotiate(['text/html'], Template::path('/'), 'post', fn(callable $contents) => $this->assertNull(true), fn() => $this->assertFalse(true), fn() => $this->assertTrue(true), fn() => $this->assertFalse(true));
    }
    
    public function test_selectByUnselectableAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.txt', 'Hello World');
        
        Template::negotiate(['text/html'], Template::path('/'), $method, fn(callable $contents) => $this->assertFalse(true), fn() => $this->assertFalse(true), fn() => $this->assertNull(true), fn() => $this->assertTrue(true));
    }
}
