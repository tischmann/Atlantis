<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Breadcrumb, Controller, Locale, Request, Response, View};

class AdminController extends Controller
{
    use ArticlesTrait;

    public function index(Request $request)
    {
        Response::send(View::make(view: 'admin/index', args: [
            'breadcrumbs' => [
                new Breadcrumb(label: Locale::get('adminpanel')),
            ],
        ])->render());
    }
}
