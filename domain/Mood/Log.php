<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\domain\Mood;

use \rikmeijer\purposeplan\domain\Mood;

final class Log {
    
    /**
     * 
     * @var Mood[]
     */
    public array $moods;
    
    public function __construct(Mood ...$mood) {
        $this->moods = $mood;
    }
}
