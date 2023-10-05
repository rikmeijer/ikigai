<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit\domain;

use \rikmeijer\purposeplan\domain\Mood\Log;
use \rikmeijer\purposeplan\domain\Mood;

class MoodLogTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {
    

    public function test_AMoodLogIsACollectionOfMoods(): void
    {
        $log = new Log(
                new Mood('Happy'),
                new Mood('Happy'),
                new Mood('Sad')
        );
        $this->assertEquals('Sad', $log->moods[2]->description);
    }
    public function test_AMoodLogIsReadOnly(): void
    {
        $log = new Log(
                new Mood('Happy'),
                new Mood('Happy'),
                new Mood('Sad')
        );
        $this->assertEquals('Sad', $log->moods[2]->description);
        $this->expectExceptionMessage('Cannot modify readonly property rikmeijer\purposeplan\domain\Mood\Log::$moods');
        $log->moods[] = new Mood('Happy');
    }
}
