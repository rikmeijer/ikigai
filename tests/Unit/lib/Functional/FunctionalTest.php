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
        $fn = fn($value, $key) => $key . ': ' . $value;
        
        $map = Functional::map($fn);
        
        $this->assertEquals(['0: a', '1: b', '2: c'], $map(['a', 'b', 'c']));
    }
    
    public function test_reduce() {
        $fn = fn($carry, $value) => $carry .= $value;
        
        $reduce = Functional::reduce($fn);
        
        $this->assertEquals('abc', $reduce(['a', 'b', 'c']));
    }
    
    
    public function test_find() {
        $fn = fn($value) => $value === 'b';
        
        $reduce = Functional::find($fn, fn($value, $key) => $key, Functional::nothing());
        
        $this->assertEquals(1, $reduce(['a', 'b', 'c']));
    }
    
    
    public function test_findNonExistent() {
        $fn = fn($value) => $value === 'd';
        
        $reduce = Functional::find($fn, fn($value, $key) => $key, fn() => 'Not Found');
        
        $this->assertEquals('Not Found', $reduce(['a', 'b', 'c']));
    }
    
    public function test_if_else() {
        $ifelse = Functional::if_else(fn($value) => $value === true, fn() => 'True', fn() => 'False');
        $this->assertEquals('True', $ifelse(true));
        
    }
    
    
    public function test_nothing() {
        $this->assertNull(Functional::nothing()());
        
    }
}
