<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{
    Article,
    Category
};

use Exception;

use Tischmann\Atlantis\{
    Breadcrumb,
    Controller,
    Locale,
    Pagination,
    Request,
    Response,
    Template,
    View
};

class AdminController extends Controller
{
    /**
     * Вывод главной страницы админпанели
     */
    public function index()
    {
        $this->checkAdmin();

        $app_title = getenv('APP_TITLE') . " - " . Locale::get('dashboard');

        View::send(
            'admin/index',
            [
                'app_title' => $app_title,
                'breadcrumbs' => [new Breadcrumb(Locale::get('dashboard'))],
            ]
        );
    }

    /**
     * Вывод списка категорий в админпанели
     */
    public function getCategories(Request $request): void
    {
        $this->checkAdmin();

        $items = [];

        $query = Category::query()
            ->where('parent_id', null)
            ->order('position', 'ASC');

        foreach (Category::fill($query) as $category) {
            assert($category instanceof Category);

            if (!array_key_exists($category->locale, $items)) {
                $items[$category->locale] = [];
            }

            $items[$category->locale][] = $category;
        }

        $app_title = getenv('APP_TITLE') . " - " . Locale::get('categories');

        View::send(
            'admin/categories',
            [
                'app_title' => $app_title,
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('categories')
                    ),
                ],
                'items' => $items,
            ]
        );
    }

    /**
     * Вывод формы редактирования категории
     *
     * @param Request $request
     * 
     * @throws Exception
     */
    public function getCategory(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
        ]);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception("Category ID:{$id} not found");
        }

        $this->getCategoryEditor($category);
    }

    /**
     * Форма добавления категории
     *
     * @param Request $request
     * 
     * @return void
     */
    public function newCategory(Request $request)
    {
        $this->checkAdmin();

        $this->getCategoryEditor();
    }

    /**
     * Вывод формы добавления/редактирования категории
     * 
     * @param Category $category Категория
     */
    public function getCategoryEditor(Category $category = new Category())
    {
        $this->checkAdmin();

        $parentBreadcrumbs = [];

        if ($category->parent_id) {
            $parent = Category::find($category->parent_id);

            assert($parent instanceof Category);

            while (true) {
                $parentBreadcrumbs[] = new Breadcrumb(
                    $parent->title,
                    '/category/edit/' . $parent->id
                );

                $parent = Category::find($parent->parent_id);

                if (!$parent->id) break;
            }
        }

        $parentBreadcrumbs = array_reverse($parentBreadcrumbs);

        $breadcrumbs = [
            new Breadcrumb(
                url: '/admin',
                label: Locale::get('dashboard')
            ),
            new Breadcrumb(
                url: '/admin/categories',
                label: Locale::get('categories')
            ),
            ...$parentBreadcrumbs
        ];

        if ($category->id) {
            $breadcrumbs[] = new Breadcrumb($category->title);
        } else {
            $breadcrumbs[] = new Breadcrumb(Locale::get('category_new'));
        }

        $app_title = $category->id ? $category->title : Locale::get('category_new');

        $app_title = getenv('APP_TITLE') . " - " . $app_title;

        View::send(
            'admin/category',
            [
                'app_title' => $app_title,
                'breadcrumbs' => $breadcrumbs,
                'category' => $category,

            ]
        );
    }

    /**
     * Вывод списка статей в админпанели
     */
    public function getArticles(): void
    {
        $this->checkAdmin();

        $items = [];

        $query = Category::query()
            ->where('id', '()', Article::query()->distinct('category_id'))
            ->order('position', 'ASC');

        foreach (Category::fill($query) as $category) {
            assert($category instanceof Category);

            $query = Article::query()->where('category_id', $category->id)
                ->limit(Pagination::DEFAULT_LIMIT);

            $items[] = Article::fill($query);
        }

        $app_title = getenv('APP_TITLE') . " - " . Locale::get('articles');

        View::send(
            'admin/articles',
            [
                'app_title' => $app_title,
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('articles')
                    ),
                ],
                'items' => $items,

            ]
        );
    }

    /**
     * Динамическая подгрузка статей в админпанели
     */
    public function fetchArticles(Request $request): void
    {
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
                    $html .= Template::html('admin/articles-item', [
                        'article' => $article,
                    ]);
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
