<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{
    Article,
    Category,
    User
};

use Exception;

use Tischmann\Atlantis\{
    Breadcrumb,
    Controller,
    Locale,
    Pagination,
    Request,
    Response,
    Sorting,
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
                'breadcrumbs' => [new Breadcrumb(Locale::get('dashboard'))],
            ]
        );
    }

    /**
     * Вывод списка пользователь в админпанели
     */
    public function getUsers(Request $request): void
    {
        $this->checkAdmin();

        View::send(
            'admin/users',
            [
                'breadcrumbs' => [
                    new Breadcrumb(
                        url: '/admin',
                        label: Locale::get('dashboard')
                    ),
                    new Breadcrumb(
                        label: Locale::get('users')
                    ),
                ],
                'users' => User::fill(User::query()),
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

        View::send(
            'admin/categories',
            [
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
            throw new Exception(Locale::get('category_not_found'));
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

            static::setTitle($category->title);
        } else {
            $breadcrumbs[] = new Breadcrumb(Locale::get('category_new'));
        }

        View::send(
            'admin/category',
            [
                'breadcrumbs' => $breadcrumbs,
                'category' => $category,

            ]
        );
    }

    /**
     * Вывод списка статей в админпанели
     */
    public function getArticles(Request $request): void
    {
        $this->checkAdmin();

        $query = Article::query()->limit(Pagination::DEFAULT_LIMIT);

        $sort = $request->request('sort') ?: 'id';

        $order = $request->request('order') ?: 'desc';

        $query->order($sort, $order);

        View::send(
            'admin/articles',
            [
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
                    new Sorting('updated_at', 'asc'),
                    new Sorting('updated_at', 'desc'),
                    new Sorting('visible', 'asc'),
                    new Sorting('visible', 'desc'),
                ]
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
