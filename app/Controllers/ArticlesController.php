<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Article, Category, Rating, User};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    CSRF,
    Image,
    Locale,
    Pagination,
    Query,
    Request,
    Response,
    Sorting,
    Template,
    View
};

class ArticlesController extends Controller
{
    public const ADMIN_FETCH_LIMIT = 10;

    public const FETCH_LIMIT = 10;
    /**
     * Вывод списка статей в админпанели
     */
    public function index(Request $request): void
    {
        $this->checkAdmin();

        $query = Article::query()->limit(static::ADMIN_FETCH_LIMIT);

        $this->sort($query, $request);

        $this->search($query, $request, ['title']);

        $pagination = new Pagination(
            total: $query->count(),
            limit: static::ADMIN_FETCH_LIMIT,
        );

        View::send(
            'admin/articles',
            [
                'pagination' => $pagination,
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('articles')
                    ),
                ],
                'articles' => Article::fill($query),
                'sortings' => [
                    new Sorting(),
                    new Sorting('title', 'asc'),
                    new Sorting('title', 'desc'),
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc'),
                    new Sorting('visible', 'asc'),
                    new Sorting('visible', 'desc'),
                ]
            ]
        );
    }

    protected function sort(Query &$query, Request $request): Query
    {
        $sort = $request->request('sort') ?: 'created_at';

        $order = $request->request('order') ?: 'desc';

        return $query->order($sort, $order);
    }

    /**
     * Динамическая подгрузка статей в админпанели
     */
    public function fetchAdmin(Request $request): void
    {
        $this->checkAdmin();

        $query = Article::query();

        $this->sort($query, $request);

        $this->search($query, $request, ['title']);

        $this->fetch(
            $request,
            $query,
            function ($query) {
                $html = '';

                foreach (Article::fill($query) as $article) {
                    $html .= Template::html(
                        'admin/articles-item',
                        [
                            'article' => $article,
                        ]
                    );
                }

                return $html;
            },
            static::ADMIN_FETCH_LIMIT
        );
    }

    /**
     * Добавление статьи
     *
     * @param Request $request
     * @return void
     */
    public function add(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'title' => ['required'],
            'locale' => ['required'],
            'short_text' => ['required'],
            'full_text' => ['required'],
        ]);

        $article = new Article();

        $title = $request->request('title');

        if (!$title) {
            throw new Exception(Locale::get('article_title_required'));
        }

        $article->title = strval($title);

        $article->locale = strval($request->request('locale'));

        $category_id = $request->request('category_id');

        $article->category_id = $category_id ? intval($category_id) : null;

        $article->short_text = strval($request->request('short_text'));

        $article->full_text = strval($request->request('full_text'));

        $article->image = strval($request->request('image'));

        $article->visible = boolval($request->request('visible'));

        $tags = strval($request->request('tags'));

        $tags = $tags ? explode(',', $tags) : [];

        $tags = array_map('trim', $tags);

        $article->tags = $tags;

        $article->author_id = User::current()->id;

        if (!$article->save()) {
            throw new Exception(Locale::get('article_save_error'));
        }

        if (!is_dir(getenv('APP_ROOT') . "/public/images")) {
            mkdir(getenv('APP_ROOT') . "/public/images", 0775, true);
        }

        if (!is_dir(getenv('APP_ROOT') . "/public/images/articles")) {
            mkdir(getenv('APP_ROOT') . "/public/images/articles", 0775, true);
        }

        if (!is_dir(getenv('APP_ROOT') . "/public/images/articles/{$article->id}")) {
            mkdir(getenv('APP_ROOT') . "/public/images/articles/{$article->id}", 0775, true);
        }

        preg_match_all('/(\w+\.webp)/i', $article->full_text, $matches, PREG_SET_ORDER);

        $images = [];

        if ($article->image) {
            $images[] = $article->image;
            $images[] = "thumb_{$article->image}";
        }

        foreach ($matches as $set) {
            $images[] = $set[1];
            $images[] = "thumb_$set[1]";
        }

        foreach ($images as $image) {
            $temp_path = getenv('APP_ROOT') . "/public/images/articles/temp/{$image}";

            $new_path = getenv('APP_ROOT') . "/public/images/articles/{$article->id}/{$image}";

            if (is_file($temp_path)) rename($temp_path, $new_path);
        }

        static::removeTempImages($article);

        Response::redirect("/" . getenv('APP_LOCALE') . "/admin/articles");
    }

    /**
     * Редактирование статьи
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request): void
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'title' => ['required'],
        ]);

        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->exists()) {
            throw new Exception(Locale::get('article_not_found'));
        }

        $article->title = strval($request->request('title'));

        $article->locale = strval($request->request('locale'));

        $category = $request->request('category_id');

        $article->category_id = $category ? intval($category) : null;

        $image = $request->request('image');

        $article->image = $image ? preg_replace('/thumb_/', '', $image) : null;

        $short_text =  $request->request('short_text');

        $article->short_text = $short_text ?: null;

        $full_text = $request->request('full_text');

        $article->full_text = $full_text ?: null;

        $article->visible = boolval($request->request('visible'));

        $tags = strval($request->request('tags'));

        $tags = $tags ? explode(',', $tags) : [];

        $tags = array_map('trim', $tags);

        $article->tags = $tags;

        $article->last_author_id = User::current()->id;

        static::removeTempImages($article);

        if (!$article->save()) {
            Response::redirect(
                url: "/" . getenv('APP_LOCALE') . "/edit/article/{$article->id}",
                alert: new Alert(
                    status: 0,
                    message: Locale::get('article_save_error')
                )
            );
        }

        Response::redirect("/" . getenv('APP_LOCALE') . '/admin/articles');
    }

    /**
     * Удаление статьи
     *
     * @param Request $request
     * @return void
     */
    public function delete(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $article = Article::find($request->route('id'));

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception(Locale::get('article_not_found'));
        }

        $result = $article->delete();

        if ($result) {
            $path = getenv('APP_ROOT') . "/public/images/articles/{$article->id}";

            if (is_dir($path)) {
                foreach (glob("$path/*.*") as $file) {
                    unlink($file);
                }

                rmdir($path);
            }
        }

        Response::send(new Alert(
            status: intval($result),
            message: $result
                ? Locale::get('article_deleted')
                : Locale::get('article_delete_error')
        ));
    }

    /**
     * Загрузка изображений для статьи
     * 
     * @param Request $request
     */
    public function uploadImage(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = $request->route('id');

        $width = intval($request->request('width'));

        $height = intval($request->request('height'));

        $article = Article::find($id);

        $imageFolder = $article->id
            ? "images/articles/{$article->id}"
            : 'images/articles/temp';

        if (!is_dir("images/articles")) {
            mkdir("images/articles", 0775, true);
        }

        if (!is_dir("images/articles/temp")) {
            mkdir("images/articles/temp", 0775, true);
        }

        if (!is_dir($imageFolder)) {
            mkdir($imageFolder, 0775, true);
        }

        reset($_FILES);

        $temp = current($_FILES);

        list($csrf_key, $csrf_token) = CSRF::set();

        if (!is_uploaded_file($temp['tmp_name'])) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Error uploading file'
            ]);

            exit;
        }

        $extensions = ["gif", "jpg", "png", "webp", "jpeg", "bmp"];

        $fileExtension = strtolower(
            pathinfo(
                $temp['name'],
                PATHINFO_EXTENSION
            )
        );

        if (!in_array($fileExtension, $extensions)) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Invalid extension'
            ]);

            exit;
        }

        $extension = 'webp';

        $filename = md5(bin2hex(random_bytes(128)));

        $filetowrite =  "{$imageFolder}/{$filename}.{$extension}";

        $thumbtowrite = "{$imageFolder}/thumb_{$filename}.{$extension}";

        $quality = 80;

        switch ($fileExtension) {
            case 'gif':
                $im = imagecreatefromgif($temp['tmp_name']);
                break;
            case 'jpg':
            case 'jpeg':
                $im = imagecreatefromjpeg($temp['tmp_name']);
                break;
            case 'png':
                $im = imagecreatefrompng($temp['tmp_name']);
                break;
            case 'bmp':
                $im = imagecreatefrombmp($temp['tmp_name']);
                break;
            case 'webp':
                $im = imagecreatefromwebp($temp['tmp_name']);
                break;
            default:
                Response::json([
                    'csrf' => $csrf_token,
                    'error' => 'Unsupported image format'
                ]);

                exit;
        }

        $src_width = imagesx($im);

        $src_height = imagesy($im);

        $src_ratio = $src_width / $src_height;

        if ($src_width > Article::MAX_WIDTH) {
            $width = Article::MAX_WIDTH;
            $height = intval($width / $src_ratio);
        } else if ($src_height > Article::MAX_HEIGHT) {
            $height = Article::MAX_HEIGHT;
            $width = intval($height * $src_ratio);
        }

        $image = ($width && $height) ? Image::resize($im, $width, $height) : $im;

        $thumb = Image::resize(
            $im,
            Article::THUMB_WIDTH,
            Article::THUMB_HEIGHT
        );

        $success = imagewebp($image, $filetowrite, $quality) &&
            imagewebp($thumb, $thumbtowrite, $quality);

        if (!$success) {
            Response::json([
                'csrf' => $csrf_token,
                'error' => 'Error converting image to webp'
            ]);

            exit;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'
            ? "https://"
            : "http://";

        $baseurl = $protocol . $_SERVER["HTTP_HOST"];

        $location = $baseurl . "/" . $filetowrite;

        $thumb_location = $baseurl . "/" . $thumbtowrite;

        Response::json([
            'status' => 1,
            'csrf' => $csrf_token,
            'image' => basename($location),
            'thumb' => basename($thumb_location),
            'location' => $location,
            'thumb_location' => $thumb_location,
        ]);
    }

    /**
     * Вывод статьи
     * 
     * @param Request $request
     */
    public function show(Request $request)
    {
        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception(Locale::get('article_not_found'));
        }

        static::setTitle($article->title);

        View::send(
            'article',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        $article->category->title,
                        "/" . getenv('APP_LOCALE') . "/category/{$article->category->slug}",
                    ),
                    new Breadcrumb($article->title),
                ],
                'article' => $article,
            ]
        );
    }

    public function newArticle(Request $request)
    {
        $this->checkAdmin();

        $this->getArticleEditor($request, new Article());
    }

    public function editArticle(Request $request)
    {
        $this->checkAdmin();

        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception("Article ID:{$id} not found");
        }

        $this->getArticleEditor($request, $article);
    }

    protected function getArticleEditor(
        Request $request,
        Article $article
    ) {
        static::removeTempImages($article);

        $title = $article->id
            ? $article->title
            : Locale::get('article_new');

        static::setTitle($title);

        View::send(
            'admin/article',
            [
                'article' => $article,
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        url: '/admin/articles',
                        label: Locale::get('articles')
                    ),
                    new Breadcrumb($title)
                ],
            ]
        );
    }

    public static function getCategoriesOptions(Article $article): string
    {
        $options = Template::make('option', [
            'value' => '',
            'title' => '',
            'label' => '',
            'selected' => !$article->category_id ? 'selected' : '',
        ])->render();

        foreach (Category::fill(Category::query()) as $category) {
            assert($category instanceof Category);

            $options .= Template::make('option', [
                'value' => $category->id,
                'title' => $category->title,
                'label' => $category->title,
                'selected' => $category->id == $article->category_id ? 'selected' : '',
            ])->render();
        }

        return $options;
    }

    protected static function removeTempImages(Article $article)
    {
        $root = getenv('APP_ROOT');

        $imagesInArticle = [
            $article->image,
            "thumb_{$article->image}",
        ];

        preg_match_all(
            '/([0-9a-zA-Z]+\.webp)/',
            strval($article->full_text),
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $imagesInArticle[] = $match[1];
            $imagesInArticle[] = "thumb_{$match[1]}";
        }

        $path = "{$root}/public/images/articles/{$article->id}/*.webp";

        foreach (glob($path) as $file) {
            if (!in_array(basename($file), $imagesInArticle)) unlink($file);
        }
    }

    /**
     * Установка рейтинга статьи
     *
     * @param Request $request
     */
    public function setRating(Request $request)
    {
        $id = $request->route('id');

        $uuid = $request->request('uuid');

        if (!$uuid) {
            throw new Exception(Locale::get('uuid_is_required'));
        }

        $value = intval($request->request('rating'));

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception(Locale::get('article_not_found'));
        }

        if ($value < 1 || $value > 5) {
            throw new Exception(Locale::get('rating_invalid'));
        }

        $query = Rating::query()->where('uuid', $uuid)
            ->where('article_id', $article->id);

        $rating = Rating::make($query->first());

        assert($rating instanceof Rating);

        if (!$rating->id) {
            $rating = new Rating();
            $rating->article_id = $article->id;
            $rating->uuid = $uuid;
        }

        $rating->rating = ceil($value);

        $result = $rating->save();

        list($csrf_key, $csrf_token) = CSRF::set();

        Response::json([
            'status' => $result ? 1 : 0,
            'csrf' => $csrf_token,
            'message' => $result ? 'OK' : Locale::get('rating_save_error'),
        ]);
    }

    public function showArticlesInCategory(Request $request)
    {
        $slug = $request->route('slug');

        $category = Category::find($slug, 'slug');

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception(Locale::get('category_not_found'));
        }

        $id_list = [];

        if ($category->visible) $id_list = [$category->id];

        foreach ($category->getAllChildren() as $child) {
            assert($child instanceof Category);

            if (!$child->visible) continue;

            $id_list[] = $child->id;
        }

        $query = Article::query()
            ->where('category_id', '()', $id_list)
            ->where('visible', 1)
            ->limit(Pagination::DEFAULT_LIMIT);

        $this->sort($query, $request);

        $this->search($query, $request, ['title', 'short_text', 'full_text']);

        $pagination = new Pagination(
            total: $query->count(),
            limit: Pagination::DEFAULT_LIMIT,
        );

        View::send(
            'articles',
            [
                'pagination' => $pagination,
                'breadcrumbs' => [
                    new Breadcrumb($category->slug),
                ],
                'category' => $category,
                'articles' => Article::fill($query),
                'sortings' => [
                    new Sorting(),
                    new Sorting('title', 'asc'),
                    new Sorting('title', 'desc'),
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc'),
                ]
            ]
        );
    }

    /**
     * Динамическая подгрузка статей
     */
    public function fetchArticlesInCategory(Request $request)
    {
        $this->checkAdmin();

        $query = Article::query()->where('visible', 1);

        $slug = $request->route('slug');

        $category = Category::find($slug, 'slug');

        assert($category instanceof Category);

        $id_list = [];

        if ($category->visible) $id_list = [$category->id];

        foreach ($category->getAllChildren() as $child) {
            assert($child instanceof Category);

            if (!$child->visible) continue;

            $id_list[] = $child->id;
        }

        $query->where('category_id', '()', $id_list);

        $this->sort($query, $request);

        $this->search($query, $request, ['title', 'short_text', 'full_text']);

        $this->fetch(
            $request,
            $query,
            function ($query) {
                $html = '';

                foreach (Article::fill($query) as $article) {
                    $html .= Template::html(
                        'articles-item',
                        [
                            'article' => $article,
                        ]
                    );
                }

                return $html;
            },
            Pagination::DEFAULT_LIMIT
        );
    }
}
