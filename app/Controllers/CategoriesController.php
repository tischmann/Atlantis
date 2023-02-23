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
    Locale,
    Request,
    Response,
    Template,
    View,
};

class CategoriesController extends Controller
{
    /**
     * Вывод списка категорий в админпанели
     */
    public function index(Request $request): void
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
    public function get(Request $request)
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

        $this->editor($category);
    }

    /**
     * Форма добавления категории
     *
     * @param Request $request
     * 
     * @return void
     */
    public function new(Request $request)
    {
        $this->checkAdmin();

        $this->editor();
    }

    /**
     * Вывод формы добавления/редактирования категории
     * 
     * @param Category $category Категория
     */
    public function editor(Category $category = new Category())
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
     * Добавление категории
     * 
     * @param Request $request Запрос
     * 
     * @throws Exception
     */
    public function add(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'title' => ['required'],
            'slug' => ['required'],
            'locale' => ['required'],
        ]);

        CSRF::verify($request);

        $category = new Category();

        $category->title = $request->request('title');

        if (!strlen($category->title)) {
            throw new Exception('Title can not be empty');
        }

        $category->slug = $request->request('slug');

        if (!strlen($category->slug)) {
            throw new Exception('Slug can not be empty');
        }

        $category->locale = $request->request('locale');

        $parent_id = $request->request('parent_id');

        $category->parent_id = $parent_id ? intval($parent_id) : null;

        $category->visible = boolval($request->request('visible'));

        $result = $category->save();

        Response::redirect(
            url: '/' . getenv('APP_LOCALE') . '/admin/categories',
            alert: new Alert(
                status: $result ? -1 : 0,
                message: Locale::get(
                    $result ? 'category_added' : 'category_add_error'
                )
            )
        );
    }

    /**
     * Сортировка категорий
     * 
     * @param Request $request Запрос
     */
    public function order(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $categories = $request->request('children') ?? [];

        $position = 1;

        foreach ($categories as $id) {
            $category = Category::find($id);

            assert($category instanceof Category);

            if (!$category->id) continue;

            $category->position = $position++;

            if (!$category->save()) {
                Response::send([
                    'status' => 0,
                    'message' => Locale::get('category_order_error')
                ]);
            }
        }

        Response::send([
            'status' => 1,
            'csrf' => CSRF::set()[1],
        ]);
    }

    /**
     * Изменение категории
     *
     * @param Request $request
     *
     */
    public function update(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'id' => ['required'],
            'title' => ['required'],
            'slug' => ['required'],
            'locale' => ['required'],
        ]);

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception(Locale::get('category_not_found') . ": {$id}");
        }

        $title = strval($request->request('title'));

        $slug = strval($request->request('slug'));

        $url = '/' . getenv('APP_LOCALE') . "/category/edit/{$id}";

        $slugs = Category::query()->where('slug', '!=', $category->slug)
            ->distinct('slug');

        if (in_array($slug, $slugs)) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('category_slug_exists') . ": {$slug}"
                )
            );
        }

        $locale = strval($request->request('locale'));

        $parent_id = $request->request('parent_id');

        $category->title = $title;

        $category->slug = $slug;

        $category->locale = $locale;

        $category->parent_id = $parent_id ? intval($parent_id) : null;

        $category->visible = boolval($request->request('visible'));

        if (!$category->save()) {
            Response::redirect(
                url: $url,
                alert: new Alert(
                    status: 0,
                    message: Locale::get('category_save_error') . ": {$id}"
                )
            );
        }

        Response::redirect('/' . getenv('APP_LOCALE') . '/admin/categories');
    }

    /**
     * Удаление категории
     *
     * @param Request $request
     *
     */
    public function delete(Request $request)
    {
        $this->checkAdmin();

        CSRF::verify($request);

        $id = intval($request->route('id'));

        $category = Category::find($id);

        assert($category instanceof Category);

        if (!$category->id) {
            throw new Exception(Locale::get('category_not_found') . ": {$id}");
        }

        foreach ($category->getAllChildren() as $child) {
            assert($child instanceof Category);

            if (!$child->id) continue;

            if (!$child->delete()) {
                throw new Exception(
                    Locale::get('category_child_delete_error') . ": {$child->id}"
                );
            }
        }

        $result = $category->delete();

        Response::send(new Alert(
            status: intval($result),
            message: $result
                ? Locale::get('category_deleted')
                : Locale::get('category_delete_error')
        ));
    }

    public function getArticles(Request $request)
    {
        $category_id = $request->route('id');

        $query = Article::query()
            ->where('category_id', $category_id)
            ->order('updated_at', 'DESC');

        $category = Category::find($category_id);

        assert($category instanceof Category);

        View::send(
            'articles',
            [
                'breadcrumbs' => [
                    new Breadcrumb($category->title),
                ],
                'articles' => Article::fill($query),
            ]
        );
    }

    public function fetchParentCategories(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'locale' => ['required', 'string'],
        ]);

        CSRF::verify($request);

        $locale = $request->request('locale');

        $id = $request->request('id');

        $html = '';

        $query = Category::query()->where('locale', $locale);

        if ($id) $query->where('id', $id);

        foreach (Category::fill($query) as $category) {
            $html = Template::html('admin/parent-category-options', [
                'category' => $category,
            ]);
        }

        Response::send([
            'status' => 1,
            'html' => $html,
            'message' => 'OK',
            'csrf' => CSRF::set()[1],
        ]);
    }
}
