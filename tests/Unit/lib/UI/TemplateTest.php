<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\UI;

use rikmeijer\purposeplan\lib\UI\Template;

class TemplateTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    
    
    public function test_replaceBlockElement(): void
    {
        $this->assertEquals('<html>Hello World</html>', Template::render('<html><block name="world" /></html>')(fn(string $identifier) => match($identifier) {'world' => 'Hello World'}));
    }
    
    public function test_replaceBlockElements(): void
    {
        $this->assertEquals('<html>Hello WorldHello Universe</html>', Template::render('<html><block name="world" /><block name="universe" /></html>')(fn(string $identifier) => match($identifier) {
            'world' => 'Hello World',
            'universe' => 'Hello Universe'
        }));
    }
    
    
    public function test_selectByAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', '<html>Hello World</html>');
        $this->prepareTemplate('/', $method . '.txt', 'Hello World');
        $directory = Template::path('/');
        
        Template::negotiate($directory, Template::filepath($directory, $method))(fn() => false)(\rikmeijer\purposeplan\lib\UI\Web::acceptableTypes('text/html,text/plain;q=0.0'), fn() => false)(function(string $type, string $body) {
            $this->assertEquals('text/html', $type);
            $this->assertEquals('<html>Hello World</html>', $body);
        }, fn() => false);
    }
    
    
    public function test_selectByMissingTemplateDirectory(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', 'Hello World');
        $directory = Template::path('/blabla');
        
        $negotiator =Template::negotiate(
                $directory, 
                Template::filepath($directory, 'post'))(fn() => $this->assertTrue(true));
    }
    
    public function test_selectByMissingTemplateIdentifier(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.html', 'Hello World');
        $directory = Template::path('/');
        
        Template::negotiate(
                $directory, 
                Template::filepath($directory, 'post'))
                (fn() => $this->assertFalse(true))
                (\rikmeijer\purposeplan\lib\UI\Web::acceptableTypes('text/html'), fn() => $this->assertTrue(true));
    }
    
    public function test_selectByUnselectableAcceptedType(): void
    {
        $method = uniqid();
        $this->prepareTemplate('/', $method . '.txt', 'Hello World');
        $directory = Template::path('/');
        
        Template::negotiate(
                $directory, 
                Template::filepath($directory, $method),)
                (fn() => $this->assertFalse(true))
                (\rikmeijer\purposeplan\lib\UI\Web::acceptableTypes('text/html'), fn() => $this->assertNull(true))
                (fn(callable $contents) => $this->assertFalse(true),
                fn() => $this->assertTrue(true));
    }
}
