<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\tests\Unit\domain;

class MoodTest extends \rikmeijer\purposeplan\Tests\Unit\TestCase {

    public function test_MoodHasDescription(): void
    {
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy');
        $this->assertEquals('Happy', $mood->description);
        $this->assertPropertyIsReadOnly($mood, 'description');
    }
    
    public function test_MoodAcceptsATimestamp(): void
    {
        $timestamp = new \DateTimeImmutable();
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy', $timestamp);
        $this->assertEquals($timestamp, $mood->timestamp);
        $this->assertPropertyIsReadOnly($mood, 'timestamp');
    }
    public function test_MoodRequiresAnImmutableTimestamp(): void
    {
        $timestamp = new \DateTime();
        $this->expectExceptionMessageMatches('/^' . preg_quote('rikmeijer\\purposeplan\\domain\\Mood::__construct(): Argument #2 ($timestamp) must be of type DateTimeImmutable, DateTime given') . '/');
        $mood = new \rikmeijer\purposeplan\domain\Mood('Happy', $timestamp);
    }
}
