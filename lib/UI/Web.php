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
    
    static function status(string $protocol, callable $body, callable $headers) {
        return function(string $status, string $content) use ($protocol, $body, $headers) : void {
            $headers($protocol . ' ' . $status);
            $body($content);
        };
    }
    
    static function negotiate(array $acceptedTypes, string $availableType, callable $success) {
        return function(callable $headers, callable $body) use ($availableType, $success) {
            $headers('Content-Type: ' . $availableType);
            $success($headers, $body);
        };
    }
    
    static function entry(array $server) : callable {
        return fn(string $availableType, callable $router) => self::negotiate(
                self::parseRelativeQuality($server['HTTP_ACCEPT']), 
                $availableType, 
                fn(callable $headers, callable $body) => $router(self::status($server['SERVER_PROTOCOL'], $body, $headers))
        );
    }
    
}
