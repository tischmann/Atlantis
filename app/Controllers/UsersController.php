<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Controller,
    Cookie,
    CSRF,
    Image,
    Locale,
    Request,
    Response,
    View
};

class UsersController extends Controller
{
    public function signinForm(): void
    {
        View::send('signin');
    }

    public function signin(Request $request)
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

        if (!password_verify($password, $user->password)) {
            Response::redirect('/signin', new Alert(
                status: 0,
                message: Locale::get('signin_error_bad_password')
            ));
        }

        $month = time() + 60 * 60 * 24 * 30;

        Cookie::set('remember', $remember ? $month : 0, ['expires' => $month]);

        $user->signIn();

        Response::redirect('/');
    }

    public function signOut()
    {
        User::current()->signOut();

        Response::redirect('/');
    }

    /**
     * Загрузка аватара
     * 
     * @param Request $request
     */
    public function uploadAvatar(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = $request->route('id');

        $width = intval($request->request('width'));

        $height = intval($request->request('height'));

        if (!is_dir("images/avatars")) {
            mkdir("images/avatars", 0775, true);
        }

        $imageFolder = $id ? "images/avatars" : "images/avatars/temp";

        if (!is_dir($imageFolder)) {
            mkdir($imageFolder, 0775, true);
        }

        reset($_FILES);

        $temp = current($_FILES);

        list($csrf_key, $csrf_token) = CSRF::set();

        if (!is_uploaded_file($temp['tmp_name'])) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Error uploading file'
            ]);

            exit;
        }

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Invalid file name'
            ]);

            exit;
        }

        $extensions = ["gif", "jpg", "png", "webp", "jpeg", "bmp"];

        $fileExtension = strtolower(
            pathinfo(
                $temp['name'],
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($fileExtension, $extensions)) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Invalid extension'
            ]);

            exit;
        }

        $filename = md5(bin2hex(random_bytes(128))) . '.webp';

        $filetowrite = $imageFolder . "/" . $filename;

        $quality = 80;

        switch ($fileExtension) {
            case 'gif':
                $im = imagecreatefromgif($temp['tmp_name']);
                break;
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($temp['tmp_name']);
                break;
            case 'png':
                $im = imagecreatefrompng($temp['tmp_name']);
                break;
            case 'bmp':
                $im = imagecreatefrombmp($temp['tmp_name']);
                break;
            case 'webp':
                $im = imagecreatefromwebp($temp['tmp_name']);
                break;
            default:
                Response::json([
                    'csrf' => $csrf_token,
                    'error' => 'Unsupported image format'
                ]);

                exit;
        }

        if ($width && $height) {
            $im = Image::resize($im, $width, $height);
        }

        if (!imagewebp($im, $filetowrite, $quality)) {
            Response::json([
                'csrf' => $csrf_token,
                'error' => 'Error converting image to webp'
            ]);

            exit;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
            ? "https://"
            : "http://";

        $baseurl = $protocol . $_SERVER["HTTP_HOST"];

        $location = $baseurl . "/" . $filetowrite;

        Response::json([
            'status' => 1,
            'csrf' => $csrf_token,
            'image' => basename($location),
            'location' => $location
        ]);
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

        CSRF::verify($request);

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

        $root = getenv('APP_ROOT');

        if ($avatar) {
            if (!is_dir("{$root}/public/images/avatars")) {
                mkdir("{$root}/public/images/avatars", 0775, true);
            }

            if (!is_dir("{$root}/public/images/avatars/temp")) {
                mkdir("{$root}/public/images/avatars/temp", 0775, true);
            }

            rename(
                "{$root}/public/images/avatars/temp/{$avatar}",
                "{$root}/public/images/avatars/{$avatar}"
            );

            $user->avatar = $avatar;
        }

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

    protected static function removeTempAvatars()
    {
        $root = getenv('APP_ROOT');

        $images = User::query()->distinct('avatar');

        if (!is_dir("{$root}/public/images/avatars/temp")) {
            mkdir("{$root}/public/images/avatars/temp", 0775, true);
        }

        foreach (glob("{$root}/public/images/avatars/temp/*.webp") as $file) {
            if (!in_array(basename($file), $images)) {
                unlink($file);
            }
        }
    }
}
