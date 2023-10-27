<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Template {
    
    static function render(string $html, callable ...$blocks) {
        return preg_replace_callback('/<block\s+name="(\w+)"\s+\/>/', fn(array $matches) => $blocks[$matches[1]](), $html);
    }
    
    static function directory() : string {
        chdir(dirname(dirname(__DIR__)));
        return realpath($_ENV['TEMPLATE_DIR']);
    }
    
    static function open(string $filepath) : array {
        return file_exists($filepath) ? (require $filepath)() : [];
    }
    
    static function negotiate(array $acceptedTypes, string $path, string $identifier, callable $found, callable $missingFile, callable $missingIdentifier, callable $missingType) : void {
        \rikmeijer\purposeplan\lib\Functional\Functional::if_else(
                fn(callable $directory) => is_dir($directory('')),
                fn(callable $directory) => \rikmeijer\purposeplan\lib\Functional\Functional::if_else(
                    fn(callable $template) => count(glob($template('*/*'))) > 0, 
                    fn(callable $template) => \rikmeijer\purposeplan\lib\Functional\Functional::find(
                        fn(string $acceptedType) => file_exists($template($acceptedType)),
                        fn(string $acceptedType) => $found($acceptedType, fn() => Template::render(file_get_contents($template($acceptedType)), ...self::open($directory('.php')))),
                        $missingType
                    )($acceptedTypes), 
                    fn(callable $template) => $missingIdentifier($template('*/*'))
                )(fn(string $type) => $directory(DIRECTORY_SEPARATOR . $identifier . '.' . self::typeToExtension($type))),
                fn(callable $directory) => $missingFile($directory(''))
        )(fn(string $file) => self::directory() . ($path === '/' ? '' : $path) . $file);
    }
    
    static function typeToExtension(string $contentType) {
        return match ($contentType) {
            'text/html' => 'html',
            'text/plain' => 'txt',
            'application/json' => 'json.php',
            '*/*' => '*'
        };
    }
    
}
