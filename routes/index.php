<?php

declare(strict_types=1);

use App\Controllers\{
    IndexController,
    UsersController
};

use Tischmann\Atlantis\{
    Router,
    Route
};

/**
 * Главная страница
 */
Router::add(new Route(
    controller: new IndexController(),
));

/**
 * Форма входа
 */
Router::add(new Route(
    controller: new UsersController(),
    path: 'signin',
    action: 'signInForm',
    method: 'GET',
    title: get_str('signin')
));

/**
 * Авторизация
 */
Router::add(new Route(
    controller: new UsersController(),
    path: 'signin',
    action: 'signIn',
    method: 'POST',
));