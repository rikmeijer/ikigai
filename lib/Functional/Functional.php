<?php declare(strict_types=1);

namespace rikmeijer\purposeplan\lib\Functional;

/**
 * Description of Functional
 *
 * @author rmeijer
 */
final class Functional {
    
    static function partial_left(callable $fn, mixed ...$args_left) {
        return fn(mixed ...$args) => $fn(...array_merge($args_left, $args));
    }
    static function partial_right(callable $fn, mixed ...$args_right) {
        return fn(mixed ...$args) => $fn(...array_merge($args, $args_right));
    }
    
    static function each(callable $fn, callable $empty) : callable {
        return fn(array $array) => empty($array) ? $empty() : array_walk($array, $fn);
    }
    
    static function first(callable $fn, callable $empty) : callable {
        $each = self::each($fn, $empty);
        return fn(array $array) => $each(array_slice($array, 0, 1));
    }
    
    static function filter(callable $fn) : callable {
        return function(array $array) use ($fn) {
            $filtered = [];
            foreach ($array as $key => $value) {
                if ($fn($value, $key) === true) {
                    $filtered[$key] = $value;
                }
            }
            return $filtered;
        };
    }
    
    static function map(callable $fn) : callable {
        return function(array $array) use ($fn) {
            $map = [];
            foreach ($array as $key => $value) {
                $map[$key] = $fn($value, $key);
            }
            return $map;
        };
    }
    
    
    static function find(callable $condition, callable $if, callable $else) : callable {
        return function(array $array) use ($condition, $if, $else) {
            foreach ($array as $key => $value) {
                if ($condition($value, $key) === true) {
                    return $if($value, $key);
                }
            }
            return $else();
        };
    }
    
    static function reduce(callable $fn) : callable {
        return fn(array $array, mixed $intial = null) => array_reduce($array, $fn, $intial);
    }
    
    static function compose(callable ...$fns) : callable {
        return fn(mixed $intial = null) => self::reduce(fn($carry, callable $fn) => $fn($carry))($fns, $intial);
    }
    
    static function recurseTail(callable ...$fns) : callable {
        $tail_fn = array_pop($fns);
        $composed = self::compose(...$fns);
        return fn(mixed ...$args) => $tail_fn($composed(...$args), ...$args);
    }
    
    static function if_else(callable $evaluation, callable $true, callable $false) {
        return fn(mixed $value) => $evaluation($value) ? $true($value) : $false($value);
    }
    
    static function nothing() {
        return fn() => null;
    }
    
    static function arsort(array $array) {
        arsort($array);
        return $array;
    }
}
