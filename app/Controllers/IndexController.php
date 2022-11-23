<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Controller, View, Response};

class IndexController extends Controller
{
    public function index(): void
    {
        Response::send(View::make('welcome')->render());
    }
}
