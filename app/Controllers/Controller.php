<?php

declare(strict_types=1);

namespace App\Controllers;

use App\{Auth};

use Tischmann\Atlantis\{Facade, Request, Response, Template, View};

use Tischmann\Atlantis\Exceptions\{MethodNotExistsException, RouteNotFoundException};

class Controller extends Facade
{
    public function __construct()
    {
        $user = Auth::authorize();

        Template::ifDirective('auth', function (...$args) use ($user) {
            return $user->exists();
        });

        Template::ifDirective('admin', function (...$args) use ($user) {
            return $user->isAdmin();
        });
    }

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

    public function __call($name, $arguments): mixed
    {
        if (intval(getenv('APP_DEBUG'))) {
            throw new MethodNotExistsException($this, $name);
        }

        throw new RouteNotFoundException();
    }
}
