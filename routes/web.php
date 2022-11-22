<?php

declare(strict_types=1);

use App\Controllers\{AuthController};

use Tischmann\Atlantis\{Locale, Router, Route};

// Для всех методов
Router::add(new Route());

Router::add(new Route(
    controller: new AuthController(),
    path: 'signin',
    method: 'GET',
    action: 'index',
    title: Locale::get('auth_title'),
));

Router::add(new Route(
    controller: new AuthController(),
    path: 'signin',
    method: 'POST',
    action: 'signIn',
));

Router::add(new Route(
    controller: new AuthController(),
    path: 'signout',
    action: 'signOut',
));
