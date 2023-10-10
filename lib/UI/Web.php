<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

use rikmeijer\purposeplan\lib\Functional\Functional;

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
        $protocol = $server['SERVER_PROTOCOL'];
        $acceptedTypes = fn(array $availableTypes) => self::parseRelativeQuality($server['HTTP_ACCEPT'])($availableTypes) ?? self::notAcceptable();
        $requestMethod = fn(string $method) => $method === $server['REQUEST_METHOD'];
        $path = $server['REQUEST_URI'];    

        
        return function(callable $headers, callable $body) use ($protocol, $acceptedTypes, $requestMethod, $path, $routings) {
            $endpoint = self::fileNotFound();
            
            $contentNegotiator = function(array $availableTypes) use (&$endpoint, $acceptedTypes) {
                $endpoint = $acceptedTypes($availableTypes);
            };
            
            $methodMatcher = function(string $method, callable $endpoints) use (&$endpoint, $requestMethod, $contentNegotiator) {
                $endpoint = self::methodNotAllowed();
                if ($requestMethod($method)) {
                    $endpoints($contentNegotiator);
                }
            };
            
            $methods = Functional::map(['get', 'update', 'put', 'delete', 'head'], fn($value, $key) => [$key => Functional::partial_left($methodMatcher, strtoupper($value))]);

            
            $resourceMatcher;
            $resourceMatcher = function(string $path) use (&$resourceMatcher, $methodMatcher, $methods) {
                return function(string $identifier, callable $resource) use ($path, &$resourceMatcher, $methods) {
                    if (str_starts_with($path, '/' . $identifier)) {
                        $methods['child'] = $resourceMatcher(substr($path, strlen($identifier) + 1));
                        $resource(...$methods);
                    }
                };
            };
            
            $routings($resourceMatcher($path));

            $endpoint[1](self::status($protocol, $headers, $body)($endpoint[0]));
            
        };
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
