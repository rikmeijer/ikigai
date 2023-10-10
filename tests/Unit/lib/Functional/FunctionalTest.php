<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\lib\Functional;

use rikmeijer\purposeplan\lib\Functional\Functional;

class FunctionalTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_partial_left() {
        $fn = fn($a, $b) => $a * $b;
        
        $fn_partial = Functional::partial_left($fn, 5);
        
        $this->assertEquals(25, $fn_partial(5));
    }
    
}
