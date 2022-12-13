<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Controller, Locale, Response, View};

class AdminController extends Controller
{
    use ArticlesTrait;

    public function index(): void
    {
        $this->checkAdmin();

        $view = View::make(
            'admin/index',
            [
                'breadcrumbs' => [
                    ['title' => Locale::get('adminpanel'), 'href' => '']
                ]
            ]
        );

        Response::send($view->render());
    }
}
