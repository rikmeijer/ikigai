<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\domain;

final readonly class Mood {

    public function __construct(
            public string $description,
            public \DateTimeImmutable $timestamp = new \DateTimeImmutable()
    ) {
        
    }
}
