<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

use rikmeijer\purposeplan\lib\Functional\Functional;

class Web {
    
    static function entry(array $server, callable $routings) : callable {
        $protocol = fn(string $code) => $server['SERVER_PROTOCOL'] . ' ' . $code;
        $typesAccepted = fn() => array_reduce(explode(',', $server['HTTP_ACCEPT']), function ($res, $el) {
            list($l, $q) = array_merge(explode(';q=', $el), [1]); 
            $res[$l] = (float) $q; 
            return $res; 
        }, []);
        
        $requestMethod = fn(string $method) => $method === $server['REQUEST_METHOD'];
        $path = $server['REQUEST_URI'];    

        
        return function(callable $headers, callable $body) use ($protocol, $typesAccepted, $requestMethod, $path, $routings) {
            $protocol = fn(string $code) => $headers($protocol($code));
            $status = function(string $contentType, string $status, string $content) use ($protocol, $body, $headers) : void {
                static $sent = false;
                if ($sent) {
                    return;
                }
                $protocol($status);
                $headers('Content-Type: ' . $contentType);
                $body($content);
                $sent = true;
            };
            
            $acceptedTypes = fn(array $availableTypes) => Functional::find(
                    fn(float $value, string $typeAccepted) => array_key_exists($typeAccepted, $availableTypes), 
                    fn(float $value, string $typeAccepted) => Functional::partial_left($availableTypes[$typeAccepted], Functional::partial_left($status, $typeAccepted)),
                    fn() => self::notAcceptable($status)
            )(Functional::arsort($typesAccepted()));
            
            
            $contentNegotiator = function(array $availableTypes) use ($acceptedTypes) {
                $acceptedTypes($availableTypes)();
            };
            
            $methodMatcher = function(string $method, callable $endpoints) use ($requestMethod, $contentNegotiator) {
                if ($requestMethod($method)) {
                    $endpoints($contentNegotiator);
                }
            };
            
            $methods = Functional::map(fn($value) => Functional::partial_left($methodMatcher, strtoupper($value)))(['get', 'update', 'put', 'delete', 'head']);

            $routings(self::resourceMatcher($methods, $path, $status));
            
            self::fileNotFound($status)();
        };
    }
    
    static function resourceMatcher(array $methods, string $path, callable $status) {
        return fn(string $identifier, callable $resource) => Functional::if_else(
            fn(string $identifier) => str_starts_with($path, '/' . $identifier), 
            fn(string $identifier) => Functional::compose(
                fn() => $resource(...array_merge($methods, ['child' => self::resourceMatcher($methods, substr($path, strlen($identifier) + 1), $status)])), 
                fn() => self::methodNotAllowed($status)(),
            )(),
            fn(string $identifier) => Functional::nothing()
        )($identifier);
    }
    
    static function notAcceptable(callable $status) : callable {
        return fn() => $status('text/plain', '406 Not Acceptable', '');
    }
    static function methodNotAllowed(callable $status) : callable {
        return fn() => $status('text/plain', '405 Method Not Allowed', '');
    }
    static function fileNotFound(callable $status) : callable {
        return fn() => $status('text/plain', '404 File Not Found', '');
    }
}
