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
    Locale,
    Request,
    Response,
    View
};

class UsersController extends Controller
{
    public function signinForm(): void
    {
        Response::send(
            View::make('signin', ['csrf' => $this->getCsrfInput()])->render()
        );
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
}
