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
    
    static function status(string $protocol, callable $headers) {
        return function(string $status) use ($protocol, $headers) {
            $headers($protocol . ' ' . $status);
            return $headers;
        };
    }
    
    static function entry(array $server) : callable {
        return fn(callable $router) => fn(callable $headers, callable $body) => $body($router(self::parseRelativeQuality($server['HTTP_ACCEPT']), self::status($server['SERVER_PROTOCOL'], $headers)));
    }
    
}
