<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Controller, View};

class IndexController extends Controller
{
    public function index(): void
    {
        View::send('index');
    }
}
