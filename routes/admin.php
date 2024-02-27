<?php

declare(strict_types=1);

use App\Controllers\{
    ArticlesController,
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
        path: 'users',
        action: 'showAllUsers',
        method: 'GET',
        title: get_str('user_list')
    ));

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

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'edit/article/{id}',
        action: 'getArticleEditor',
        method: 'GET',
        title: get_str('article_edit')
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/{id}',
        action: 'updateArticle',
        method: 'PUT',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/image',
        action: 'uploadImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/gallery',
        action: 'uploadGalleryImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/videos',
        action: 'uploadVideos',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/attachements',
        action: 'uploadAttachements',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/image/{id}',
        action: 'uploadImage',
        method: 'POST',
    ));

    Router::add(new Route(
        controller: new ArticlesController(),
        path: 'article/temp/image',
        action: 'deleteTempImage',
        method: 'DELETE',
    ));
}
