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

        View::send(
            'admin/index',
            [
                'app_title' => getenv('APP_TITLE') . " - " . Locale::get('dashboard'),
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

        $query = Article::query()->limit(Pagination::DEFAULT_LIMIT);

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
                'articles' => Article::fill($query),

            ]
        );
    }

    /**
     * Динамическая подгрузка статей в админпанели
     */
    public function fetchArticles(Request $request): void
    {
        ArticlesController::fetchArticles(
            $request,
            'admin/articles-item'
        );
    }
}
