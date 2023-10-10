<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\Functional;

use rikmeijer\purposeplan\lib\Functional\Functional;

class FunctionalTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_partial_left() {
        $fn = fn($a, $b) => $a * $b;
        
        $fn_partial = Functional::partial_left($fn, 5);
        
        $this->assertEquals(25, $fn_partial(5));
    }
    
    
    public function test_partial_left_too_much_arguments() {
        $fn = fn($a, $b) => $a * $b;
        
        $fn_partial = Functional::partial_left($fn, 5);
        
        $this->assertEquals(25, $fn_partial(5, 6));
    }
    
    public function test_map() {
        $fn = fn($value, $key) => [$key => $key . ': ' . $value];
        
        $map = Functional::map(['a', 'b', 'c'], $fn);
        
        $this->assertEquals(['0: a', '1: b', '2: c'], $map);
    }
    
}
