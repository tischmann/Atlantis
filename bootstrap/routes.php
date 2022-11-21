<?php

declare(strict_types=1);

foreach (glob(getenv('APP_ROOT') . "/routes/*.php") as $path) {
    require_once $path;
}
