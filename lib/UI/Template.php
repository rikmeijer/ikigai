<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Template {
    
    static function path(string $identifier, string $contentType) {
        return self::directory() . DIRECTORY_SEPARATOR . $identifier . '.' . match ($contentType) {
            'text/html' => 'html',
            'text/plain' => 'txt',
            'application/json' => 'json.php'
        };
    }
    
    static function render(string $html, callable ...$blocks) {
        return preg_replace_callback('/<block\s+name="(\w+)"\s+\/>/', fn(array $matches) => $blocks[$matches[1]](), $html);
    }
    
    static function directory() : string {
        chdir(dirname(dirname(__DIR__)));
        return realpath($_ENV['TEMPLATE_DIR']);
    }
    
    static function negotiate(array $acceptedTypes, string $identifier, callable $else) : string {
        return \rikmeijer\purposeplan\lib\Functional\Functional::find(
                fn(string $acceptedType) => file_exists(self::directory() . '/' . $identifier . '.' . self::typeToExtension($acceptedType)),
                fn(string $acceptedType) => file_get_contents(self::directory() . '/' . $identifier . '.' . self::typeToExtension($acceptedType)),
                fn(string $acceptedType) => $else()
        )($acceptedTypes);
    }
    
    static function typeToExtension(string $contentType) {
        return match ($contentType) {
            'text/html' => 'html',
            'text/plain' => 'txt',
            'application/json' => 'json',
            default => ''
        };
    }
    
}
