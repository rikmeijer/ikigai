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
        $template_identifier = $this->prepareTemplates([
            'text/html' => '<html>Hello World</html>',
            'text/plain' => 'Hello World'
        ]);
        
        $this->assertEquals('<html>Hello World</html>', Template::render(Template::negotiate(['text/html', 'text/plain'], $template_identifier['text/html'], fn() => false)));
    }
    
    public function test_selectByUnselectableAcceptedType(): void
    {
        $template_identifier = $this->prepareTemplates([
            'text/plain' => 'Hello World'
        ]);
        
        $this->assertEquals('false', Template::render(Template::negotiate(['text/html'], $template_identifier['text/plain'], fn() => 'false')));
    }
}
