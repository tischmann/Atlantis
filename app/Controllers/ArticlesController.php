<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Article;
use App\Models\Category;
use Exception;

use Tischmann\Atlantis\{
    App,
    Controller,
    DateTime,
    Image,
    Locale,
    Pagination,
    Request,
    Response,
    View
};

/**
 * Главный контроллер
 */
class ArticlesController extends Controller
{
    public function showArticlesByTag(): void
    {
        $tag = $this->route->args('tag');

        if (!$tag) {
            View::send(
                view: 'error',
                args: [
                    'title' => get_str('not_found'),
                    'code' => '404'
                ],
                code: 404,
                exit: true
            );
        }

        $query = Article::query()
            ->where('tags', '[]', $tag)
            ->order('created_at', 'DESC');

        $pagination = new Pagination(query: $query, limit: 12);

        View::send(
            view: 'articles_by_tag',
            args: [
                'tag' => $tag,
                'pagination' => $pagination,
                'articles' => Article::all($query)
            ]
        );
    }

    protected function checkEditRights(
        string $type = 'html',
        ?Article $article = null
    ): mixed {
        $user = App::getCurrentUser();

        $is_admin = $user->isAdmin();

        $is_author = $user->isAuthor();

        $is_moderator = $user->isModerator();

        $result = match (true) {
            $is_admin => true,
            $is_author => $article->exists() ? $article->author_id === $user->id : true,
            $is_moderator => true,
            default => false
        };

        switch (mb_strtolower($type)) {
            case 'json':
                if (!$result) {
                    Response::json(
                        response: [
                            'title' => get_str('warning'),
                            'message' => get_str('access_denied')
                        ],
                        code: 403
                    );
                }
                break;
            case 'bool':
                return $result;
            default:
                if (!$result) {
                    View::send(
                        view: 'error',
                        layout: 'default',
                        exit: true,
                        code: 403,
                        args: [
                            'title' => get_str('access_denied'),
                            'code' => '403'
                        ],
                    );
                }
                break;
        }

        return null;
    }

    public function showAllArticles(): void
    {
        $this->checkEditRights(type: 'html');

        $user = App::getCurrentUser();

        $request = Request::instance();

        $category_id = mb_strtolower(strval($request->request('category_id') ?? 'all'));

        $visible = strval($request->request('visible'));

        $locale = strval($request->request('locale'));

        $fixed = strval($request->request('fixed'));

        $moderated = strval($request->request('moderated'));

        $order_types = [
            'created_at',
            'title',
            'visible',
            'fixed'
        ];

        $order = strval($request->request('order') ?? 'created_at');

        $order = in_array($order, $order_types) ? $order : 'created_at';

        $direction_types = [
            'asc',
            'desc'
        ];

        $direction = strval($request->request('direction') ?? 'desc');

        $direction = mb_strtolower($direction);

        $direction = in_array($direction, ['asc', 'desc']) ? $direction : 'desc';

        $category = null;

        if ($category_id === 'all') {
            $category = new Category();
        } else if ($category_id !== "") {
            $category = Category::find($category_id);
        }

        $order_options = [];

        foreach ($order_types as $type) {
            $order_options[] = [
                'value' => $type,
                'text' => get_str("article_order_{$type}"),
                'selected' => $order === $type,
                'level' => '0'
            ];
        }

        $direction_options = [];

        foreach ($direction_types as $type) {
            $direction_options[] = [
                'value' => $type,
                'text' => get_str("direction_{$type}"),
                'selected' => $direction === $type,
                'level' => '0'
            ];
        }

        $locale_options = [];

        foreach (['', ...Locale::available()] as $value) {
            $locale_options[] = [
                'value' => $value,
                'text' => get_str("locale_{$value}"),
                'selected' => $locale === $value,
                'level' => '0'
            ];
        }

        $category_options = [
            [
                'value' => 'all',
                'text' => get_str('article_category_all'),
                'selected' => $category !== null && $category?->id === 0,
                'level' => 0
            ],
            [
                'value' => '',
                'text' => '',
                'selected' => $category === null,
                'level' => 0
            ]
        ];

        foreach (Category::getAllCategories(locale: $locale, recursive: false) as $value) {
            assert($value instanceof Category);
            $category_options = [
                ...$category_options,
                ...get_category_options($value, intval($category?->id))
            ];
        }

        $visible_types = [
            "" => "all",
            "0" => "invisible",
            "1" => "visible"
        ];

        $visible_options = [];

        foreach ($visible_types as $key => $value) {
            $visible_options[] = [
                'value' => $key,
                'text' => get_str("article_visible_{$value}"),
                'selected' => $visible == $key,
                'level' => 0
            ];
        }

        $fixed_types = [
            "" => "all",
            "0" => "off",
            "1" => "on"
        ];

        $fixed_options = [];

        foreach ($fixed_types as $key => $value) {
            $fixed_options[] = [
                'value' => $key,
                'text' => get_str("article_fixed_{$value}"),
                'selected' => $fixed == $key,
                'level' => 0
            ];
        }

        $moderated_types = [
            "" => "all",
            "0" => "no",
            "1" => "yes"
        ];

        $moderated_options = [];

        foreach ($moderated_types as $key => $value) {
            $moderated_options[] = [
                'value' => $key,
                'text' => get_str("article_moderated_{$value}"),
                'selected' => $moderated == $key,
                'level' => 0
            ];
        }

        // Query

        $query = Article::query()
            ->order($order, $direction);

        if ($user->isAuthor()) {
            $query->where('author_id', $user->id);
        }

        if ($category === null) {
            $query->where('category_id', null);
        } else if ($category?->id) {
            $query->where('category_id', $category?->id);
        }

        if ($visible !== "") {
            $query->where('visible', $visible);
        }

        if ($locale !== "") {
            $query->where('locale', $locale);
        }

        if ($fixed !== "") {
            $query->where('fixed', $fixed);
        }

        if ($moderated !== "") {
            $query->where('moderated', $moderated);
        }

        $pagination = new Pagination(query: $query, limit: 12);

        $articles = Article::all($query);

        View::send(
            view: 'articles_list',
            args: [
                'pagination' => $pagination,
                'articles' => $articles,
                'category_id' => $category_id,
                'category' => $category,
                'order' => $order,
                'direction' => $direction,
                'visible' => $visible,
                'locale' => $locale,
                'fixed' => $fixed,
                'order_options' => $order_options,
                'direction_options' => $direction_options,
                'locale_options' => $locale_options,
                'category_options' => $category_options,
                'visible_options' => $visible_options,
                'fixed_options' => $fixed_options,
                'moderated_options' => $moderated_options,
            ]
        );
    }

    /**
     * Вывод полной статьи
     *
     * @return void
     */
    public function showFullArticle(): void
    {
        $url = $this->route->args('url');

        $url = rtrim($url, '.html');

        $article = Article::find($url, 'url');

        if (!$article->exists()) {
            View::send(
                view: 'error',
                layout: 'default',
                code: 404,
                args: [
                    'title' => get_str('not_found'),
                    'code' => '404'
                ]
            );
        }

        View::send('full_article', ['article' => $article]);
    }

    public function getArticleEditor(): void
    {
        $article = Article::find($this->route->args('id'));

        $this->checkEditRights(type: 'html', article: $article);

        $category = $article->getCategory();

        $category_options = [
            [
                'value' => '',
                'text' => '',
                'selected' => !$category->id,
                'level' => 0
            ]
        ];

        foreach (Category::getAllCategories(locale: $article->locale, recursive: false) as $value) {
            assert($value instanceof Category);
            $category_options = [
                ...$category_options,
                ...get_category_options($value, $category->id)
            ];
        }

        $image_sizes_options = [
            [
                'value' => '16_9',
                'text' => "16:9",
                'selected' => true,
                'level' => 0
            ],
            [
                'value' => '4_3',
                'text' => "4:3",
                'selected' => false,
                'level' => 0
            ],
            [
                'value' => '1_1',
                'text' => "1:1",
                'selected' => false,
                'level' => 0
            ]
        ];

        View::send(
            view: 'article_editor',
            args: [
                'article' => $article,
                'category_options' => $category_options,
                'image_sizes_options' => $image_sizes_options
            ],
            layout: 'default'
        );
    }

    /**
     * Загрузка изображения для статьи
     * 
     */
    public function uploadImage()
    {
        $this->checkAdmin(type: 'json');

        Article::removeOldTempImagesAndUploads();

        try {
            $request = Request::instance();

            $size = $request->request('size') ?? '16_9';

            $width = Article::IMAGE_WIDTH;

            $height = match ($size) {
                '4_3' => intval($width / 4 * 3),
                '1_1' => $width,
                default => intval($width / 16 * 9)
            };

            $thumb_width = Article::IMAGE_THUMB_WIDTH;

            $thumb_height = match ($size) {
                '4_3' => intval($thumb_width / 4 * 3),
                '1_1' => $thumb_width,
                default => intval($thumb_width / 16 * 9)
            };

            $images = Image::upload(
                files: $_FILES,
                path: getenv('APP_ROOT') . "/public/images/articles/temp",
                min_width: $width,
                min_height: $height,
                max_width: $width,
                max_height: $height,
                thumb_width: $thumb_width,
                thumb_height: $thumb_height,
                quality: 80
            );

            Response::json(['image' => reset($images)]);
        } catch (Exception $e) {
            Response::json(['title' => get_str('warning'), 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Загрузка изображениий для статьи
     * 
     */
    public function uploadImages()
    {
        $this->checkAdmin(type: 'json');

        Article::removeOldTempImagesAndUploads();

        try {
            $request = Request::instance();

            $size = $request->request('size') ?? '16_9';

            $image = $_FILES['image'];

            $sizes = getimagesize($image['tmp_name']);

            $width = $sizes[0];

            $height = $sizes[1];

            $ratio = $width / $height;

            $thumb_width = Article::IMAGE_THUMB_WIDTH;

            $thumb_height = intval($thumb_width / $ratio);

            $images = Image::upload(
                files: $_FILES,
                path: getenv('APP_ROOT') . "/public/images/articles/temp",
                min_width: $width,
                min_height: $height,
                max_width: $width,
                max_height: $height,
                thumb_width: $thumb_width,
                thumb_height: $thumb_height,
                quality: 80
            );

            Response::json(['src' => "/images/articles/temp/" . reset($images)]);
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
        $this->checkAdmin(type: 'json');

        Article::removeOldTempImagesAndUploads();

        try {
            $request = Request::instance();

            $size = $request->request('size') ?? '16_9';

            $width = Article::IMAGE_WIDTH;

            $height = match ($size) {
                '4_3' => intval($width / 4 * 3),
                '1_1' => $width,
                default => intval($width / 16 * 9)
            };

            $thumb_width = Article::IMAGE_THUMB_WIDTH;

            $thumb_height = match ($size) {
                '4_3' => intval($thumb_width / 4 * 3),
                '1_1' => $thumb_width,
                default => intval($thumb_width / 16 * 9)
            };

            Response::json([
                'images' => Image::upload(
                    files: $_FILES,
                    path: getenv('APP_ROOT') . "/public/images/articles/temp",
                    min_width: $width,
                    min_height: $height,
                    max_width: $width,
                    max_height: $height,
                    thumb_width: $thumb_width,
                    thumb_height: $thumb_height,
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
        $this->checkAdmin(type: 'json');

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
                    throw new Exception(get_str('temp_file_not_moved'), 500);
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
        $this->checkAdmin(type: 'json');

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
                    throw new Exception(get_str('temp_file_not_moved'), 500);
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
        $this->checkAdmin(type: 'json');

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
     * Удаление категории
     */
    public function deleteArticle()
    {
        $this->checkAdmin(type: 'json');

        try {
            $id = $this->route->args('id');

            $article = Article::find($id);

            if (!$article->exists()) {
                throw new Exception(get_str('article_not_found'), 404);
            }

            if (!$article->delete()) {
                throw new Exception(get_str('not_deleted'), 500);
            }

            Response::json([
                'title' => get_str('attention'),
                'message' => get_str('deleted')
            ]);
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Добавление статьи
     */
    public function insertArticle()
    {
        $this->checkAdmin(type: 'json');

        $this->updateArticle();
    }

    /**
     * Изменение статьи
     */
    public function updateArticle()
    {
        try {
            $user = App::getCurrentUser();

            $request = Request::instance();

            $request->validate([
                'category_id' => ['required', 'string'],
                'image' => ['required', 'string'],
                'gallery' => ['required', 'string'],
                'videos' => ['required', 'string'],
                'attachements' => ['required', 'string'],
                'title' => ['required', 'string'],
                'locale' => ['required', 'string'],
                'short_text' => ['required', 'string'],
                'text' => ['required', 'string'],
                'tags' => ['required', 'string'],
                'created_at' => ['required', 'string'],
                'visible' => ['required', 'string'],
                'fixed' => ['required', 'string']
            ]);

            $id = $this->route->args('id');

            $article = new Article();

            if ($id) {
                $article = Article::find($id);

                if (!$article->exists()) {
                    throw new Exception(get_str('article_not_found') . ":{$id}", 404);
                }
            }

            $this->checkEditRights(type: 'json', article: $article);

            $article->title = $request->request('title');

            if (!$article->title) {
                throw new Exception(get_str('field_required') . ": title", 400);
            }

            $article->locale = $request->request('locale');

            if (!$article->locale) {
                throw new Exception(get_str('field_required') . ": locale", 400);
            }

            $article->text = html_entity_decode($request->request('text'));

            if (!$article->text) {
                throw new Exception(get_str('field_required') . ": text", 400);
            }

            $article->short_text = $request->request('short_text');

            if (!$article->short_text) {
                throw new Exception(get_str('field_required') . ": short_text", 400);
            }

            $article->category_id = intval($request->request('category_id'));

            if (!$article->category_id) {
                throw new Exception(get_str('field_required') . ": category_id", 400);
            }

            $article->tags = explode(",", $request->request('tags'));

            $article->tags = array_map('trim', $article->tags);

            $article->visible = boolval($request->request('visible'));

            $article->fixed = boolval($request->request('fixed'));

            if ($user->canModerate()) {
                $article->moderated = boolval($request->request('moderated') ?? false);
            }

            $date = $request->request('created_at');

            if (!DateTime::validate($date)) {
                throw new Exception(get_str('article_created_at_invalid'), 400);
            }

            $article->created_at = new DateTime($date);

            if (!$article->exists()) {
                $article->author_id = App::getCurrentUser()->id;

                if (!$article->save()) {
                    throw new Exception(get_str('not_saved'));
                }
            }

            $this->updateArticleImages(
                article: $article
            );

            $this->updateArticleImage(
                article: $article,
                image: $request->request('image')
            );

            $images = $request->request('gallery');

            $images = $images ? explode(";", $images) : [];

            $this->updateArticleGallery(
                article: $article,
                images: $images
            );

            $videos = $request->request('videos');

            $videos = $videos ? explode(";", $videos) : [];

            $this->updateArticleVideos(
                article: $article,
                videos: $videos
            );

            $attachements = $request->request('attachements');

            $attachements = $attachements ? explode(";", $attachements) : [];

            $this->updateArticleAttachements(
                article: $article,
                attachements: $attachements
            );

            $article->url = $article->createUrl();

            if (!$article->save()) {
                throw new Exception(get_str('not_saved'));
            }

            Article::removeOldTempImagesAndUploads();

            Response::json([
                'title' => get_str('attention'),
                'message' => get_str('saved'),
                'id' => $article->id
            ]);
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    protected function updateArticleImages(Article $article): self
    {
        if (!preg_match_all('/<img[^>]+>/i', $article->text, $matches)) {
            return $this;
        }

        $article_dir = getenv('APP_ROOT') . "/public/images/articles/{$article->id}";

        $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

        $images_dir = "{$article_dir}/images";

        if (!is_dir($images_dir)) mkdir($images_dir, 0775, true);

        $old_article = Article::find($article->id);

        preg_match_all('/<img[^>]+>/i', $old_article->text, $old_matches);

        $old_images = [];

        foreach ($old_matches[0] as $match) {
            if (preg_match('/src="([^"]+)"/i', $match, $src)) {
                $old_images[] = basename($src[1]);
            }
        }

        $all_images = [];

        $images = [];

        foreach ($matches[0] as $match) {
            if (preg_match('/src="([^"]+)"([^>]+)>/i', $match, $src)) {
                $image = basename($src[1]);

                $all_images[] = $image;

                if (in_array($image, $old_images)) continue;

                $images[] = $image;

                $article->text = str_replace($src[1], "/images/articles/{$article->id}/images/{$image}", $article->text);
            }
        }

        // Перемещение новых изображений

        foreach ($images as $image) {
            $temp_path = "{$temp_dir}/{$image}";

            $new_path = "{$images_dir}/{$image}";

            $thumb_path = "{$images_dir}/thumb_{$image}";

            $paths = [
                $new_path => $temp_path,
                $thumb_path => "{$temp_dir}/thumb_{$image}"
            ];

            foreach ($paths as $new_path => $temp_path) {
                if (!file_exists($temp_path)) {
                    throw new Exception(get_str('temp_file_not_found') . ":{$temp_path}");
                }

                if (!rename($temp_path, $new_path)) {
                    throw new Exception(get_str('temp_file_not_moved') . ":{$temp_path}");
                }
            }
        }

        // Удаление старых изображений

        foreach (array_diff($old_images, $all_images) as $image) {
            $thumb = "thumb_{$image}";

            if (file_exists("{$images_dir}/{$image}")) {
                unlink("{$images_dir}/{$image}");
            }

            if (file_exists("{$images_dir}/{$thumb}")) {
                unlink("{$images_dir}/{$thumb}");
            }
        }

        return $this;
    }

    protected function updateArticleImage(Article $article, string $image): self
    {
        $article_dir = getenv('APP_ROOT') . "/public/images/articles/{$article->id}";

        $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

        $image_dir = "{$article_dir}/image";

        if (!is_dir($image_dir)) mkdir($image_dir, 0775, true);

        $old_files = glob("{$image_dir}/*.webp");

        if (!$image) {
            foreach (glob("{$article_dir}/image/*.webp") as $file) {
                if (file_exists($file)) unlink($file);
            }
        }

        if (file_exists("{$article_dir}/image/{$image}")) {
            return $this;
        }

        $paths = [
            "{$image_dir}/{$image}" => "{$temp_dir}/{$image}",
            "{$image_dir}/thumb_{$image}" => "{$temp_dir}/thumb_{$image}"
        ];

        foreach ($paths as $new_path => $temp_path) {
            if (!file_exists($temp_path)) {
                throw new Exception(get_str('temp_file_not_found'));
            }

            if (!rename($temp_path, $new_path)) {
                throw new Exception(get_str('temp_file_not_moved'));
            }
        }

        foreach ($old_files as $file) {
            if (file_exists($file)) unlink($file);
        }

        return $this;
    }

    protected function updateArticleGallery(Article $article, array $images): self
    {
        $article_dir = getenv('APP_ROOT') . "/public/images/articles/{$article->id}";

        $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

        $gallery_dir = "{$article_dir}/gallery";

        if (!is_dir($gallery_dir)) mkdir($gallery_dir, 0775, true);

        $old_files = glob("{$article_dir}/gallery/thumb_*.webp");

        if (!$images) { // Удаление всех файлов
            foreach (glob("{$article_dir}/gallery/*.webp") as $file) {
                if (file_exists($file)) unlink($file);
            }
        } else { // Перемещение новых файлов
            foreach ($images as $image) {
                if (file_exists("{$gallery_dir}/{$image}")) continue;

                $image = str_replace('thumb_', '', $image);

                $temp_path = "{$temp_dir}/{$image}";

                $new_path = "{$gallery_dir}/{$image}";

                $thumb_path = "{$gallery_dir}/thumb_{$image}";

                $paths = [
                    $new_path => $temp_path,
                    $thumb_path => "{$temp_dir}/thumb_{$image}"
                ];

                foreach ($paths as $new_path => $temp_path) {
                    if (!file_exists($temp_path)) {
                        throw new Exception(get_str('temp_file_not_found'));
                    }

                    if (!rename($temp_path, $new_path)) {
                        throw new Exception(get_str('temp_file_not_moved'));
                    }
                }
            }

            // Сортировка файлов по времени

            foreach ($images as $key => $image) {
                $image = str_replace('thumb_', '', $image);

                $timestamp = time() + $key;

                touch("{$gallery_dir}/{$image}", $timestamp);

                touch("{$gallery_dir}/thumb_{$image}", $timestamp);
            }

            // Удаление старых файлов

            foreach ($old_files as $image) {
                $filename = basename($image);

                $filename = str_replace('thumb_', '', $filename);

                if (in_array($filename, $images)) continue;

                unlink($image);

                unlink(str_replace('thumb_', '', $image));
            }
        }

        return $this;
    }

    protected function updateArticleVideos(Article $article, array $videos): self
    {
        $uploads_dir = getenv('APP_ROOT') . "/public/uploads/articles/{$article->id}";

        $uploads_temp_dir = getenv('APP_ROOT') . "/public/uploads/articles/temp";

        $videos_dir = "{$uploads_dir}/video";

        if (!is_dir($videos_dir)) mkdir($videos_dir, 0775, true);

        $old_videos = glob("{$videos_dir}/*.*");

        if (!$videos) {
            foreach ($old_videos as $file) {
                if (file_exists($file)) unlink($file);
            }
        } else {
            foreach ($videos as $video) {
                if (file_exists("{$videos_dir}/{$video}")) continue;

                $temp_path = "{$uploads_temp_dir}/{$video}";

                $new_path = "{$videos_dir}/{$video}";

                if (!file_exists($temp_path)) {
                    throw new Exception(get_str('temp_file_not_found'));
                }

                if (!rename($temp_path, $new_path)) {
                    throw new Exception(get_str('temp_file_not_moved'));
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

        return $this;
    }

    protected function updateArticleAttachements(Article $article, array $attachements): self
    {
        $uploads_dir = getenv('APP_ROOT') . "/public/uploads/articles/{$article->id}";

        $uploads_temp_dir = getenv('APP_ROOT') . "/public/uploads/articles/temp";

        $attachements_dir = "{$uploads_dir}/attachements";

        if (!is_dir($attachements_dir)) mkdir($attachements_dir, 0775, true);

        $old_attachements = glob("{$attachements_dir}/*.*");

        if (!$attachements) {
            foreach ($old_attachements as $file) {
                if (file_exists($file)) unlink($file);
            }
        } else {
            foreach ($attachements as $attachement) {
                if (file_exists("{$attachements_dir}/{$attachement}")) continue;

                $temp_path = "{$uploads_temp_dir}/{$attachement}";

                $new_path = "{$attachements_dir}/{$attachement}";

                if (!file_exists($temp_path)) continue;

                if (!rename($temp_path, $new_path)) {
                    throw new Exception(get_str('temp_file_not_moved'));
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

        return $this;
    }
}
