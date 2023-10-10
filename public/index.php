<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$response = rikmeijer\purposeplan\lib\UI\Web::entry($_SERVER, require dirname(__DIR__) . '/config/routes.php');
$response('header', fn(string $body) => print $body);