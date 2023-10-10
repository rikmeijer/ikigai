<?php declare(strict_types=1);

return function(callable $route) {
    $route('', function(callable $method) {
        $method('GET', function(callable $negotiate) {
            $negotiate([
                'text/html' => fn(callable $status) => $status('200 OK', '<!DOCTYPE html>Hello World!</html>')
            ]);
        });
    });
    $route('test', function(callable $method) {
        $method('GET', function(callable $negotiate) {
            $negotiate([
                'text/html' => fn(callable $status) => $status('200 OK', '<!DOCTYPE html>Hello World 2!</html>')
            ]);
        });
    });
};