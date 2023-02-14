<?php

declare(strict_types=1);

namespace App\Controllers;

use Tischmann\Atlantis\{Breadcrumb, Controller, Locale, Request, Response, Template, View};

class AdminController extends Controller
{
    use ArticlesTrait;

    public function index(Request $request)
    {

        Response::send(
            View::make(
                view: 'admin/index',
                args: [
                    'breadcrumbs' => $this->renderBreadcrumbs(
                        [
                            new Breadcrumb(label: Locale::get('adminpanel')),
                        ]
                    ),
                    'admin' => $this->getAdminMenu(),
                ]
            )->render()
        );
    }

    public static function renderBreadcrumbs(array $breadcrumbs): string
    {
        $items = '';

        foreach ($breadcrumbs as $breadcrumb) {
            $items .= Template::make(
                template: $breadcrumb->url
                    ? 'admin/breadcrumb-url'
                    : 'admin/breadcrumb-span',
                args: [
                    'label' => $breadcrumb->label,
                    'url' => $breadcrumb->url,
                ]
            )->render();
        }

        return Template::make('admin/breadcrumbs', ['items' => $items])->render();
    }
}
