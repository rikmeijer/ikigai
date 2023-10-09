<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Web {
    
    static function entry() : callable {
        
        return function(callable $headers, callable $body) {
            $headers('HTTP/2 200 OK');
            $headers('Content-Type: text/html');
            $body('Hello World');
        };
    }
    
}
