<?php

declare(strict_types=1);

use Tischmann\Atlantis\Template;

Template::directive('referrer', function (...$args) {
    return $_SERVER['HTTP_REFERER'] ?? '/';
});

Template::directive('nonce', function (...$args) {
    return getenv('APP_NONCE');
});

Template::directive('title', function (...$args) {
    return getenv('APP_TITLE');
});

Template::directive('date', function (...$args) {
    return gmdate(...$args);
});

$uniqid = uniqid();

Template::directive('uniqid', function (...$args) use ($uniqid) {
    return $uniqid;
});
