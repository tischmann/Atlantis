<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use Tischmann\Atlantis\{
    App,
    Auth,
    Controller,
    Pagination,
    Request,
    Response,
    View
};

/**
 * Контроллер пользователей
 */
class UsersController extends Controller
{
    public function showAllUsers(): void
    {
        $this->checkAdmin(type: 'html');

        $reqest = Request::instance();

        $order_types = [
            'created_at',
            'login',
            'name',
            'role'
        ];

        $order = strval($reqest->request('order') ?? 'login');

        $order = in_array($order, $order_types) ? $order : 'login';

        $direction = strval($reqest->request('direction') ?? 'asc');

        $direction = mb_strtolower($direction);

        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'asc';

        $order_options = [];

        foreach ($order_types as $type) {
            $order_options[] = [
                'value' => $type,
                'text' => get_str("user_order_{$type}"),
                'selected' => $order === $type,
                'level' => '0'
            ];
        }

        $direction_types = [
            'asc',
            'desc'
        ];

        $direction_options = [];

        foreach ($direction_types as $type) {
            $direction_options[] = [
                'value' => $type,
                'text' => get_str("direction_{$type}"),
                'selected' => $direction === $type,
                'level' => '0'
            ];
        }

        // Query

        $query = User::query();

        $query->order($order, $direction);

        $pagination = new Pagination(query: $query);

        $users = [];

        foreach ($query->get() as $user) {
            $users[] = User::instance($user);
        }

        View::send(
            view: 'users_list',
            args: [
                'pagination' => $pagination,
                'order_options' => $order_options,
                'direction_options' => $direction_options,
                'users' => $users,
                'order' => $order,
                'direction' => $direction,
            ]
        );
    }
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
    public function signIn(): void
    {
        if (csrf_failed()) {
            View::send(view: '403', layout: 'default', exit: true, code: 403);
        }

        $request = Request::instance();

        $login = strval($request->request('login'));

        $password = strval($request->request('password'));

        $user = User::find($login, 'login');

        if (!$user->exists()) {
            View::send(view: '403', layout: 'default', exit: true, code: 403);
        }

        if (!$user->status) {
            View::send(view: '403', layout: 'default', exit: true, code: 403);
        }

        if (!password_verify($password, $user->password)) {
            View::send(view: '403', layout: 'default', exit: true, code: 403);
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
    public function signOut(): void
    {
        $user = App::getCurrentUser();

        if (!$user->exists()) Response::redirect('/');

        Auth::instance($user)->signOut();

        $user->refresh_token = '';

        $user->save();

        Response::redirect('/');
    }

    /**
     * Вывод страницы пользователя
     *
     * @return void
     */
    public function getUser(): void
    {
        $this->checkAdmin(type: 'html');

        $id = intval($this->route->args('id'));

        $user = new User();

        if ($id) {
            $user = User::find($id);

            if (!$user->exists()) {
                View::send(
                    view: '404',
                    layout: 'default',
                    args: [
                        'exception' => new Exception(
                            message: get_str('user_not_found'),
                            code: 404
                        )
                    ],
                    exit: true,
                    code: 404
                );
            }
        }

        View::send(
            view: 'user_editor',
            layout: 'default',
            args: ['user' => $user]
        );
    }

    /**
     * Удаление пользователя
     *
     * @return void
     */
    public function deleteUser(): void
    {
        $this->checkAdmin(type: 'json');

        try {
            $id = intval($this->route->args('id'));

            $user = User::find($id);

            if (!$user->exists()) {
                throw new Exception(get_str('user_not_found'), 404);
            }

            if ($user->isLastAdmin()) {
                throw new Exception(get_str('user_last_admin'), 403);
            }

            if (!$user->delete()) {
                throw new Exception(get_str('user_delete_error'), 500);
            }

            Response::json(
                response: [
                    'title' => get_str('attention'),
                    'message' => get_str('deleted'),
                ],
                code: 200
            );
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Обновление/добавление пользователя
     *
     * @return void
     */
    public function updateUser(): void
    {
        $this->checkAdmin(type: 'json');

        try {
            $id = intval($this->route->args('id'));

            $user = new User();

            $new_user = false;

            if ($id) {
                $user = User::find($id);

                if (!$user->exists()) {
                    throw new Exception(get_str('user_not_found'), 404);
                }
            } else {
                $new_user = true;
            }

            $request = Request::instance();

            $request->validate([
                'name' => ['required', 'string'],
                'login' => ['required', 'string'],
                'password' => ['required', 'string'],
                'password_repeat' => ['required', 'string'],
                'remarks' => ['required', 'string'],
                'role' => ['required', 'string'],
                'status' => ['required', 'string']
            ]);

            $user->name = strval($request->request('name'));

            if (!User::checkUserName($user->name)) {
                throw new Exception(get_str('user_name_format'), 400);
            }

            $login = strval($request->request('login'));

            if (!User::checkUserLogin($login)) {
                throw new Exception(get_str('user_login_format'), 400);
            }

            if ($login !== $user->login) {
                if (!User::checkUserLoginExists($user->login)) {
                    throw new Exception(get_str('user_login_exists'), 400);
                }
            }

            $user->login = $login;

            $password = strval($request->request('password'));

            $password_repeat = strval($request->request('password_repeat'));

            if ($password || $password_repeat || $new_user) {
                if ($password !== $password_repeat) {
                    throw new Exception(get_str('user_passwords_not_match'), 400);
                }

                if (!User::checkPasswordComplexity($password)) {
                    throw new Exception(get_str('user_password_complexity'), 400);
                }

                $user->password = password_hash($password, PASSWORD_DEFAULT);
            }

            $user->remarks = strval($request->request('remarks'));

            $user->role = intval($request->request('role'));

            $user->status = boolval($request->request('status'));

            if (!$user->save()) {
                throw new Exception(get_str('user_save_error'), 500);
            }

            Response::json(
                response: [
                    'title' => get_str('attention'),
                    'message' => get_str('saved'),
                    'id' => $user->id,
                ],
                code: 200
            );
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }
}
