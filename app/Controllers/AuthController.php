<?php

declare(strict_types=1);

namespace App\Controllers;

use App\{Auth};

use App\Models\{User};

use Tischmann\Atlantis\{CSRF, Request, Response,  Session, View};

class AuthController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->id) {
            return Response::redirect('/');
        }

        $view = View::make(
            view: 'auth',
            args: ['user' => Auth::user()]
        );

        Response::echo($view->render());
    }

    /**
     * Вход пользователя
     * 
     * @param Request $request Экземпляр запроса
     * @return void
     */
    public function signIn(Request $request): void
    {
        CSRF::verify($request);

        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string']
        ]);

        $login = $request->request('login');

        $password = $request->request('password');

        $user = User::find($login, 'login');

        if ($user->exists()) {
            if (password_verify($password, $user->password)) {
                session_regenerate_id();

                Session::set('user_id', $user->id);

                Response::redirect('/');
            } else {
                $view = View::make(
                    view: 'errors/404',
                    args: ['message' => 'Wrong password']
                );

                Response::echo($view->render());
            }
        } else {
            $view = View::make(
                view: 'errors/404',
                args: ['message' => 'User not found']
            );

            Response::echo($view->render());
        }
    }

    /**
     * Выход пользователя
     * 
     * @return void
     */
    public function signOut(Request $request): void
    {
        Session::delete('user_id');

        session_regenerate_id();

        Response::redirect('/');
    }
}
