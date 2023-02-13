<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Article, Category};
use Exception;
use Tischmann\Atlantis\{Alert, Breadcrumb, Controller, CSRF, Image, Locale, Request, Response, View};

class ArticlesController extends Controller
{
    use ArticlesTrait;

    public function getArticles(): void
    {
        $this->checkAdmin();

        $articles = [];

        $query = Article::query();

        foreach ($query->get() as $row) {
            $articles[] = $this->processArticle(Article::make()->__fill($row));
        }

        $view = View::make(
            'admin/articles',
            [
                'articles' => $articles,
                'alert' => $this->getAlert()
            ]
        );

        Response::send($view->render());
    }

    public function getArticle(Request $request): void
    {
        $this->checkAdmin();

        $article = Article::find($request->route('id'));

        $view = View::make(
            'article',
            [
                'article' => $this->processArticle($article),
            ]
        );

        Response::send($view->render());
    }

    public function getArticleEditor(Request $request): void
    {
        $this->checkAdmin();

        $article = Article::find($request->route('id'));

        $article = $this->processArticle($article);

        $locales = [];

        foreach (Locale::available() as $locale) {
            $locales[$locale] = [
                'title' => Locale::get("locale_{$locale}"),
                'selected' => getenv('APP_LOCALE') === $locale
            ];
        }

        $categories = [];

        $query = Category::query()->where('locale', $article->locale);

        foreach ($query->get() as $row) {
            $category = (object) get_object_vars(Category::make()->__fill($row));
            $category->selected = $article->category_id == $category->id;
            $categories[] = $category;
        }

        $view = View::make(
            'admin/article',
            [
                'article' => $article,
                'locales' => $locales,
                'categories' => $categories,
                'alert' => $this->getAlert()
            ]
        );

        Response::send($view->render());
    }

    public function updateArticle(Request $request): void
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $request->validate([
            'title' => ['required', 'string'],
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
            $dase64Data = Image::getBase64Data($pictureBase64, false);

            $filename = md5(bin2hex(random_bytes(128))) . '.webp';

            $saveDir = Image::ARTICLES_IMAGES_DIR . "/{$article->id}";

            $root = getenv('APP_ROOT') . "/public";

            if (!file_exists("{$root}/" . Image::ARTICLES_IMAGES_DIR)) {
                mkdir("{$root}/" . Image::ARTICLES_IMAGES_DIR, 0755, true);
            }

            if (!file_exists("{$root}/{$saveDir}")) {
                mkdir("{$root}/{$saveDir}", 0755, true);
            }

            $newImage = "{$root}/{$saveDir}/{$filename}";

            $oldImage = $article->image ? "{$root}/{$saveDir}/{$article->image}" : null;

            if (Image::base64ToWebp($dase64Data, $newImage)) {
                if ($oldImage && is_file($oldImage)) {
                    if (!unlink($oldImage)) {
                        Response::redirect(
                            url: '/edit/article/' . $id,
                            alert: new Alert(
                                status: 0,
                                message: "Error while deleting old image"
                            )
                        );
                    }
                }

                $article->image = $filename;

                $changed = true;
            } else {
                Response::redirect(
                    url: '/edit/article/' . $id,
                    alert: new Alert(
                        status: 0,
                        message: "Error while saving image"
                    )
                );
            }
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
                    url: '/edit/article/' . $id,
                    alert: new Alert(
                        status: 0,
                        message: "Error while saving article"
                    )
                );
            }
        }

        Response::redirect(
            url: '/admin/articles',
            alert: new Alert(
                status: 1,
                message: "Article saved"
            )
        );
    }
}
