<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertPropertyIsReadOnly(object $object, string $property, string $message = 'Property is writable')
    {
        $this->assertTrue((new \ReflectionProperty($object, $property))->isReadOnly(), $message);
    }
    
    public function expectHeadersSent(array $expectedHeaders) : callable {
        return function(?string $header = null) use (&$expectedHeaders) { 
            $this->assertEquals(array_shift($expectedHeaders), $header); 
        };
    }
    
    public function expectBodySent(string $expectedBody) : callable {
        return function(?string $body = null) use ($expectedBody) { 
            $this->assertEquals($expectedBody, $body); 
        };
    }
    public function expectBodySentCallback(callable $fn) : callable {
        return function(?string $body = null) use ($expectedBody) { 
            $this->assertTrue($fn($body)); 
        };
    }
    
    public function prepareTemplate(string $template_identifier, string $contents) {
        $_ENV['TEMPLATE_DIR'] = sys_get_temp_dir();
        file_put_contents($_ENV['TEMPLATE_DIR'] . DIRECTORY_SEPARATOR . $template_identifier, $contents);
        return $template_identifier; 
    }
    
    public function prepareTemplates(string $method, array $templateByContentType) {
        $_ENV['TEMPLATE_DIR'] = sys_get_temp_dir();
        return \rikmeijer\purposeplan\lib\Functional\Functional::map(fn(string $contents, string $contentType) => file_put_contents($_ENV['TEMPLATE_DIR'] . DIRECTORY_SEPARATOR . $method . '.' . \rikmeijer\purposeplan\lib\UI\Template::typeToExtension($contentType), $contents))($templateByContentType);
    }
}
