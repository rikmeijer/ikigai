<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Web {
    
    static function parseRelativeQuality(string $header_value) : array {
        $accepting_types = array_reduce(explode(',', $header_value), function ($res, $el) {
            list($l, $q) = array_merge(explode(';q=', $el), [1]); 
            $res[$l] = (float) $q; 
            return $res; 
        }, []);
        arsort($accepting_types);
        return $accepting_types;
    }
    
    static function entry(array $server) : callable {
        return function(callable $headers, callable $body) use ($server) {
            $headers('HTTP/2 200 OK');
            $headers('Content-Type: ' . key(self::parseRelativeQuality($server['HTTP_ACCEPT'])));
            $body('Hello World');
        };
    }
    
}
