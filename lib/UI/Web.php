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
    
    static function status(string $protocol, string $contentType, callable $body, callable $headers) {
        return function(string $status, string $content) use ($protocol, $contentType, $body, $headers) : void {
            $headers($protocol . ' ' . $status);
            $headers('Content-Type: ' . $contentType);
            $body($content);
        };
    }
    
    static function negotiate(array $acceptedTypes) {
        return function(string $availableType, callable $success) {
            $success($availableType);
        };
    }
        
    static function entry(array $server, callable $headers, callable $body) : callable {
        $negotiator = self::negotiate(self::parseRelativeQuality($server['HTTP_ACCEPT']));
        return fn(string $availableType, callable $router) => $negotiator( 
            $availableType, 
            fn(string $contentType) => $router(self::status($server['SERVER_PROTOCOL'], $contentType, $body, $headers))
        );
    }
    
}
