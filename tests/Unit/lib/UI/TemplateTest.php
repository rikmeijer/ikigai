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
        $this->prepareTemplates('get', [
            'text/html' => '<html>Hello World</html>',
            'text/plain' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html', 'text/plain'], 'get', fn(string $type, string $contents) => $this->assertEquals('<html>Hello World</html>', Template::render($contents)), fn() => false, fn() => false);
    }
    
    public function test_selectByMissingTemplateIdentifier(): void
    {
        $this->prepareTemplates('get', [
            'text/plain' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html'], 'post', fn(string $type, string $contents) => $this->assertEquals('flase', Template::render($contents)), fn() => $this->assertTrue(true), fn() => trigger_error('WRONG'));
    }
    
    public function test_selectByUnselectableAcceptedType(): void
    {
        $this->prepareTemplates('get', [
            'text/plain' => 'Hello World'
        ]);
        
        Template::negotiate(['text/html'], 'get', fn(string $type, string $contents) => trigger_error('WRONG'), fn() => trigger_error('WRONG'), fn() => $this->assertTrue(true));
    }
}
