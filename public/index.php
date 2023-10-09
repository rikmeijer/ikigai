<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use function Lambdish\Phunctional\identity;

rikmeijer\purposeplan\lib\UI\Web::entry($_SERVER)('header', fn(string $body) => print $body);