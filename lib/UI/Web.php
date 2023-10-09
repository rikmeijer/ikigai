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
        return fn(string $contentType) => function(string $status, string $content) use ($protocol, $contentType, $body, $headers) : void {
            $headers($protocol . ' ' . $status);
            $headers('Content-Type: ' . $contentType);
            $body($content);
        };
    }
    
    static function negotiate(array $acceptedTypes, callable $status) {
        return fn(string $availableType) => fn(callable $success, callable $error) => array_key_exists($availableType, $acceptedTypes) ? $success($status($availableType)) : $error($status(key($acceptedTypes)));
    }
        
    static function entry(array $server, callable $headers, callable $body) : callable {
        return self::negotiate(self::parseRelativeQuality($server['HTTP_ACCEPT']), self::status($server['SERVER_PROTOCOL'], $body, $headers));
    }
    
    static function notAcceptable() : callable {
        return fn(callable $status) => $status('406 Not Acceptable', '');
    }
    
    static function skip() : callable {
        return function(callable $status) : void {};
    }
}
