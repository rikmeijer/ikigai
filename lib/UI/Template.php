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
    
    
    static function negotiate(array $acceptedTypes, callable $directory, callable $template, callable $found, callable $missingFile, callable $missingIdentifier, callable $missingType) : callable {
        $findType = Functional::first(
            fn(string $typePath, string $acceptedType) => $found(fn(callable $send) => $send($acceptedType, Template::render(file_get_contents($typePath))(self::open($directory('.php'))))),
            $missingType
        );
        
        $ifTemplatesUnavailable = Functional::partial_left([Functional::class, 'if_else'], fn(array $availableTemplates) => count($availableTemplates) === 0, $missingIdentifier);
        $findMethod = fn(string $path) => $ifTemplatesUnavailable(fn(array $availableTemplates) => $findType(Functional::intersect(Functional::map(fn(float $v, string $k) => $template($k))($acceptedTypes))($availableTemplates)))(glob($template('*/*')));
        return \rikmeijer\purposeplan\lib\Functional\Functional::if_else('is_dir', $findMethod, $missingFile);
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
