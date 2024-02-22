<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use Tischmann\Atlantis\{
    Controller,
    Image,
    Request,
    Response,
    View
};

/**
 * Главный контроллер
 */
class ArticlesController extends Controller
{
    /**
     * Вывод полной статьи
     *
     * @return void
     */
    public function showFullArticle(): void
    {
        $article = Article::find($this->route->args('id'));

        View::send('full_article', ['article' => $article]);
    }

    public function getArticleEditor(): void
    {
        $article = Article::find($this->route->args('id'));

        View::send('article_editor', ['article' => $article]);
    }

    /**
     * Загрузка изображений для статьи
     * 
     * @param Request $request
     */
    public function uploadImages()
    {
        $id = $this->route->args('id');

        Response::json([
            'images' => Image::upload(
                files: $_FILES,
                path: getenv('APP_ROOT') . "/public/images/articles/{$id}",
                width: 800,
                height: 600,
                max_width: 800,
                max_height: 600,
                thumb_width: 400,
                thumb_height: 300,
                quality: 80
            )
        ]);
    }
}
