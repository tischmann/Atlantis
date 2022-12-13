<?php

declare(strict_types=1);

use App\Models\User;
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

Template::ifDirective('auth', function (...$args) {
    return User::current()->role !== User::ROLE_GUEST;
});

Template::ifDirective('admin', function (...$args) {
    return User::current()->role === User::ROLE_ADMIN;
});
