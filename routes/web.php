<?php

declare(strict_types=1);

use App\Controllers\{
    AdminController,
    ArticlesController,
    CategoriesController,
    IndexController,
    LocalesController,
    UsersController
};

use App\Models\User;

use Tischmann\Atlantis\{Locale, Router, Route};

// Главная
Router::add(new Route(
    controller: new IndexController(),
    title: Locale::get('home')
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

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'rating/{id}/{rating}',
    action: 'setRating',
    method: 'POST'
));

// Админка

if (User::current()->isAdmin()) {
    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin',
        action: 'index',
        method: 'GET',
        title: Locale::get('dashboard')
    ));

    // Локали

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'admin/locales',
        action: 'index',
        method: 'GET',
        title: Locale::get('locales')
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'add/locale',
        action: 'new',
        method: 'GET',
        title: Locale::get('locale_new')
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'add/locale',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'add/locale',
        action: 'add',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'locale/edit/{code}',
        action: 'get',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'locale/edit/{code}',
        action: 'update',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'locale/delete/{code}',
        action: 'delete',
        method: 'DELETE'
    ));

    // Категории

    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin/categories',
        action: 'getCategories',
        method: 'GET',
        title: Locale::get('categories')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories/order',
        action: 'order',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'add/category',
        action: 'newCategory',
        method: 'GET',
        title: Locale::get('category_new')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'add/category',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'add/category',
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
        method: 'GET',
        title: Locale::get('articles')
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

    // Пользователи

    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin/users',
        action: 'getUsers',
        method: 'GET',
        title: Locale::get('users')
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'edit/user/{id}',
        action: 'editUser',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'edit/user/{id}',
        action: 'update',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'upload/user/avatar/{id}',
        action: 'uploadAvatar',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'newUser',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'add',
        method: 'POST'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'add',
        method: 'PUT'
    ));

    Router::add(new Route(
        controller: new AdminController(),
        path: 'fetch/admin/users',
        action: 'fetchUsers',
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
