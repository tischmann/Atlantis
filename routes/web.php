<?php

declare(strict_types=1);

use App\Controllers\{
    AdminController,
    ArticlesController,
    CategoriesController,
    IndexController,
    UsersController
};

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
    method: 'GET',
));

Router::add(new Route(
    controller: new CategoriesController(),
    path: 'category/{id}',
    action: 'getArticles',
    method: 'GET',
));

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'fetch/articles/{category_id}',
    action: 'fetchArticles',
    method: 'POST'
));

// Админка

if (User::current()->isAdmin()) {
    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin',
        action: 'index',
        method: 'GET'
    ));

    // Категории

    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin/categories',
        action: 'getCategories',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories/order',
        action: 'order',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'category/add',
        action: 'newCategory',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/add',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/add',
        action: 'add',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'category/edit/{id}',
        action: 'getCategory',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/edit/{id}',
        action: 'update',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/delete/{id}',
        action: 'delete',
        method: 'DELETE'
    ));

    // Статьи

    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin/articles',
        action: 'getArticles',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'upload/article/image/{id}',
        action: 'uploadImage',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'editArticle',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'update',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'add/article',
        action: 'newArticle',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'add/article',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'add/article',
        action: 'add',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/delete/{id}',
        action: 'delete',
        method: 'DELETE'
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'fetch/admin/articles/{category_id}',
        action: 'fetchArticles',
        method: 'POST'
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
