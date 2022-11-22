<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Facade, Locale, Request, Response, View};

class Controller extends Facade
{
    public function index()
    {
        Response::echo(View::make(view: 'welcome')->render());
    }

    public function __call($name, $arguments): mixed
    {
        throw new \Exception(Locale::get('error_404'), 404);
    }
}
