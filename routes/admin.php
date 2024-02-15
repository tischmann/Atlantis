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
        action: 'addUserForm',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'addUser',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'getUser',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'updateUser',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'deleteUser',
        method: 'DELETE'
    ));
}
