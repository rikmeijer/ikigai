<?php

namespace rikmeijer\purposeplan\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MoodTest extends TestCase
{
    
    public function test_MoodHasDescription(): void
    {
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy');
        $this->assertEquals('Happy', $mood->description);
    }
}
