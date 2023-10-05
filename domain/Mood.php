<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\domain;

readonly class Mood {

    public function __construct(
            public string $description,
            public \DateTime $timestamp = new \DateTime()
    ) {
        
    }
}
