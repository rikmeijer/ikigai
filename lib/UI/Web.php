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
        
        $requestMethod = fn(string $method) => strtoupper($method) === $server['REQUEST_METHOD'];
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
            $error = fn(string $code, string $description) => $respond('text/plain', $code . ' ' . $description, $description);
            
            $methods = Functional::map(fn(string $value) => fn(callable $endpoints) => Functional::if_else(
                    $requestMethod, 
                    fn($value) => $endpoints(fn(array $availableTypes) => Functional::find(
                            fn(float $value, string $typeAccepted) => array_key_exists($typeAccepted, $availableTypes), 
                            fn(float $value, string $typeAccepted) => Functional::partial_left($availableTypes[$typeAccepted], self::template(Functional::partial_left($respond, $typeAccepted)))(),
                            fn() => $error('406', 'Not Acceptable')
                    )(Functional::arsort($typesAccepted()))), 
                    Functional::nothing()
            )($value))(['get', 'update', 'put', 'delete', 'head']);

            $routings(self::resourceMatcher($methods, $path, $error));
            
            $error('404', 'File Not Found');
        };
    }
    
    static function template(callable $respond) {
        return fn(string $identifier, callable ...$blocks) => $respond('200 OK', Template::render(file_get_contents(Template::path($identifier)), ...$blocks));
    }
    
    static function resourceMatcher(array $methods, string $path, callable $error) {
        return fn(string $identifier, callable $resource) => Functional::if_else(
            Functional::partial_left('str_starts_with', $path), 
            Functional::compose(
                fn(string $resource_path) => $resource(...array_merge($methods, ['child' => self::resourceMatcher($methods, substr($path, strlen($resource_path)), $error)])), 
                fn() => $error('405', 'Method Not Allowed'),
            ),
            fn() => Functional::nothing()
        )('/' . $identifier);
    }
}
