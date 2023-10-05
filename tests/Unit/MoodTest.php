<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit;

class MoodTest extends \rikmeijer\purposeplan\Tests\TestCase
{
    
    public function test_MoodHasDescription(): void
    {
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy');
        $this->assertEquals('Happy', $mood->description);
    }
    public function test_MoodDescriptionIsReadOnly(): void
    {
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy');
        $this->expectExceptionMessage('Cannot modify readonly property rikmeijer\purposeplan\domain\Mood::$description');
        $mood->description = 'Sad';
    }
}
