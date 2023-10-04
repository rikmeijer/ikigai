<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use function Lambdish\Phunctional\identity;

print identity('hello world');