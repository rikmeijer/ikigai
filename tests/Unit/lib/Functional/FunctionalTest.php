<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\Functional;

use rikmeijer\purposeplan\lib\Functional\Functional;

class FunctionalTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_partial_left() {
        $fn = fn($a, $b) => $a * $b;
        
        $fn_partial = Functional::partial_left($fn, 5);
        
        $this->assertEquals(25, $fn_partial(5));
    }
    
    public function test_partial_right() {
        $fn = fn($a, $b) => $a / $b;
        
        $fn_partial = Functional::partial_right($fn, 5);
        
        $this->assertEquals(1, $fn_partial(5));
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
    
    
    public function test_compose() {
        
        $composed = Functional::compose(
            fn($value) => $value. 'a',
            fn($value) => $value . 'b'
        );
        
        $this->assertEquals('gab', $composed('g'));
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
    
    public function test_eachWhenNotEmpty() {
        $each = Functional::each(fn($value, $key) => $this->assertEquals(1, $value), fn() => $this->assertFalse(true));
        
        $each([1]);
    }
    
    public function test_eachWhenEmpty() {
        $each = Functional::each(fn($value, $key) => $this->assertFalse(true), Functional::nothing());
        $each([]);
    }

    public function test_firstWhenNotEmpty() {
        $this->assertEquals(2, Functional::first(fn(int $value) => $value * 2)([1,2,3]));
    }
    
    public function test_firstWhenEmpty() {
        Functional::first(fn($value) => $this->assertFalse(true));
    }
    public function test_empty() {
        $this->assertTrue(Functional::empty([]));
        $this->assertFalse(Functional::empty([0]));
    }
    public function test_populated() {
        $this->assertFalse(Functional::populated([]));
        $this->assertTrue(Functional::populated([0]));
    }
    
    public function test_intersect() {
        $this->assertEquals(['a' => 1, 'c' => 3, 'e' =>5], Functional::intersect(['a' => 1, 'b' => 2, 'c' => 3, 'd'=>4, 'e' => 5])([1,3,5,6,7,8]));
    }
    
    public function test_filter() {
        $filter = Functional::filter(fn($v, $k) => $v === 1);
        $this->assertEquals([1], $filter([1,2,3]));
    }
    
    public function test_nothing() {
        $this->assertNull(Functional::nothing()());
        
    }
}
