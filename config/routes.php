<?php declare(strict_types=1);

return function(callable $route) {
    $route('', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
        $get(function(callable $negotiate) {
            $negotiate([
                'text/html' => fn(callable $template) => $template(...[
                    
                ])
            ]);
        });
    });
};