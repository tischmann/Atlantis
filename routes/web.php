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
        path: 'admin/categories',
        action: 'index',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'admin/categories/order',
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
        path: 'admin/articles',
        action: 'getArticles',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'upload/article/image/{id}',
        action: 'uploadArticleImage',
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
        action: 'updateArticle',
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
        action: 'addArticle',
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
        action: 'confirmDeleteArticle',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'delete/article/{id}',
        action: 'deleteArticle',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'delete/article/{id}',
        action: 'deleteArticle',
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
