<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Article, Category};

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
    public function getArticles(): void
    {
        $this->checkAdmin();

        $items = '';

        foreach (Article::fill(Article::query()) as $article) {
            assert($article instanceof Article);

            $items .= Template::make('admin/articles-item', [
                'article_id' => $article->id,
                'article_image_url' => $article->image_url,
                'article_title' => $article->title,
                'article_description' => $article->short_text,
            ])->render();
        }

        Response::send(View::make(
            'admin/articles',
            [
                'breadcrumbs' => AdminController::renderBreadcrumbs([
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('adminpanel')
                    ),
                    new Breadcrumb(
                        label: Locale::get('articles')
                    ),
                ]),
                'items' => $items
            ]
        )->render());
    }

    public function getArticle(Request $request): void
    {
        $id = $request->route('id');

        $article = Article::find($id);

        assert($article instanceof Article);

        if (!$article->id) {
            throw new Exception("Article ID:{$id} not found");
        }

        Response::send(View::make(
            'article',
            []
        )->render());
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
                label: Locale::get('adminpanel')
            ),
            new Breadcrumb(
                url: '/articles',
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

        Response::send(View::make(
            'admin/article',
            [
                'csrf' => $this->getCsrfInput(),
                'csrf-token' => CSRF::set()[1],
                'article_id' => $article->id,
                'article_title' => $article->title,
                'article_image' => $article->image_url,
                'article_short_text' => $article->short_text,
                'article_full_text' => $article->full_text,
                'delete_button' => $delete_button,
                'locales_options' => $this->getLocalesOptions($article->locale),
                'category_options' => $this->getCategoriesOptions($article),
                'breadcrumbs' => AdminController::renderBreadcrumbs($breadcrumbs),
            ]
        )->render());
    }

    public function uploadArticleImage(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = $request->route('id');

        $article = Article::find($id);

        Response::json([
            'csrf' => CSRF::set()[1],
            'location' => self::uploadArticleFullTextImage($article)
        ]);

        exit;
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

        $title = strval($request->post('title'));

        if ($title && $title != $article->title) {
            $article->title = $title;
            $changed = true;
        }

        // Locale

        $locale = strval($request->post('locale'));

        if ($locale && $locale != $article->locale) {
            $article->locale = $locale;
            $changed = true;
        }

        // Category

        $category = intval($request->post('category_id'));

        if ($category && $category != $article->category_id) {
            $article->category_id = $category;
            $changed = true;
        }

        // Image

        $pictureBase64 = $request->post('image');

        if ($pictureBase64) {
            static::uploadArticleMainImage($article, $pictureBase64);
            $changed = true;
        }

        // Short text

        $shortText = strval($request->post('short_text'));

        if ($shortText && $shortText != $article->short_text) {
            $article->short_text = $shortText;
            $changed = true;
        }

        // Full text

        $fullText = strval($request->post('full_text'));

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
            url: "/" . getenv('APP_LOCALE') . '/articles',
            alert: new Alert(
                status: 1,
                message: "Article saved"
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

    public static function uploadArticleMainImage(
        Article &$article,
        string $base64Image
    ) {
        $dase64Data = Image::getBase64Data($base64Image, false);

        $filename = md5(bin2hex(random_bytes(128))) . '.webp';

        $imagesDir = 'images/articles';

        $saveDir =  "{$imagesDir}/{$article->id}";

        $root = getenv('APP_ROOT') . "/public";

        if (!file_exists("{$root}/{$imagesDir}")) {
            mkdir("{$root}/{$imagesDir}", 0755, true);
        }

        if (!file_exists("{$root}/{$saveDir}")) {
            mkdir("{$root}/{$saveDir}", 0755, true);
        }

        $newImage = "{$root}/{$saveDir}/{$filename}";

        if (!Image::base64ToWebp($dase64Data, $newImage)) {
            Response::redirect(
                url: "/edit/article/{$article->id}",
                alert: new Alert(
                    status: 0,
                    message: "Error while saving image"
                )
            );
        }

        $article->image = $filename;
    }

    public function uploadArticleFullTextImage(Article $article): string
    {
        $imageFolder = $article->id
            ? "images/articles/{$article->id}"
            : 'images/articles/temp';

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
            default:
                Response::json([
                    'error' => 'Unsupported image format'
                ]);

                exit;
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

        return $baseurl . "/" . $filetowrite;
    }
}
