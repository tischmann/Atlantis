<?php

declare(strict_types=1);

foreach (glob(__DIR__ . "/../routes/*.php") as $path) {
    include_once $path;
}
