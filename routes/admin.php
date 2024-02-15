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
        method: 'GET',
        title: get_str('user_new')
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user',
        action: 'addUser',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/{id}',
        action: 'getUser',
        method: 'GET',
        title: get_str('user_update')
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
