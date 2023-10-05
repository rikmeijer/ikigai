<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\HTTP;

class Message {
    
    public function __construct(
            public int $code
    ) {}
    
}
