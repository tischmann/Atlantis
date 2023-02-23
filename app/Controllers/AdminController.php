<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{
    Breadcrumb,
    Controller,
    Locale,
    View
};

class AdminController extends Controller
{
    /**
     * Вывод главной страницы админпанели
     */
    public function index()
    {
        $this->checkAdmin();

        View::send(
            'admin/index',
            [
                'breadcrumbs' => [new Breadcrumb(Locale::get('dashboard'))],
            ]
        );
    }
}
