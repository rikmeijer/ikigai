<?php declare(strict_types=1);

return function(callable $route) {
    $route('', function(callable $get, callable $update, callable $put, callable $delete, callable $head, callable $child) {
        $get(function(callable $negotiate) {
            $negotiate([
                'text/html' => fn(callable $status) => $status('200 OK', file_get_contents(dirname(__DIR__) . '/resources/view/index.html'))
            ]);
        });
    });
};