<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\Functional;

/**
 * Description of Functional
 *
 * @author rmeijer
 */
final class Functional {
    
    static function partial_left(callable $fn, mixed ...$args) {
        return fn() => $fn(...array_merge($args, func_get_args()));
    }
    
    static function map(array $array, callable $fn) {
        $map = [];
        foreach ($array as $key => $value) {
            $map = array_merge($map, $fn($value, $key));
        }
        return $map;
    }
    
    static function if_else(callable $evaluation, callable $true, callable $false) {
        return fn(mixed $value) => $evaluation($value) ? $true($value) : $false($value);
    }
    
    static function nothing() {
        return fn() => null;
    }
}
