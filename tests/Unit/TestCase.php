<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertPropertyIsReadOnly(object $object, string $property, string $message = 'Property is writable')
    {
        $this->assertTrue((new \ReflectionProperty($object, $property))->isReadOnly(), $message);
    }
    
    public function expectHeadersSent() : callable {
        $headers_list = [];
        return function(?string $header = null) use (&$headers_list) { 
            if (is_null($header)) { 
                return $headers_list; 
            } 
            $headers_list[] = $header; 
        };
    }
    
    public function expectBodySent() : callable {
        $sent = null;
        return function(?string $body = null) use (&$sent) { 
            if (is_null($body)) { 
                return $sent; 
            } 
            $sent = $body; 
        };
    }
}
