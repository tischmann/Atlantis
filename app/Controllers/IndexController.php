<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{
    Controller,
    View
};

/**
 * Главный контроллер
 */
class IndexController extends Controller
{
    /**
     * Вывод главной страницы
     *
     * @return void
     */
    public function index(): void
    {
        View::send('index');
    }
}
