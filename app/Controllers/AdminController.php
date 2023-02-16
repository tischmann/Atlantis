<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use Tischmann\Atlantis\{Breadcrumb, Controller, Locale, Pagination, Request, Response, Template, View};

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $this->checkAdmin();

        $items = '';

        $sections = [
            'categories' => [
                'label' => Locale::get('categories'),
                'url' => '/admin/categories',
                'icon' => 'fas fa-sitemap',
                'title' => Locale::get('categories'),
            ],
            'articles' => [
                'label' => Locale::get('articles'),
                'url' => '/admin/articles',
                'icon' => 'fas fa-newspaper',
                'title' => Locale::get('articles'),
            ],
        ];

        foreach ($sections as $item => $args) {
            $items .= Template::make(
                template: 'admin/index-item',
                args: $args
            )->render();
        }

        Response::send(
            View::make(
                view: 'admin/index',
                args: [
                    'breadcrumbs' => $this->renderBreadcrumbs(
                        [
                            new Breadcrumb(label: Locale::get('adminpanel')),
                        ]
                    ),
                    'items' => $items,
                    'app_title' => getenv('APP_TITLE') . " - " . Locale::get('adminpanel'),
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
                    ? 'breadcrumb-url'
                    : 'breadcrumb-span',
                args: [
                    'label' => $breadcrumb->label,
                    'url' => $breadcrumb->url,
                ]
            )->render();
        }

        return Template::make('breadcrumbs', ['items' => $items])->render();
    }

    public function fetchArticles(Request $request): void
    {
        $category_id = $request->route('category_id');

        $pagination = new Pagination();

        $html = '';

        $page = 1;

        $total = 0;

        $limit = Pagination::DEFAULT_LIMIT;

        if ($category_id) {
            $limit = $request->request('limit');

            $limit = intval($limit ?? Pagination::DEFAULT_LIMIT);

            $query = Article::query()
                ->where('category_id', $category_id)
                ->order('id', 'DESC');

            $total = $query->count();

            if ($total > $limit) {
                $page = intval($request->request('page') ?? 1);

                $offset = ($page - 1) * $limit;

                if ($limit) $query->limit($limit);

                if ($offset) $query->offset($offset);

                foreach (Article::fill($query) as $article) {
                    $html .= Template::make('admin/articles-item', [
                        'article_id' => $article->id,
                        'article_category_id' => $article->category_id,
                        'article_category_title' => $article->category_title,
                        'article_image_url' => $article->image_url,
                        'article_views' => $article->views,
                        'article_rating' => $article->rating,
                        'article_created_at' => $article->created_at,
                        'article_updated_at' => $article->updated_at,
                        'article_title' => $article->title,
                        'article_description' => $article->short_text,
                    ])->render();
                }
            }
        }

        $pagination = new Pagination(
            total: $total,
            page: $page,
            limit: $limit
        );

        Response::json([
            'status' => 1,
            'html' => $html,
            'page' => $pagination->page,
            'last' => $pagination->last,
        ]);
    }
}
