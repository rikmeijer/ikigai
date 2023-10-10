<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Web {
    
    static function parseRelativeQuality(string $header_value) : callable {
        return function(array $availableTypes) use ($header_value) {
            $typesAccepted = array_reduce(explode(',', $header_value), function ($res, $el) {
                list($l, $q) = array_merge(explode(';q=', $el), [1]); 
                $res[$l] = (float) $q; 
                return $res; 
            }, []);
            arsort($typesAccepted);
            
            foreach ($typesAccepted as $typeAccepted => $value) {
                if (array_key_exists($typeAccepted, $availableTypes)) {
                    return [$typeAccepted, $availableTypes[$typeAccepted]];
                }
            }
        };
    }
    
    static function status(string $protocol, callable $headers, callable $body) {
        return fn(string $contentType) => function(string $status, string $content) use ($protocol, $contentType, $body, $headers) : void {
            $headers($protocol . ' ' . $status);
            $headers('Content-Type: ' . $contentType);
            $body($content);
        };
    }
    
    
    static function entry(array $server, callable $routings) : callable {
        $acceptedTypes = self::parseRelativeQuality($server['HTTP_ACCEPT']);
        $requestMethod = fn(string $method) => $method === $server['REQUEST_METHOD'];
        $path = $server['REQUEST_URI'];    
        $endpoint = self::fileNotFound();
        $routings(function(string $identifier, callable $resource) use (&$endpoint, $acceptedTypes, $requestMethod, $path) {
            if (str_starts_with($path, '/' . $identifier)) {
                $resource(function(string $method, callable $endpoints) use (&$endpoint, $requestMethod, $acceptedTypes) {
                    $endpoint = self::methodNotAllowed();
                    if ($requestMethod($method)) {
                        $endpoints(function(array $availableTypes) use (&$endpoint, $acceptedTypes) {
                            $endpoint = $acceptedTypes($availableTypes) ?? self::notAcceptable();
                        });
                    }
                });
            }
                
        });
        return fn(callable $headers, callable $body) => $endpoint[1](self::status($server['SERVER_PROTOCOL'], $headers, $body)($endpoint[0]));
    }
    
    static function notAcceptable() : array {
        return ['text/plain', fn(callable $status) => $status('406 Not Acceptable', '')];
    }
    static function methodNotAllowed() : array {
        return ['text/plain', fn(callable $status) => $status('405 Method Not Allowed', '')];
    }
    static function fileNotFound() : array {
        return ['text/plain', fn(callable $status) => $status('404 File Not Found', '')];
    }
}
