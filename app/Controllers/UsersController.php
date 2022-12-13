<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Exception;

use Tischmann\Atlantis\{
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
        Response::send(View::make('signin')->render());
    }

    public function signin(Request $request)
    {
        try {
            CSRF::verify($request);
        } catch (Exception $e) {
            return $this->signInError();
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
            return $this->signInError();
        }

        if (!password_verify($password, $user->password)) {
            return $this->signInError();
        }

        $month = time() + 60 * 60 * 24 * 30;

        Cookie::set('remember', $remember ? $month : 0, ['expires' => $month]);

        $user->signIn();

        Response::redirect('/');
    }

    private function signInError(): void
    {
        Response::send(View::make('signin', [
            'error' => Locale::get('signin_error'),
        ])->render());
    }

    public function signOut()
    {
        User::current()->signOut();

        Response::redirect('/');
    }
}
