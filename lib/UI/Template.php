<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Template {
    
    static function path(string $identifier) {
        chdir(dirname(dirname(__DIR__)));
        return realpath($_ENV['TEMPLATE_DIR']) . DIRECTORY_SEPARATOR . $identifier . '.html';
    }
    
    static function render(string $html, callable ...$blocks) {
        return preg_replace_callback('/<block\s+name="(\w+)"\s+\/>/', fn(array $matches) => $blocks[$matches[1]](), $html);
    }
    
}
