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

use function PHPSTORM_META\type;

// Главная
Router::add(new Route(
    controller: new IndexController(),
    title: Locale::get('home')
));

// Статьи
Router::add(new Route(
    controller: new ArticlesController(),
    path: 'article/{id}',
    action: 'show',
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
    action: 'fetch',
    method: 'POST',
    type: 'json',
    accept: 'json'
));

Router::add(new Route(
    controller: new ArticlesController(),
    path: 'rating/{id}/{rating}',
    action: 'setRating',
    method: 'POST',
    type: 'json',
    accept: 'json'
));

// Админка

if (User::current()->isAdmin()) {
    // Главная

    Router::add(new Route(
        controller: new AdminController(),
        path: 'admin',
        action: 'index',
        method: 'GET',
        title: Locale::get('dashboard')
    ));

    // Пользователи

    Router::add(new Route(
        controller: new UsersController(),
        path: 'admin/users',
        action: 'index',
        method: 'GET',
        title: Locale::get('users')
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'edit/user/{id}',
        action: 'get',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'edit/user/{id}',
        action: 'update',
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'upload/user/avatar/{id}',
        action: 'uploadAvatar',
        method: 'POST',
        type: 'form',
        accept: 'json'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'new',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'add',
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'add/user',
        action: 'add',
        method: 'PUT',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'user/delete/{id}',
        action: 'delete',
        method: 'DELETE',
        accept: 'json'
    ));

    Router::add(new Route(
        controller: new UsersController(),
        path: 'fetch/admin/users',
        action: 'fetch',
        method: 'POST',
        type: 'json',
        accept: 'json'
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
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'add/locale',
        action: 'add',
        method: 'PUT',
        type: 'form',
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
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new LocalesController(),
        path: 'locale/delete/{code}',
        action: 'delete',
        method: 'DELETE',
        accept: 'json'
    ));

    // Категории

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'admin/categories',
        action: 'index',
        method: 'GET',
        title: Locale::get('categories')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'categories/order',
        action: 'order',
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'add/category',
        action: 'new',
        method: 'GET',
        title: Locale::get('category_new')
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'add/category',
        action: 'add',
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'add/category',
        action: 'add',
        method: 'PUT',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/edit/{id}',
        action: 'get',
        method: 'GET'
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/edit/{id}',
        action: 'update',
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new CategoriesController(),
        path: 'category/delete/{id}',
        action: 'delete',
        method: 'DELETE',
        accept: 'json'
    ));

    // Статьи

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'admin/articles',
        action: 'index',
        method: 'GET',
        title: Locale::get('articles')
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'upload/article/image/{id}',
        action: 'uploadImage',
        method: 'POST',
        type: 'form',
        accept: 'json'
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
        method: 'POST',
        type: 'form',
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
        method: 'POST',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'add/article',
        action: 'add',
        method: 'PUT',
        type: 'form',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/delete/{id}',
        action: 'delete',
        method: 'DELETE',
        accept: 'json'
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'fetch/admin/articles',
        action: 'fetchAdmin',
        method: 'POST',
        type: 'json',
        accept: 'json'
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
        action: 'signIn',
        method: 'POST'
    ));
}
