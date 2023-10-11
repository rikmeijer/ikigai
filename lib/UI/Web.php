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

        
        return function(callable $headers, callable $body) use ($protocol, $typesAccepted, $requestMethod, $path, $routings) : void  {
            $protocol = fn(string $code) => $headers($protocol($code));
            $respond = function(string $contentType, string $status, string $content) use ($protocol, $body, $headers) : void {
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
                    fn(float $value, string $typeAccepted) => Functional::partial_left($availableTypes[$typeAccepted], Functional::partial_left($respond, $typeAccepted)),
                    fn() => self::notAcceptable($respond)
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

            $routings(self::resourceMatcher($methods, $path, $respond));
            
            self::fileNotFound($respond)();
        };
    }
    
    static function resourceMatcher(array $methods, string $path, callable $respond) {
        return fn(string $identifier, callable $resource) => Functional::if_else(
            Functional::partial_left('str_starts_with', $path), 
            Functional::compose(
                fn(string $resource_path) => $resource(...array_merge($methods, ['child' => self::resourceMatcher($methods, substr($path, strlen($resource_path)), $respond)])), 
                fn() => self::methodNotAllowed($respond)(),
            ),
            fn() => Functional::nothing()
        )('/' . $identifier);
    }
    
    static function notAcceptable(callable $respond) : callable {
        return fn() => $respond('text/plain', '406 Not Acceptable', '');
    }
    static function methodNotAllowed(callable $respond) : callable {
        return fn() => $respond('text/plain', '405 Method Not Allowed', '');
    }
    static function fileNotFound(callable $status) : callable {
        return fn() => $status('text/plain', '404 File Not Found', '');
    }
}
