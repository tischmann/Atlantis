<?php

declare(strict_types=1);

use App\Controllers\{
    UsersController
};

use Tischmann\Atlantis\{
    App,
    Router,
    Route
};

if (App::getCurrentUser()->isAdmin()) {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'new',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'get',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'update',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'delete',
        method: 'DELETE'
    ));
}
