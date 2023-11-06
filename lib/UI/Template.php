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
    
    static function negotiate(array $acceptedTypes, callable $directory, callable $template, callable $found, callable $missingFile, callable $missingIdentifier, callable $missingType) : void {
        $resourceExists = self::try($directory(''));
        $methodExists = $resourceExists('is_dir', fn(string $path) => glob($template('*/*')), fn(string $path) => $missingFile());
        
        $mapTypes = Functional::intersect(Functional::map(fn(float $v, string $k) => $template($k))($acceptedTypes));
        $templateExists = $methodExists(
            [Functional::class, 'populated'],
            $mapTypes, 
            fn(array $availableTemplates) => $missingIdentifier()
        );
        $templateExists(
            [Functional::class, 'populated'],
            Functional::first(
                fn(string $typePath, string $acceptedType) => $found(fn(callable $send) => $send($acceptedType, Template::render(file_get_contents($typePath))(self::open($directory('.php'))))),
            ), 
            fn(array $selectedTemplates) => $missingType()
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
