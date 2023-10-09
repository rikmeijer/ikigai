<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Web {
    
    static function entry(array $server) : callable {
        
        return function(callable $headers, callable $body) use ($server) {
            $headers('HTTP/2 200 OK');
            $headers('Content-Type: ' . $server['HTTP_ACCEPT']);
            $body('Hello World');
        };
    }
    
}
