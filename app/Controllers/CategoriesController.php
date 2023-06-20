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
            ->order('locale', 'ASC')
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

        if ($category->id) static::setTitle($category->title);

        View::send(
            'admin/category',
            [
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

        $categories = $request->request('children') ?? [];

        $position = 1;

        foreach ($categories as $id) {
            $category = Category::find($id);

            assert($category instanceof Category);

            if (!$category->id) continue;

            $category->position = $position++;

            if (!$category->save()) {
                Response::json([
                    'message' => Locale::get('category_order_error')
                ], 500);
            }
        }

        Response::json();
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

        Response::send([
            'message' => $result
                ? Locale::get('category_deleted')
                : Locale::get('category_delete_error')
        ], $result ? 200 : 500);
    }

    public function fetchParentCategories(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'locale' => ['required', 'string'],
        ]);

        $locale = $request->request('locale');

        $args = ['locale' => $locale];

        $category = $request->request('category') ?? null;

        $article = $request->request('article') ?? null;

        if ($category !== null) {
            $args['category'] = Category::find($category);
        } elseif ($article !== null) {
            $args['article'] = Article::find($article);
        }

        Response::json([
            'html' => Template::html(
                'admin/category-options',
                $args
            )
        ]);
    }
}
