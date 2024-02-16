<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use InvalidArgumentException;

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
        $this->checkAdminHtml();

        $query = User::query();

        $pagination = new Pagination(query: $query);

        $users = [];

        foreach ($query->get() as $user) {
            $users[] = User::instance($user);
        }

        View::send(
            view: 'user_list',
            layout: 'default',
            args: [
                'users' => $users,
                'pagination' => $pagination
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
     * Вывод страницы добавления пользователя
     *
     * @return void
     */
    public function addUserForm(): void
    {
        $this->checkAdminHtml();

        View::send(
            view: 'user',
            layout: 'default',
            args: ['user' => User::instance()]
        );
    }

    /**
     * Добавление пользователя
     *
     * @return void
     */
    public function addUser(): void
    {
        $this->checkCsrfJson();

        $this->checkAdminJson();

        $user = User::instance();

        $this->fillUser($user);

        if (!$user->save()) {
            Response::json(
                response: [
                    'token' => csrf_set()->token,
                    'text' => get_str('user_save_error')
                ],
                code: 500
            );
        }

        Response::json(
            response: ['ok' => true, 'redirect' => '/users'],
            code: 200
        );
    }

    /**
     * Вывод страницы пользователя
     *
     * @return void
     */
    public function getUser(): void
    {
        $this->checkAdminHtml();

        $id = intval($this->route->args('id'));

        $user = User::find($id);

        if (!$user->exists()) {
            View::send(
                view: '404',
                layout: 'default',
                args: ['exception' => new Exception(get_str('user_not_found'))],
                exit: true,
                code: 404
            );
        }

        View::send(view: 'user', layout: 'default', args: ['user' => $user]);
    }

    /**
     * Удаление пользователя
     *
     * @return void
     */
    public function deleteUser(): void
    {
        $this->checkCsrfJson();

        $this->checkAdminJson();

        $id = intval($this->route->args('id'));

        $user = User::find($id);

        if (!$user->exists()) {
            Response::json(
                response: [
                    'title' => get_str('error'),
                    'text' => get_str('user_not_found'),
                    'redirect' => '/users'
                ],
                code: 404
            );
        }

        if ($user->isLastAdmin()) {
            Response::json(
                response: [
                    'token' => csrf_set()->token,
                    'title' => get_str('error'),
                    'text' => get_str('user_last_admin')
                ],
                code: 403
            );
        }

        if (!$user->delete()) {
            Response::json(
                response: [
                    'token' => csrf_set()->token,
                    'title' => get_str('error'),
                    'text' => get_str('user_delete_error')
                ],
                code: 500
            );
        }

        Response::json(
            response: ['redirect' => '/users'],
            code: 200
        );
    }

    /**
     * Обновление пользователя
     *
     * @return void
     */
    public function updateUser(): void
    {
        $this->checkCsrfJson();

        $this->checkAdminJson();

        $id = intval($this->route->args('id'));

        $user = User::find($id);

        if (!$user->exists()) {
            Response::json(
                response: [
                    'title' => get_str('error'),
                    'text' => get_str('user_not_found')
                ],
                code: 404
            );
        }

        $this->fillUser($user);

        if (!$user->save()) {
            Response::json(
                response: [
                    'token' => csrf_set()->token,
                    'title' => get_str('error'),
                    'text' => get_str('user_save_error')
                ],
                code: 500
            );
        }

        Response::json(
            response: ['ok' => true, 'redirect' => '/users'],
            code: 200
        );
    }

    protected function fillUser(User &$user): User
    {
        try {
            $request = Request::instance();

            try {
                $request->validate([
                    'name' => ['required', 'string'],
                    'login' => ['required', 'string'],
                    'password' => ['required', 'string'],
                    'password_repeat' => ['required', 'string'],
                    'remarks' => ['required', 'string'],
                    'role' => ['required', 'string'],
                    'status' => ['required', 'string']
                ]);
            } catch (InvalidArgumentException $exception) {
                Response::text(response: $exception->getMessage(), code: 400);
            }

            $user->name = strval($request->request('name'));

            if (!User::checkUserName($user->name)) {
                Response::json(
                    response: [
                        'token' => csrf_set()->token,
                        'title' => get_str('error'),
                        'text' => get_str('user_name_format')
                    ],
                    code: 400
                );
            }

            $user->login = strval($request->request('login'));

            if (!User::checkUserLogin($user->login)) {
                Response::json(
                    response: [
                        'token' => csrf_set()->token,
                        'title' => get_str('error'),
                        'text' => get_str('user_login_format')
                    ],
                    code: 400
                );
            }

            if (!$user->exists()) {
                if (!User::checkUserLoginExists($user->login)) {
                    Response::json(
                        response: [
                            'token' => csrf_set()->token,
                            'title' => get_str('error'),
                            'text' => get_str('user_login_exists')
                        ],
                        code: 400
                    );
                }
            }

            $password = strval($request->request('password'));

            $password_repeat = strval($request->request('password_repeat'));

            if ($password || $password_repeat || !$user->exists()) {
                if ($password !== $password_repeat) {
                    Response::json(
                        response: [
                            'token' => csrf_set()->token,
                            'text' => get_str('user_passwords_not_match')
                        ],
                        code: 400
                    );
                }

                if (!User::checkPasswordComplexity($password)) {
                    Response::json(
                        response: [
                            'token' => csrf_set()->token,
                            'title' => get_str('error'),
                            'text' => get_str('user_password_complexity')
                        ],
                        code: 400
                    );
                }

                $user->password = password_hash($password, PASSWORD_DEFAULT);
            }

            $user->remarks = strval($request->request('remarks'));

            $user->role = intval($request->request('role'));

            $user->status = boolval($request->request('status'));

            return $user;
        } catch (Exception $exception) {
            Response::json(
                response: [
                    'token' => csrf_set()->token,
                    'title' => get_str('error'),
                    'text' => $exception->getMessage()
                ],
                code: 500
            );
        }
    }


    /**
     * Проверка CSRF токена
     *
     * В случае ошибки токена отправляет JSON
     *
     * @return void
     */
    protected function checkCsrfJson(): void
    {
        if (!csrf_verify()) {
            Response::json(
                response: [
                    'title' => get_str('error'),
                    'text' => get_str('csrf_failed'),
                    'redirect' => '/'
                ],
                code: 403
            );
        }
    }

    /**
     * Проверка прав доступа администратора
     *
     * В случае отсутствия прав доступа отправляет JSON
     *
     * @return void
     */
    protected function checkAdminJson(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            Response::json(
                response: [
                    'title' => get_str('error'),
                    'text' => get_str('access_denied'),
                    'redirect' => '/'
                ],
                code: 403
            );
        }
    }
}
