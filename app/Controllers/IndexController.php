<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Controller, Locale, View, Response, Template};

class IndexController extends Controller
{
    public function index(): void
    {
        Response::send(
            View::make('index')->render()
        );
    }
}
