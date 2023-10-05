<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\Tests\Unit;

use \rikmeijer\purposeplan\domain\Mood\Log;
use \rikmeijer\purposeplan\domain\Mood;

class MoodLogTest extends \rikmeijer\purposeplan\Tests\TestCase {
    

    public function test_AMoodLogIsACollectionOfMoodsPerDate(): void
    {
        $log = new Log(
                new Mood('Happy'),
                new Mood('Happy'),
                new Mood('Sad')
        );
        $this->assertEquals('Sad', $log->moods[2]->description);
    }
}
