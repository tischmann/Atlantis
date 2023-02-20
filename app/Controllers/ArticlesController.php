<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Article, Category, User};

use Exception;

use Tischmann\Atlantis\{
    Alert,
    Breadcrumb,
    Controller,
    CSRF,
    Image,
    Locale,
    Pagination,
    Request,
    Response,
    Template,
    View
};

class ArticlesController extends Controller
{
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

        if ($article->image) $images[] = $article->image;

        foreach ($matches as $set) $images[] = $set[1];

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

        $article->image = $image ?: null;

        $short_text =  $request->request('short_text');

        $article->short_text = $short_text ?: null;

        $full_text = $request->request('full_text');

        $article->full_text = $full_text ?: null;

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

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            Response::send([
                'csrf' => $csrf_token,
                'error' => 'Invalid file name'
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

        $filename = md5(bin2hex(random_bytes(128))) . '.webp';

        $filetowrite = $imageFolder . "/" . $filename;

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

        if ($width && $height) {
            $im = Image::resize($im, $width, $height);
        }

        if (!imagewebp($im, $filetowrite, $quality)) {
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

        Response::json([
            'csrf' => $csrf_token,
            'image' => basename($location),
            'location' => $location
        ]);
    }

    /**
     * Вывод статьи
     * 
     * @param Request $request
     */
    public function getArticle(Request $request)
    {
        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception(Locale::get('article_not_found'));
        }

        View::send(
            'article',
            [
                'app_title' => getenv('APP_TITLE') . " - {$article->title}",
                'breadcrumbs' => [
                    new Breadcrumb(
                        $article->category->title,
                        "/" . getenv('APP_LOCALE') . "/category/{$article->category_id}",
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

        $breadcrumbs = [
            new Breadcrumb(
                url: '/admin',
                label: Locale::get('dashboard')
            ),
            new Breadcrumb(
                url: '/admin/articles',
                label: Locale::get('articles')
            ),
        ];

        if ($article->id) {
            $breadcrumbs[] = new Breadcrumb(
                label: $article->title
            );

            $delete_button = Template::make(
                template: 'admin/delete-button',
                args: ['href' => "/delete/article/{$article->id}"]
            )->render();
        } else {
            $breadcrumbs[] = new Breadcrumb(
                label: Locale::get('article_new')
            );
        }

        $app_title = getenv('APP_TITLE') . " - "
            . ($article->id
                ? $article->title
                : Locale::get('article_new'));

        View::send(
            'admin/article',
            [
                'article' => $article,
                'breadcrumbs' => $breadcrumbs,
                'app_title' => $app_title,
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

        $imagesInArticle = [$article->image];

        preg_match_all(
            '/([0-9a-zA-Z]+\.webp)/',
            $article->full_text,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) $imagesInArticle[] = $match[1];

        $path = "{$root}/public/images/articles/{$article->id}/*.webp";

        foreach (glob($path) as $file) {
            if (!in_array(basename($file), $imagesInArticle)) unlink($file);
        }
    }

    /**
     * Динамическая подгрузка статей
     */
    public static function fetchArticles(
        Request $request,
        string $template = 'articles-item'
    ) {
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
                    $html .= Template::html(
                        $template,
                        [
                            'article' => $article,
                        ]
                    );
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
