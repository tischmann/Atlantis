<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{User};

use Tischmann\Atlantis\{Auth, Facade, Request, Response,  Session, View};

use Tischmann\Atlantis\Exceptions\{MethodNotExistsException, NotFoundException, RouteNotFoundException};

class Controller extends Facade
{
    public function index(Request $request)
    {
        $view = View::make(
            view: 'welcome',
            args: ['user' => Auth::user()]
        );

        Response::echo($view->render());
    }

    public function phpinfo(Request $request)
    {
        phpinfo();
    }

    public function signIn(Request $request)
    {
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

    public function signOut(Request $request)
    {
        Session::delete('user_id');

        session_regenerate_id();

        Response::redirect('/');
    }

    public function __call($name, $arguments): mixed
    {
        if (intval(getenv('APP_DEBUG'))) {
            throw new MethodNotExistsException($this, $name);
        }

        throw new RouteNotFoundException();
    }
}
