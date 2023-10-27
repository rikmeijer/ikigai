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
    
    private function setTemplateDir() {
        $_ENV['TEMPLATE_DIR'] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ikigai';
        return $_ENV['TEMPLATE_DIR'];
    }
    
    public function prepareLogic(string $path, string $logic) {
        $logic_path = $this->setTemplateDir() . ($path === '/' ? '' : $path) . '.php';
        $this->assertGreaterThan(0, file_put_contents($logic_path, '<?php return fn() => ' . $logic . ';'), 'Nothing written to `'.$logic_path.'`');
        $this->assertFileExists($logic_path);
    }
    
    public function prepareTemplate(string $path, string $template_identifier, string $contents) {
        
        $template_path = $this->setTemplateDir() . $path;
        if (is_dir($template_path) === false) {
            $this->assertTrue(mkdir($template_path, recursive:true), 'Unable to create template directory `'.$_ENV['TEMPLATE_DIR'] . $path.'`');
        }
        
        $template_file = $template_path . DIRECTORY_SEPARATOR . $template_identifier;
        file_put_contents($template_file, $contents);
        $this->assertFileExists($template_file);
        
        return $template_identifier; 
    }
    
    public function prepareTemplates(string $method, array $templateByContentType) {
        $_ENV['TEMPLATE_DIR'] = sys_get_temp_dir();
        return \rikmeijer\purposeplan\lib\Functional\Functional::map(fn(string $contents, string $contentType) => file_put_contents($_ENV['TEMPLATE_DIR'] . DIRECTORY_SEPARATOR . $method . '.' . \rikmeijer\purposeplan\lib\UI\Template::typeToExtension($contentType), $contents))($templateByContentType);
    }
}
