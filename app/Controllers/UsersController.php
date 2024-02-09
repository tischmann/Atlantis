<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    Cookie,
    CSRF,
    Image,
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
    public const ADMIN_FETCH_LIMIT = 5;

    /**
     * Вывод списка пользователь в админпанели
     */
    public function index(Request $request): void
    {
        $this->checkAdmin();

        $query = User::query()->limit(self::ADMIN_FETCH_LIMIT);

        $this->sort($query, $request);

        $pagination = new Pagination(
            total: $query->count(),
            limit: self::ADMIN_FETCH_LIMIT,
        );

        View::send(
            'admin/users',
            [
                'pagination' => $pagination,
                'users' => User::fill($query),
                'sortings' => [
                    new Sorting('login', 'asc'),
                    new Sorting('login', 'desc'),
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc'),
                    new Sorting('status', 'asc'),
                    new Sorting('status', 'desc'),
                ]
            ]
        );
    }

    public function signinForm(): void
    {
        View::send('signin');
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

        $remember = $request->request('remember') === 'on';

        $user = User::find($login, 'login');

        assert($user instanceof User);

        if (!$user->exists()) {
            Response::redirect('/signin', new Alert(
                status: 0,
                message: Locale::get('signin_error_user_not_found')
            ));
        }

        if (!$user->status) {
            Response::redirect('/signin', new Alert(
                status: 0,
                message: Locale::get('signin_error_user_disabled')
            ));
        }

        if (!password_verify($password, $user->password)) {
            Response::redirect('/signin', new Alert(
                status: 0,
                message: Locale::get('signin_error_bad_password')
            ));
        }

        $month = time() + 60 * 60 * 24 * 30;

        Cookie::set('atlantis_remember', $remember ? $month : 0, ['expires' => $month]);

        $user->signIn();

        Response::redirect('/');
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
        $this->checkAdmin();

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
        $this->checkAdmin();

        $this->editor(new User());
    }

    /**
     * Вывод формы добавления/редактирования пользователя
     * 
     * @param Category $category Категория
     */
    protected function editor(User $user = new User())
    {
        $this->checkAdmin();

        static::setTitle($user->id ? $user->login : Locale::get('user_new'));

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
        $this->checkAdmin();

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

        $avatar = strval($request->request('avatar'));

        $root = getenv('APP_ROOT');

        if ($avatar) {
            if (!is_dir("{$root}/public/images/avatars")) {
                mkdir("{$root}/public/images/avatars", 0775, true);
            }

            rename(
                sys_get_temp_dir() . "{$avatar}",
                "{$root}/public/images/avatars/{$avatar}"
            );

            $user->avatar = $avatar;
        }

        if (!$user->save()) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_add_error')
                )
            );
        }

        static::removeTempAvatars();

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
        $this->checkAdmin();

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

        $avatar = strval($request->request('avatar'));

        $user->avatar = $avatar;

        if (!$user->save()) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('user_save_error')
                )
            );
        }

        static::removeTempAvatars();

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
        $this->checkAdmin();

        $id = intval($request->route('id'));

        $user = User::find($id);

        assert($user instanceof User);

        if (!$user->id) {
            throw new Exception(Locale::get('user_not_found') . ": {$id}");
        }

        $file = getenv('APP_ROOT') . "/images/avatars/{$user->avatar}";

        if (is_file($file)) unlink($file);

        $result = $user->delete();

        Response::send([
            'message' => $result
                ? Locale::get('user_deleted')
                : Locale::get('user_delete_error')
        ], $result ? 200 : 500);
    }

    /**
     * Загрузка аватара
     * 
     * @param Request $request
     */
    public function uploadAvatar(Request $request)
    {
        $this->checkAdmin();

        $request->args('path', 'images/avatars');

        $request->args('width', 400);

        $request->args('height', 400);

        parent::uploadImage($request);
    }

    protected static function removeTempAvatars()
    {
        $root = getenv('APP_ROOT');

        $images = User::query()->distinct('avatar');

        foreach (glob("{$root}/public/images/avatars/*.webp") as $file) {
            if (!in_array(basename($file), $images)) {
                unlink($file);
            }
        }
    }

    /**
     * Динамическая подгрузка пользователей в админпанели
     */
    public function fetchUsers(Request $request): void
    {
        $this->checkAdmin();

        $this->sort($query, $request);

        $this->fetch(
            $request,
            User::query(),
            function ($query) {
                $html = '';

                foreach (User::fill($query) as $user) {
                    $html .= Template::html(
                        'admin/user-item',
                        [
                            'user' => $user,
                        ]
                    );
                }

                return $html;
            },
            static::ADMIN_FETCH_LIMIT
        );
    }
}
