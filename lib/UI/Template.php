<?php

declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\UI;

class Template {
    
    static function render(string $html, callable ...$blocks) {
        return '<html>'.$blocks['test']().'</html>';
    }
    
}
