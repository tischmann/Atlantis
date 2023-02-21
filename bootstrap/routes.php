<?php

declare(strict_types=1);

foreach (glob(__DIR__ . "/../routes/*.php") as $path) {
    require_once $path;
}
