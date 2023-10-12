<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$response = rikmeijer\purposeplan\lib\UI\Web::entry($_SERVER, require dirname(__DIR__) . '/config/routes.php');
$response('header', fn(string $body) => print $body);