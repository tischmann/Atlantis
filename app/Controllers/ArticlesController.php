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

        $files = [
            getenv('APP_ROOT') . "/public/images/articles/temp/{$request->request('image')}",
            getenv('APP_ROOT') . "/public/images/articles/temp/thumb_{$request->request('image')}"
        ];

        try {
            foreach ($files as $file) {
                if (file_exists($file)) unlink($file);
            }
        } catch (Exception $e) {
            Response::json(['message' => $e->getMessage()], 500);
        }

        Response::json();
    }

    /**
     * Изменение статьи
     *
     */
    public function updateArticle()
    {
        if (csrf_failed()) {
            Response::json(['message' => get_str('csrf_failed')], 403);
        }

        $request = Request::instance();

        $request->validate([
            'image' => ['required', 'string'],
            'title' => ['required', 'string'],
            'text' => ['required', 'string']
        ]);

        $id = $this->route->args('id');

        $article = Article::find($id);

        if (!$article->exists()) {
            Response::json([
                'message' => get_str('article_not_found') . ": {$id}"
            ], 404);
        }

        try {
            $image = $request->request('image');

            $article_dir = getenv('APP_ROOT') . "/public/images/articles/{$id}";

            $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

            $image_dir = "{$article_dir}/image";

            if (!$image) {
                foreach (glob("{$article_dir}/image/*.webp") as $file) {
                    if (file_exists($file)) unlink($file);
                }
            } else if (!file_exists("{$image_dir}/{$image}")) {
                $old_files = glob("{$image_dir}/*.webp");

                $image_dir = "{$article_dir}/image";

                $paths = [
                    "{$image_dir}/{$image}" => "{$temp_dir}/{$image}",
                    "{$image_dir}/thumb_{$image}" => "{$temp_dir}/thumb_{$image}"
                ];

                if (!is_dir($image_dir)) mkdir($image_dir, 0775, true);

                foreach ($paths as $new_path => $temp_path) {
                    if (!file_exists($temp_path)) continue;

                    if (!rename($temp_path, $new_path)) {
                        throw new Exception(get_str('article_temp_image_not_moved'));
                    }
                }

                foreach ($old_files as $file) {
                    if (file_exists($file)) unlink($file);
                }
            }

            $article->title = $request->request('title');

            $article->text = $request->request('text');

            if (!$article->save()) {
                throw new Exception(get_str('article_not_saved'));
            }
        } catch (Exception $e) {
            Response::json(['message' => $e->getMessage()], 500);
        }

        Response::json();
    }
}
