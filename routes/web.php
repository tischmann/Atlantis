<?php

declare(strict_types=1);

use App\Controllers\{AdminController, ArticlesController, CategoriesController, IndexController, UsersController};
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

    // Категории

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories',
        action: 'index',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories',
        action: 'index',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories/order',
        action: 'orderCategories',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/add',
        action: 'newCategory',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/add',
        action: 'addCategory',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/add',
        action: 'addCategory',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/edit/{id}',
        action: 'getCategory',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/edit/{id}',
        action: 'updateCategory',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/delete/{id}',
        action: 'confirmDeleteCategory',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/delete/{id}',
        action: 'deleteCategory',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/delete/{id}',
        action: 'deleteCategory',
        method: 'DELETE'
    ));

    // Статьи

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'articles',
        action: 'index',
        method: 'GET'
    ));


    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/edit/{id}',
        action: 'getArticleEditor',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/edit/{id}',
        action: 'updateArticle',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/add',
        action: 'addArticle',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/delete/{id}',
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
