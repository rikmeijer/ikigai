<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

use rikmeijer\purposeplan\lib\Functional\Functional;

class Web {

    static function error(callable $respond) {
        return fn(string $code, string $description) => fn() => $respond($code . ' ' . $description, 'text/plain', $description);
    }
    
    static function acceptableTypes(string $acceptHeader) {
        return fn(callable $map) => Functional::intersect($map(Functional::arsort(array_reduce(explode(',', $acceptHeader), function ($res, $el) {
            list($l, $q) = array_merge(explode(';q=', $el), [1]);
            $res[$l] = (float) $q;
            return $res;
        }, []))));
    }

    static function entry(array $server): callable {
        $path = $server['REQUEST_URI'];
        $directory = Template::path($path);
        return fn(callable $respond) => Template::negotiate($directory, strtolower($server['REQUEST_METHOD']))
                        (self::error($respond)('404', 'File Not Found'))
                        (self::acceptableTypes($server['HTTP_ACCEPT']), self::error($respond)('405', 'Method Not Allowed'))
                        (Functional::partial_left($respond, '200 OK'),
                        self::error($respond)('406', 'Not Acceptable'));
    }

    static function resourceMatcher(callable $template, string $path, callable $error) {
        return fn(string $identifier, callable $resource) => Functional::if_else(
                        Functional::curry('str_starts_with')($path),
                        Functional::recurseTail(
                                fn(string $resource_path) => $resource($template),
                                fn(mixed $composed, string $resource_path) => fn(callable $router) => $router(self::resourceMatcher($template, substr($path, strlen($resource_path)), $error))
                        ),
                        fn() => Functional::nothing()
                )('/' . $identifier);
    }
}
