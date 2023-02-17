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
    Request,
    Response,
    Template,
    View
};

class ArticlesController extends Controller
{
    public function getArticle(Request $request): void
    {
        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception("Article ID:{$id} not found");
        }

        $edit = User::current()->isAdmin()
            ? Template::make(
                'admin/article-edit-button',
                [
                    'href' => "/" . getenv('APP_LOCALE') . "/edit/article/$article->id"
                ]
            )->render()
            : '';

        View::send(
            'article',
            [
                'app_title' => getenv('APP_TITLE') . " - {$article->title}",
                'breadcrumbs' => [
                    new Breadcrumb(
                        label: $article->category->title,
                        url: "/" . getenv('APP_LOCALE') . "/category/{$article->category_id}",
                    ),
                    new Breadcrumb(
                        label: $article->title
                    ),
                ],
                'edit' => $edit,
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

        $delete_button = '';

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

        View::send(
            'admin/article',
            [
                'article_id' => $article->id,
                'article_title' => $article->title,
                'article_image' => $article->image,
                'article_image_url' => $article->image_url,
                'article_short_text' => $article->short_text,
                'article_full_text' => $article->full_text,
                'delete_button' => $delete_button,
                'locales_options' => $this->getLocalesOptions($article->locale),
                'category_options' => $this->getCategoriesOptions($article),
                'breadcrumbs' => $breadcrumbs,
                'app_title' => getenv('APP_TITLE') . " - "
                    . ($article->id
                        ? $article->title
                        : Locale::get('article_new')),
            ]
        );
    }

    public function uploadArticleImage(Request $request)
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

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("Access-Control-Allow-Methods: POST, OPTIONS");

            Response::send([
                'error' => 'Invalid method'
            ], 400);

            exit;
        }

        reset($_FILES);

        $temp = current($_FILES);

        if (!is_uploaded_file($temp['tmp_name'])) {
            Response::send([
                'error' => 'Error uploading file'
            ], 500);

            exit;
        }

        if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
            Response::send([
                'error' => 'Invalid file name'
            ], 400);

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
                'error' => 'Invalid extension'
            ], 400);

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
                    'error' => 'Unsupported image format'
                ]);

                exit;
        }

        if ($width && $height) {
            $im = Image::resize($im, $width, $height);
        }

        if (!imagewebp($im, $filetowrite, $quality)) {
            Response::json([
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
            'image' => basename($location),
            'location' => $location
        ]);

        exit;
    }

    public function addArticle(Request $request)
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
            throw new Exception('Title is required');
        }

        $article->title = strval($title);

        $article->locale = strval($request->request('locale'));

        $category_id = $request->request('category_id');

        $article->category_id = $category_id ? intval($category_id) : null;

        $article->short_text = strval($request->request('short_text'));

        $article->full_text = strval($request->request('full_text'));

        $article->image = strval($request->request('image'));

        if (!$article->save()) {
            throw new Exception('Article not saved');
        }

        foreach (glob(getenv('APP_ROOT') . "/public/images/articles/temp/*.webp") as $file) {
            $filename = basename($file);

            if ($filename == $article->image) {
                if (!is_dir(getenv('APP_ROOT') . "/public/images")) {
                    mkdir(getenv('APP_ROOT') . "/public/images", 0775);
                }

                if (!is_dir(getenv('APP_ROOT') . "/public/images/articles")) {
                    mkdir(getenv('APP_ROOT') . "/public/images/articles", 0775);
                }

                if (!is_dir(getenv('APP_ROOT') . "/public/images/articles/{$article->id}")) {
                    mkdir(getenv('APP_ROOT') . "/public/images/articles/{$article->id}", 0775);
                }

                rename($file, getenv('APP_ROOT') . "/public/images/articles/{$article->id}/{$filename}");
            }
        }

        Response::redirect(
            "/" . getenv('APP_LOCALE') . "/admin/articles",
            new Alert(
                status: 1,
                html: Template::make('admin/articles-saved', [
                    'article' => $article,
                ])->render(),
                message: Locale::get('article_added')
            )
        );
    }

    public function updateArticle(Request $request): void
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
            throw new Exception('Article not found');
        }

        $changed = false;

        // Title

        $title = strval($request->request('title'));

        if ($title && $title != $article->title) {
            $article->title = $title;
            $changed = true;
        }

        // Locale

        $locale = strval($request->request('locale'));

        if ($locale && $locale != $article->locale) {
            $article->locale = $locale;
            $changed = true;
        }

        // Category

        $category = $request->request('category_id');

        if ($category != $article->category_id) {
            $article->category_id = $category ? intval($category) : null;
            $changed = true;
        }

        // Image

        $image = $request->request('image');

        if ($image != $article->image) {
            $article->image = $image;
            $changed = true;
        }

        // Short text

        $shortText = strval($request->request('short_text'));

        if ($shortText && $shortText != $article->short_text) {
            $article->short_text = $shortText;
            $changed = true;
        }

        // Full text

        $fullText = strval($request->request('full_text'));

        if ($fullText && $fullText != $article->full_text) {
            $article->full_text = $fullText;
            $changed = true;
        }

        if ($changed) {
            if (!$article->save()) {
                Response::redirect(
                    url: "/" . getenv('APP_LOCALE') . "/edit/article/{$article->id}",
                    alert: new Alert(
                        status: 0,
                        message: "Error while saving article"
                    )
                );
            }
        }

        static::removeTempImages($article);

        Response::redirect(
            url: "/" . getenv('APP_LOCALE') . '/admin/articles',
            alert: new Alert(
                status: 1,
                html: Template::make('admin/articles-saved', [
                    'article' => $article,
                ])->render(),
                message: Locale::get('article_saved')
            )
        );
    }

    public function deleteArticle(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception("Article ID:{$id} not found");
        }

        if (!$article->delete()) {
            Response::redirect(
                url: '/' . getenv('APP_LOCALE') . '/admin/articles',
                alert: new Alert(
                    status: 1,
                    message: Locale::get('article_delete_error')
                )
            );

            exit;
        }

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/articles',
            alert: new Alert(
                status: 1,
                message: Locale::get('article_deleted')
            )
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

    public static function removeTempImages(Article $article)
    {
        $root = getenv('APP_ROOT');

        $imagesInArticle = [$article->image];

        preg_match_all(
            '/([0-9a-zA-Z]+\.webp)/',
            $article->full_text,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $match) {
            $imagesInArticle[] = $match[1];
        }

        foreach (glob("{$root}/public/images/articles/{$article->id}/*.webp") as $file) {
            $imageInFolder = basename($file);

            if (!in_array($imageInFolder, $imagesInArticle)) {
                unlink($file);
            }
        }
    }
}
