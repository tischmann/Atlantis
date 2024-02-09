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

        $pagination = new Pagination(
            total: $query->count(),
            limit: static::ADMIN_FETCH_LIMIT,
        );

        View::send(
            'admin/articles',
            [
                'pagination' => $pagination,
                'articles' => Article::fill($query),
                'sortings' => [
                    new Sorting('title', 'asc'),
                    new Sorting('title', 'desc'),
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc')
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

        Response::send([
            'message' => $result
                ? Locale::get('article_deleted')
                : Locale::get('article_delete_error')
        ], $result ? 200 : 500);
    }

    /**
     * Загрузка изображений для статьи
     * 
     * @param Request $request
     */
    public function uploadImage(Request $request)
    {
        $this->checkAdmin();

        $id = intval($request->route('id'));

        $article = $id ? Article::find($id) : new Article();

        assert($article instanceof Article);

        $request->args('max_width', Article::MAX_WIDTH);

        $request->args('max_height', Article::MAX_HEIGHT);

        $request->args('thumb_width', Article::THUMB_WIDTH);

        $request->args('thumb_height', Article::THUMB_HEIGHT);

        $path = $article->id ? "{$article->id}" : 'temp';

        $request->args('path', "images/articles/{$path}");

        $request->args('thumb_path', "images/articles/{$path}");

        parent::uploadImage($request);
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

        $article->full_text = static::lazyfyImages($article->full_text);

        View::send(
            'article',
            [
                'article' => $article,
            ]
        );
    }


    /**
     * Поиск статей
     * 
     * @param Request $request
     */
    public function searchArticles(Request $request)
    {
        if (!$request->request('query')) Response::redirect('/');

        $limit = Pagination::DEFAULT_LIMIT;

        $query = Article::query()->where('visible', 1)->limit($limit);

        $this->sort($query, $request);

        $this->search($query, $request, ['title', 'short_text', 'full_text']);

        $pagination = new Pagination(
            total: $query->count(),
            limit: $limit,
        );

        static::setTitle(Locale::get('search'));

        View::send(
            'search',
            [
                'pagination' => $pagination,
                'articles' => Article::fill($query),
                'sortings' => [
                    new Sorting('created_at', 'asc'),
                    new Sorting('created_at', 'desc'),
                ]
            ]
        );
    }

    public function fetchFoundArticles(Request $request)
    {
        $query = Article::query()->where('visible', 1);

        $this->sort($query, $request);

        $this->fetch(
            $request,
            $query,
            function ($query) {
                $html = '';

                foreach (Article::fill($query) as $article) {
                    $html .= Template::html(
                        'search-article-item',
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

        if (!$rating->save()) {
            throw new Exception(Locale::get('rating_save_error'));
        }

        Response::json();
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

        $pagination = new Pagination(
            total: $query->count(),
            limit: Pagination::DEFAULT_LIMIT,
        );

        View::send(
            'articles',
            [
                'pagination' => $pagination,
                'category' => $category,
                'articles' => Article::fill($query),
                'sortings' => [
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

    protected static function lazyfyImages(?string $html): string
    {
        if (!$html) return '';

        $html = htmlspecialchars_decode($html);

        $html = preg_replace(
            '/<(img.*(?=src="))src="([^"]+)"([^>]+)>/i',
            '<$1src="/placeholder.svg" data-atlantis-lazy-image data-src="$2" $3>',
            $html
        );

        return $html;
    }
}
