<?php

declare(strict_types=1);

use App\Controllers\{
    IndexController,
    UsersController
};

use Tischmann\Atlantis\{
    App,
    Locale,
    Router,
    Route
};

$user = App::getCurrentUser();


// Главная страница

Router::add(new Route(
    controller: new IndexController(),
));

// Авторизованные пути

if ($user->exists()) {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'signout',
        action: 'signout',
        method: 'GET'
    ));

    // Пути для администраторов

    if ($user->isAdmin()) {
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
} else { // Неавторизованные пути
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
}
