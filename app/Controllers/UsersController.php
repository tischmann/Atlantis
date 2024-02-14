<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    App,
    Controller,
    Cookie,
    CSRF,
    Locale,
    Pagination,
    Request,
    Response,
    Sorting,
    Template,
    View
};

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

    public function signIn(Request $request)
    {
        try {
            CSRF::verify($request);
        } catch (Exception $e) {
            Response::redirect('/signin', new Alert(
                status: 0,
                message: $e->getMessage()
            ));
        }

        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = strval($request->request('login'));

        $password = strval($request->request('password'));

        $user = User::find($login, 'login');

        if (
            !$user->status
            || !$user->exists()
            || !password_verify($password, $user->password)
        ) {
            Response::redirect(
                url: '/signin',
                alert: new Alert(
                    status: 0,
                    message: Locale::get('access_denied')
                )
            );
        }

        $expires = time() + 60 * 60 * 24 * 30;

        Cookie::set(
            name: 'atlantis_remember',
            value: $expires,
            options: ['expires' => $expires]
        );

        $user->signIn();

        Response::redirect(url: '/');
    }

    public function signOut()
    {
        User::current()->signOut();

        Response::redirect('/');
    }

    /**
     * Вывод формы редактирования пользователя
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function get(Request $request)
    {
        $this->__admin();

        $request->validate([
            'id' => ['required'],
        ]);

        $id = intval($request->route('id'));

        $user = User::find($id);

        assert($user instanceof User);

        if (!$user->id) {
            throw new Exception(Locale::get('user_not_found'));
        }

        $this->editor($user);
    }

    /**
     * Вывод формы добавления пользователя
     *
     * @param Request $request
     * @return void
     */
    public function new(Request $request)
    {
        $this->__admin();

        $this->editor(new User());
    }

    /**
     * Вывод формы добавления/редактирования пользователя
     * 
     * @param Category $category Категория
     */
    protected function editor(User $user = new User())
    {
        $this->__admin();

        App::setTitle($user->id ? $user->login : Locale::get('user_new'));

        View::send(
            'admin/user',
            [
                'user' => $user,
            ]
        );
    }

    /**
     * Добавление пользователя
     *
     * @param Request $request
     * 
     * @return void
     */
    public function add(Request $request): void
    {
        $this->__admin();

        $request->validate([
            'login' => ['required'],
        ]);

        $user = new User();

        $user->login = strval($request->request('login'));

        $password = strval($request->request('password'));

        $url = "/" . getenv('APP_LOCALE') . '/admin/users';

        if (!$password) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_add_error_empty_password')
                )
            );
        }

        $user->password = password_hash($password, PASSWORD_DEFAULT);

        $user->role = match (strval($request->request('role'))) {
            User::ROLE_ADMIN => User::ROLE_ADMIN,
            User::ROLE_USER => User::ROLE_USER,
            User::ROLE_GUEST => User::ROLE_GUEST,
            default => null
        };

        $user->status = boolval($request->request('status'));

        $remarks = strval($request->request('remarks'));

        $user->remarks = $remarks ?: null;

        if (!$user->save()) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_add_error')
                )
            );
        }

        Response::redirect($url);
    }

    /**
     * Обновление пользователя
     *
     * @param Request $request
     * 
     * @return void
     */
    public function update(Request $request): void
    {
        $this->__admin();

        $request->validate([
            'login' => ['required'],
        ]);

        $id = $request->route('id');

        $user = User::find($id);

        assert($user instanceof User);

        if (!$user->exists()) {
            throw new Exception(Locale::get('user_not_found'));
        }

        $login = strval($request->request('login'));

        $find = User::find($login, 'login');

        $url = "/" . getenv('APP_LOCALE') . "/edit/user/{$user->id}";

        if ($find->exists() && $find->id !== $user->id) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_login_exists')
                )
            );
        }

        $user->login = $login;

        $password = strval($request->request('password'));

        if ($password) {
            $user->password = password_hash($password, PASSWORD_DEFAULT);
        }

        $role = match (strval($request->request('role'))) {
            User::ROLE_ADMIN => User::ROLE_ADMIN,
            User::ROLE_USER => User::ROLE_USER,
            User::ROLE_GUEST => User::ROLE_GUEST,
            default => null
        };

        $adminCount = User::query()->where('role', User::ROLE_ADMIN)->count();

        if (
            $user->role === User::ROLE_ADMIN &&
            $role !== User::ROLE_ADMIN &&
            $adminCount  === 1
        ) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('last_admin_change_role_error')
                )
            );
        }

        $user->role = $role;

        $status = boolval($request->request('status'));

        if (
            !$status &&
            $user->role === User::ROLE_ADMIN &&
            $adminCount  === 1
        ) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('last_admin_disable_error')
                )
            );
        }

        $user->status = $status;

        $remarks = strval($request->request('remarks'));

        $user->remarks = $remarks ?: null;

        if (!$user->save()) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_save_error')
                )
            );
        }

        Response::redirect("/" . getenv('APP_LOCALE') . '/admin/users');
    }

    /**
     * Удаление пользователя
     *
     * @param Request $request
     *
     */
    public function delete(Request $request)
    {
        $this->__admin();

        $id = intval($request->route('id'));

        $user = User::find($id);

        assert($user instanceof User);

        if (!$user->id) {
            throw new Exception(Locale::get('user_not_found') . ": {$id}");
        }

        $result = $user->delete();

        Response::send([
            'message' => $result
                ? Locale::get('user_deleted')
                : Locale::get('user_delete_error')
        ], $result ? 200 : 500);
    }
}
