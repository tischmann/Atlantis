<?php

declare(strict_types=1);

use App\Controllers\{
    IndexController,
    UsersController
};

use Tischmann\Atlantis\{
    Locale,
    Router,
    Route
};

Router::add(new Route(
    controller: new IndexController(),
));

Router::add(new Route(
    controller: new UsersController(),
    path: 'signin',
    action: 'signInForm',
    method: 'GET',
    title: Locale::get('signin')
));

Router::add(new Route(
    controller: new UsersController(),
    path: 'signin',
    action: 'signIn',
    method: 'POST',
));
