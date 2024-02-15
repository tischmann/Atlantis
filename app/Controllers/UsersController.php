<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};
use Exception;
use Tischmann\Atlantis\{
    App,
    Auth,
    Controller,
    Request,
    Response,
    Route,
    View
};

/**
 * Контроллер пользователей
 */
class UsersController extends Controller
{
    /**
     * Вывод формы авторизации
     *
     * @return void
     */
    public function signInForm(): void
    {
        View::send(view: 'signin', layout: 'signin');
    }

    /**
     * Авторизация пользователя
     *
     * @param Request $request
     * 
     * @return void
     */
    public function signIn()
    {
        if (csrf_failed()) {
            View::send(view: '403', layout: 'default', exit: true);
        }

        $request = Request::instance();

        $login = strval($request->request('login'));

        $password = strval($request->request('password'));

        $user = User::find($login, 'login');

        if (!$user->exists()) {
            View::send(view: '403', layout: 'default', exit: true);
        }

        if (!$user->status) {
            View::send(view: '403', layout: 'default', exit: true);
        }

        if (!password_verify($password, $user->password)) {
            View::send(view: '403', layout: 'default', exit: true);
        }

        $auth = new Auth($user);

        $user->refresh_token = $auth->signIn();

        $user->save();

        Response::redirect('/');
    }

    /**
     * Выход пользователя
     *
     * @return void
     */
    public function signOut()
    {
        $user = App::getCurrentUser();

        if (!$user->exists()) Response::redirect('/');

        Auth::instance($user)->signOut();

        $user->refresh_token = '';

        $user->save();

        Response::redirect('/');
    }


    /**
     * Вывод страницы добавления пользователя
     *
     * @return void
     */
    public function addUserForm()
    {
        $this->checkAdminRights(return: false);

        View::send(
            view: 'user',
            layout: 'default',
            args: ['user' => User::instance()]
        );
    }

    /**
     * Вывод страницы пользователя
     *
     * @return void
     */
    public function getUser()
    {
        $this->checkAdminRights(return: false);

        $id = intval($this->route->args('id'));

        $user = User::find($id);

        if (!$user->exists()) {
            View::send(
                view: '404',
                layout: 'default',
                args: ['exception' => new Exception(get_str('user_not_found'))],
                exit: true
            );
        }

        View::send(view: 'user', layout: 'default', args: ['user' => $user]);
    }
}
