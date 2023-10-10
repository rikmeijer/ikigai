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
    
    
    static function entry(array $server, callable $headers, callable $body, callable $routings) : void {
        $status = self::status($server['SERVER_PROTOCOL'], $body, $headers);
        $acceptedTypes = self::parseRelativeQuality($server['HTTP_ACCEPT']);
        $requestMethod = fn(string $method) => $method === $server['REQUEST_METHOD'];
        $path = $server['REQUEST_URI'];    
        $endpoint = self::fileNotFound();
        $routings(function(string $identifier, callable $resource) use (&$endpoint, $acceptedTypes, $requestMethod, $status, $path) {
            if (str_starts_with($path, '/' . $identifier)) {
                $resource(function(string $method, callable $endpoints) use (&$endpoint, $requestMethod, $acceptedTypes, $status) {
                    $endpoint = self::methodNotAllowed();
                    if ($requestMethod($method)) {
                        $endpoints(function(array $availableTypes) use (&$endpoint, $acceptedTypes) {
                            $endpoint = self::notAcceptable();
                            foreach ($acceptedTypes as $contentTypeAccepted => $value) {
                                if (array_key_exists($contentTypeAccepted, $availableTypes)) {
                                    $endpoint = [$contentTypeAccepted, $availableTypes[$contentTypeAccepted]];
                                    return;
                                }
                            }
                        });
                    }
                });
            }
                
        });
        $endpoint[1]($status($endpoint[0]));
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
