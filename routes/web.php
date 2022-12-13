<?php

declare(strict_types=1);

use App\Controllers\{AdminController, ArticlesController, IndexController, UsersController};
use App\Models\User;
use Tischmann\Atlantis\{Router, Route};

// Главная
Router::add(new Route(
    controller: new IndexController(),
));

// Статьи
Router::add(new Route(
    controller: new ArticlesController(),
    path: 'article/{id}',
    action: 'getArticle',
    method: 'GET'
));

// Админка
if (User::current()->isAdmin()) {
    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin',
        action: 'index',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'admin/articles',
        action: 'getArticles',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'getArticleEditor',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'updateArticle',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'add/article',
        action: 'addArticle',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'delete/article/{id}',
        action: 'deleteArticle',
        method: 'DELETE'
    ));
}

if (User::current()->exists()) {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'signout',
        action: 'signout'
    ));
} else {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'signin',
        action: 'signinForm',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'signin',
        action: 'signin',
        method: 'POST'
    ));
}
