<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use Exception;
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
     * Загрузка изображения для статьи
     * 
     * @param Request $request
     */
    public function uploadImage()
    {
        $id = $this->route->args('id');

        $dir = $id ? "{$id}/image" : 'temp';

        try {
            $response = [
                'images' => Image::upload(
                    files: $_FILES,
                    path: getenv('APP_ROOT') . "/public/images/articles/{$dir}",
                    width: 1280,
                    height: 720,
                    max_width: 1280,
                    max_height: 720,
                    thumb_width: 320,
                    thumb_height: 180,
                    quality: 80
                )
            ];
        } catch (Exception $e) {
            Response::json(['message' => $e->getMessage()], 500);
        }

        Response::json($response);
    }

    /**
     * Удаление временного изображения
     *
     */
    public function deleteTempImage()
    {
        $request = Request::instance();

        $file = getenv('APP_ROOT') . "/public/images/articles/temp/{$request->request('image')}";

        try {
            if (file_exists($file)) unlink($file);
        } catch (Exception $e) {
            Response::json(['message' => $e->getMessage()], 500);
        }

        Response::json();
    }
}
