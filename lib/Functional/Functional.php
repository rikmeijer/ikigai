<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\Functional;

/**
 * Description of Functional
 *
 * @author rmeijer
 */
class Functional {
    
    static function partial_left(callable $fn, mixed ...$args) {
        return fn() => $fn(...array_merge($args, func_get_args()));
    }
}
