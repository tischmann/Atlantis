<?php

declare(strict_types=1);

use App\Controllers\{
    ArticlesController,
    CategoriesController,
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

/**
 * Статьи
 */
Router::add(new Route(
    controller: new ArticlesController(),
    path: 'articles/{url}',
    action: 'showFullArticle',
    method: 'GET',
));

Router::add(new Route(
    controller: new IndexController(),
    path: 'sitemap',
    action: 'sitemap',
    method: 'GET',
));

Router::add(new Route(
    controller: new CategoriesController(),
    path: 'category/{slug}',
    action: 'showCategory',
    method: 'GET',
));

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'tags/{tag}',
    action: 'showArticlesByTag',
    method: 'GET',
));

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'like/article/{id}',
    action: 'likeArticle',
    method: 'POST',
));

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'like/article/{id}',
    action: 'dislikeArticle',
    method: 'DELETE',
));

Router::add(new Route(
    controller: new IndexController(),
    path: 'search',
    action: 'search',
    method: 'GET',
));
