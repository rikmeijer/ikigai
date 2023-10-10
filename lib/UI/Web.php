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
                    return $availableTypes[$typeAccepted];
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

            $routings(self::resourceMatcher($methods, $path));

            $endpoint(self::status($protocol, $headers, $body));
            
        };
    }
    
    static function resourceMatcher(array $methods, string $path) {
        $ifelse = Functional::if_else(
            fn(string $identifier) => str_starts_with($path, '/' . $identifier), 
            fn(string $identifier) => fn(callable $resource) => $resource(...array_merge($methods, ['child' => self::resourceMatcher($methods, substr($path, strlen($identifier) + 1))])), 
            fn(string $identifier) => Functional::nothing()
        );
        return Functional::partial_left(fn(string $identifier, callable $resource) => $ifelse($identifier)($resource));
    }
    
    static function notAcceptable() : callable {
        return fn(callable $status) => $status('text/plain')('406 Not Acceptable', '');
    }
    static function methodNotAllowed() : callable {
        return fn(callable $status) => $status('text/plain')('405 Method Not Allowed', '');
    }
    static function fileNotFound() : callable {
        return fn(callable $status) => $status('text/plain')('404 File Not Found', '');
    }
}
