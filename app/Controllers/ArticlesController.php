<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;

use Exception;

use Tischmann\Atlantis\{
    App,
    Controller,
    DateTime,
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

    protected function adminCheckJson(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            Response::json([
                'title' => get_str('warning'),
                'message' => get_str('access_denied')
            ], 403);
        }
    }

    protected function adminCheck(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            View::send('403', exit: true);
        }
    }

    protected function adminCheckException(): void
    {
        if (!App::getCurrentUser()->isAdmin()) {
            throw new Exception(get_str('access_denied'), 403);
        }
    }

    public function showAllArticles(): void
    {
        $this->adminCheck();

        View::send('articles_list');
    }

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
        $this->adminCheck();

        $article = Article::find($this->route->args('id'));

        View::send('article_editor', ['article' => $article], 'default');
    }

    /**
     * Загрузка изображения для статьи
     * 
     */
    public function uploadImage()
    {
        $this->adminCheckJson();

        Article::removeOldTempImagesAndUploads();

        try {
            $images = Image::upload(
                files: $_FILES,
                path: getenv('APP_ROOT') . "/public/images/articles/temp",
                min_width: 1280,
                min_height: 720,
                max_width: 1280,
                max_height: 720,
                thumb_width: 320,
                thumb_height: 180,
                quality: 80
            );

            Response::json(['image' => reset($images)]);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Загрузка изображения галереи для статьи
     * 
     */
    public function uploadGalleryImage()
    {
        $this->adminCheckJson();

        Article::removeOldTempImagesAndUploads();

        try {
            Response::json([
                'images' => Image::upload(
                    files: $_FILES,
                    path: getenv('APP_ROOT') . "/public/images/articles/temp",
                    min_width: 1280,
                    min_height: 720,
                    max_width: 1280,
                    max_height: 720,
                    thumb_width: 320,
                    thumb_height: 180,
                    quality: 80
                ),
            ]);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Загрузка видео для статьи
     *
     */
    public function uploadVideos()
    {
        $this->adminCheckJson();

        Article::removeOldTempImagesAndUploads();

        try {
            $response = ['videos' => []];

            $tmp_names = $_FILES['video']['tmp_name'] ?? [];

            if (!is_array($tmp_names)) $tmp_names = [$tmp_names];

            $names = $_FILES['video']['name'] ?? [];

            if (!is_array($names)) $names = [$names];

            $errors = $_FILES['video']['error'] ?? [];

            if (!is_array($errors)) $errors = [$errors];

            foreach ($errors as $index => $error) {
                if ($error !== UPLOAD_ERR_OK) {
                    throw new Exception(get_str('upload_error') . ":{$error}", 500);
                }
            }

            foreach ($tmp_names as $index => $tmp_name) {
                if (!$tmp_name) continue;

                $name = $names[$index];

                $extension = explode('.', $name);

                $extension = end($extension);

                $filename = md5(bin2hex(random_bytes(32))) . ".{$extension}";

                if (!rename($tmp_name, getenv('APP_ROOT') . "/public/uploads/articles/temp/{$filename}")) {
                    throw new Exception(get_str('article_temp_file_not_moved'), 500);
                }

                $response['videos'][] = $filename;
            }

            Response::json($response);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Загрузка вложений для статьи
     *
     */
    public function uploadAttachements()
    {
        $this->adminCheckJson();

        Article::removeOldTempImagesAndUploads();

        try {
            $response = ['files' => []];

            $tmp_names = $_FILES['file']['tmp_name'] ?? [];

            if (!is_array($tmp_names)) $tmp_names = [$tmp_names];

            $names = $_FILES['file']['name'] ?? [];

            if (!is_array($names)) $names = [$names];

            $errors = $_FILES['file']['error'] ?? [];

            if (!is_array($errors)) $errors = [$errors];

            foreach ($errors as $index => $error) {
                if ($error !== UPLOAD_ERR_OK) {
                    throw new Exception(get_str('upload_error') . ":{$error}", 500);
                }
            }

            foreach ($tmp_names as $index => $tmp_name) {
                if (!$tmp_name) continue;

                $filename = $names[$index];

                $filename = mb_strtolower(preg_replace('/\s/', '_', $filename));

                if (!rename($tmp_name, getenv('APP_ROOT') . "/public/uploads/articles/temp/{$filename}")) {
                    throw new Exception(get_str('article_temp_file_not_moved'), 500);
                }

                $response['files'][] = $filename;
            }

            Response::json($response);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Удаление временного изображения
     *
     */
    public function deleteTempImage()
    {
        $this->adminCheckJson();

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
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }

        Response::json();
    }

    /**
     * Изменение статьи
     *
     */
    public function updateArticle()
    {
        $this->adminCheckJson();

        $request = Request::instance();

        $request->validate([
            'category_id' => ['required', 'string'],
            'image' => ['required', 'string'],
            'gallery' => ['required', 'string'],
            'videos' => ['required', 'string'],
            'attachements' => ['required', 'string'],
            'title' => ['required', 'string'],
            'short_text' => ['required', 'string'],
            'text' => ['required', 'string'],
            'tags' => ['required', 'string'],
            'created_at' => ['required', 'string'],
            'visible' => ['required', 'string'],
            'fixed' => ['required', 'string']
        ]);

        $id = $this->route->args('id');

        $article = Article::find($id);

        if (!$article->exists()) {
            Response::json([
                'title' => get_str('warning'),
                'message' => get_str('article_not_found') . ": {$id}"
            ], 404);
        }

        try {
            $article->title = $request->request('title');

            if (!$article->title) {
                throw new Exception(get_str('article_title_required'), 400);
            }

            $article->text = html_entity_decode($request->request('text'));

            if (!$article->text) {
                throw new Exception(get_str('article_text_required'), 400);
            }

            $article->short_text = $request->request('short_text');

            if (!$article->short_text) {
                throw new Exception(get_str('article_short_text_required'), 400);
            }

            $article->category_id = intval($request->request('category_id'));

            if (!$article->category_id) {
                throw new Exception(get_str('article_category_required'), 400);
            }

            $image = $request->request('image');

            $article_dir = getenv('APP_ROOT') . "/public/images/articles/{$id}";

            $uploads_dir = getenv('APP_ROOT') . "/public/uploads/articles/{$id}";

            $videos_dir = "{$uploads_dir}/video";

            $attachements_dir = "{$uploads_dir}/attachements";

            $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

            $uploads_temp_dir = getenv('APP_ROOT') . "/public/uploads/articles/temp";

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

            $old_files = glob("{$article_dir}/gallery/thumb_*.webp");

            $gallery = $request->request('gallery');

            $gallery = $gallery ? explode(";", $gallery) : [];

            if (!$gallery) {
                foreach (glob("{$article_dir}/gallery/*.webp") as $file) {
                    if (file_exists($file)) unlink($file);
                }
            } else {
                $gallery_dir = "{$article_dir}/gallery";

                if (!is_dir($gallery_dir)) mkdir($gallery_dir, 0775, true);

                foreach ($gallery as $image) {
                    $image = str_replace('thumb_', '', $image);

                    if (file_exists("{$gallery_dir}/{$image}")) continue;

                    $temp_path = "{$temp_dir}/{$image}";

                    $new_path = "{$gallery_dir}/{$image}";

                    $thumb_path = "{$gallery_dir}/thumb_{$image}";

                    $paths = [
                        $new_path => $temp_path,
                        $thumb_path => "{$temp_dir}/thumb_{$image}"
                    ];

                    foreach ($paths as $new_path => $temp_path) {
                        if (!file_exists($temp_path)) continue;

                        if (!rename($temp_path, $new_path)) {
                            throw new Exception(get_str('article_temp_file_not_moved'));
                        }
                    }
                }

                foreach ($gallery as $key => $image) {
                    $image = str_replace('thumb_', '', $image);
                    $timestamp = time() + $key;
                    touch("{$gallery_dir}/{$image}", $timestamp);
                    touch("{$gallery_dir}/thumb_{$image}", $timestamp);
                }

                foreach ($old_files as $image) {
                    $filename = basename($image);

                    $filename = str_replace('thumb_', '', $filename);

                    if (in_array($filename, $gallery)) continue;

                    if (file_exists($image)) unlink($image);

                    $image = str_replace('thumb_', '', $image);

                    if (file_exists($image)) unlink($image);
                }
            }

            $old_videos = glob("{$videos_dir}/*.*");

            $videos = $request->request('videos');

            $videos = $videos ? explode(";", $videos) : [];

            if (!is_dir($videos_dir)) mkdir($videos_dir, 0775, true);

            if (!$videos) {
                foreach (glob("{$videos_dir}/*.*") as $file) {
                    if (file_exists($file)) unlink($file);
                }
            } else {
                foreach ($videos as $video) {
                    if (file_exists("{$videos_dir}/{$video}")) continue;

                    $temp_path = "{$uploads_temp_dir}/{$video}";

                    $new_path = "{$videos_dir}/{$video}";

                    $paths = [
                        $new_path => $temp_path
                    ];

                    foreach ($paths as $new_path => $temp_path) {
                        if (!file_exists($temp_path)) continue;

                        if (!rename($temp_path, $new_path)) {
                            throw new Exception(get_str('article_temp_file_not_moved'));
                        }
                    }
                }

                foreach ($videos as $key => $video) {
                    $timestamp = time() + $key;
                    touch("{$videos_dir}/{$video}", $timestamp);
                }

                foreach ($old_videos as $video) {
                    $filename = basename($video);

                    if (in_array($filename, $videos)) continue;

                    if (file_exists($video)) unlink($video);
                }
            }

            $old_attachements = glob("{$attachements_dir}/*.*");

            $attachements = $request->request('attachements');

            $attachements = $attachements ? explode(";", $attachements) : [];

            if (!is_dir($attachements_dir)) mkdir($attachements_dir, 0775, true);

            if (!$attachements) {
                foreach (glob("{$attachements_dir}/*.*") as $file) {
                    if (file_exists($file)) unlink($file);
                }
            } else {
                foreach ($attachements as $attachement) {
                    if (file_exists("{$attachements_dir}/{$attachement}")) continue;

                    $temp_path = "{$uploads_temp_dir}/{$attachement}";

                    $new_path = "{$attachements_dir}/{$attachement}";

                    $paths = [
                        $new_path => $temp_path
                    ];

                    foreach ($paths as $new_path => $temp_path) {
                        if (!file_exists($temp_path)) continue;

                        if (!rename($temp_path, $new_path)) {
                            throw new Exception(get_str('article_temp_file_not_moved'));
                        }
                    }
                }

                foreach ($attachements as $key => $attachement) {
                    $timestamp = time() + $key;
                    touch("{$attachements_dir}/{$attachement}", $timestamp);
                }

                foreach ($old_attachements as $attachement) {
                    $filename = basename($attachement);

                    if (in_array($filename, $attachements)) continue;

                    if (file_exists($attachement)) unlink($attachement);
                }
            }

            $article->tags = explode(",", $request->request('tags'));

            $article->tags = array_map('trim', $article->tags);

            $article->visible = boolval($request->request('visible'));

            $article->fixed = boolval($request->request('fixed'));

            $date = $request->request('created_at');

            if (!DateTime::validate($date)) {
                throw new Exception(get_str('article_created_at_invalid'), 400);
            }

            $article->created_at = new DateTime($date);

            if (!$article->save()) {
                throw new Exception(get_str('article_not_saved'));
            }

            Article::removeOldTempImagesAndUploads();

            Response::json(['title' => get_str('attention'), 'message' => get_str('article_saved')]);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}
