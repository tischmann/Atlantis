<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use Tischmann\Atlantis\{
    Controller,
    Pagination,
    Request,
    Response,
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
        $query = Article::query()
            ->where('visible', 1)
            ->where('moderated', 1)
            ->order('created_at', 'DESC');

        $pagination = new Pagination(query: $query, limit: 10);

        View::send(
            view: 'index',
            args: [
                'pagination' => $pagination,
                'articles' => Article::all($query)
            ]
        );
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

    /**
     * Поиск статей
     *
     * @return void
     */
    public function search(): void
    {
        $request = Request::instance();

        $value = urldecode(strval($request->request('query')));

        if (!$value) Response::redirect('/' . getenv('APP_LOCALE'));

        $query = Article::query()
            ->where(function (&$nested) use ($value) {
                $nested->orWhere('title', 'LIKE', "%$value%")
                    ->orWhere('short_text', 'LIKE', "%$value%")
                    ->orWhere('text', 'LIKE', "%$value%");
            })
            ->where('visible', 1)
            ->where('moderated', 1)
            ->order('created_at', 'DESC');

        if (!$query->count()) {
            View::send(
                view: 'error',
                args: [
                    'title' => get_str('articles_not_found'),
                    'code' => '404'
                ],
                code: 404,
                exit: true
            );
        }

        $pagination = new Pagination(query: $query, limit: 10);

        View::send(
            view: 'articles_by',
            args: [
                'label' => get_str('search_results') . ": \"{$value}\"",
                'pagination' => $pagination,
                'articles' => Article::all($query)
            ]
        );
    }
}
