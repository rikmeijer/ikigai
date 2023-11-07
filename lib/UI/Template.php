<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

use \rikmeijer\purposeplan\lib\Functional\Functional;

class Template {
    
    static function render(string $html) {
        return fn(callable $blocks) => preg_replace_callback('/<block\s+name="(\w+)"\s+\/>/', fn(array $matches) => $blocks($matches[1]), $html);
    }
    
    static function path(string $path) : callable {
        chdir(dirname(dirname(__DIR__)));
        return fn(string $file) => realpath(getenv('TEMPLATE_DIR') ? getenv('TEMPLATE_DIR') : $_ENV['TEMPLATE_DIR']) . ($path === '/' ? '' : $path) . $file;
    }
    
    static function open(string $filepath) : callable {
        return file_exists($filepath) ? (require $filepath) : fn(string $identifier) => null;
    }
    
    static function fail(mixed $value) : callable {
        return fn() => self::fail($value);
    }
    static function try(mixed $value) : callable {
        return fn(callable $try, callable $success, callable $fail) => $try($value) ? self::try($success($value)) : self::fail($fail($value));
    }
    static function extract(callable $try) : mixed {
        $reflection = new \ReflectionFunction($try);
        return $reflection->getStaticVariables()['value'];
    }
    
    static function negotiateType(callable $blocks) {
        return fn(callable $templateExists) => fn(callable $found, callable $missingType) => $templateExists(
            [Functional::class, 'populated'],
            Functional::first(
                fn(string $typePath, string $acceptedType) => $found($acceptedType, self::render(file_get_contents($typePath))($blocks)),
            ), 
            $missingType
        );
    }
    
    static function negotiateMethod(callable $typeNegotiator, callable $mapTypes) {
        return fn(callable $methodExists) => fn(callable $acceptedTypes, callable $missingIdentifier) => $typeNegotiator($methodExists(
            [Functional::class, 'populated'],
            $acceptedTypes($mapTypes), 
            $missingIdentifier
        ));
    }
    
    static function negotiateResource(callable $resourceExists, callable $methodNegotiator) {
        return fn(callable $missingFile) => $methodNegotiator($resourceExists($missingFile));
    }
    
    static function negotiate(callable $directory, string $method) : callable {
        return self::negotiateResource(
            Functional::curry(self::try($directory('')))('is_dir')(fn(string $path) => glob($path . DIRECTORY_SEPARATOR . $method . '.*')),
            self::negotiateMethod(self::negotiateType(self::open($directory('.php'))), Functional::map(fn(float $v, string $k) => $directory(DIRECTORY_SEPARATOR . $method . '.' . self::typeToExtension($k))))
        );
    }
    
    static function typeToExtension(string $contentType) {
        return match ($contentType) {
            'text/html' => 'html',
            'text/plain' => 'txt',
            'application/json' => 'json.php',
            'application/xhtml+xml' => 'xhtml',
            '*/*' => '*'
        };
    }
    
}
