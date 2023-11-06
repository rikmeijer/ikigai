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
    
    static function filepath(callable $directory, string $identifier) {
        return fn(string $type) => $directory(DIRECTORY_SEPARATOR . $identifier . '.' . self::typeToExtension($type));
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
    
    static function negotiateType(callable $directory, callable $templateExists) {
        return fn(callable $found, callable $missingType) => $templateExists(
            [Functional::class, 'populated'],
            Functional::first(
                fn(string $typePath, string $acceptedType) => $found(fn(callable $send) => $send($acceptedType, self::render(file_get_contents($typePath))(self::open($directory('.php'))))),
            ), 
            $missingType
        );
    }
    
    static function negotiateMethod(callable $typeNegotiator, callable $methodExists, callable $mapTypes) {
        return fn(array $acceptedTypes, callable $missingIdentifier) => 
                 $typeNegotiator($methodExists(
                    $mapTypes($acceptedTypes), 
                    $missingIdentifier
                ));
    }
    
    static function negotiate(callable $directory, callable $template) : callable {
        return Functional::partial_left(
                fn(callable $resourceExists, callable $missingFile) => self::negotiateMethod(
                    Functional::partial_left(
                        [self::class, 'negotiateType'], 
                        $directory
                    ),    

                    Functional::partial_left(
                        $resourceExists('is_dir', fn(string $path) => glob($template('*/*')), $missingFile),
                        [Functional::class, 'populated']
                    ), 
                    fn(array $acceptedTypes) => Functional::intersect(Functional::map(fn(float $v, string $k) => $template($k))($acceptedTypes))
                ),
        self::try($directory('')));
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
