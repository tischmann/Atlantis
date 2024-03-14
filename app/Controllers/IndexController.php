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

    /**
     * Вывод карты сайта
     *
     * @return void
     */
    public function sitemap(): void
    {
        View::send('sitemap');
    }

    /**
     * Вывод админпанели
     *
     * @return void
     */
    public function showDashboard(): void
    {
        View::send('dashboard');
    }
}
