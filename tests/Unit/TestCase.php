<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit;


abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function assertPropertyIsReadOnly(object $object, string $property, string $message = 'Property is writable')
    {
        $this->assertTrue((new \ReflectionProperty($object, $property))->isReadOnly(), $message);
    }
    
}
