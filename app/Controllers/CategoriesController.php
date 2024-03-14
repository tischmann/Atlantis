<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Article, Category};

use Exception;

use Tischmann\Atlantis\{
    Controller,
    Locale,
    Pagination,
    Request,
    Response,
    View
};

/**
 * Контроллер категорий
 */
class CategoriesController extends Controller
{
    public function showCategory(): void
    {
        $category = Category::find($this->route->args('slug'), 'slug');

        if (!$category->exists()) {
            View::send(
                view: 'error',
                exit: true,
                args: [
                    'title' => get_str('not_found'),
                    'code' => '404'
                ],
                code: 404
            );
        }

        $query = Article::query()
            ->where('category_id', $category->id)
            ->where('locale', getenv('APP_LOCALE'))
            ->order('created_at', 'DESC');

        $pagination = new Pagination(query: $query, limit: 12);

        View::send(
            view: 'articles_in_category',
            args: [
                'category' => $category->title,
                'pagination' => $pagination,
                'articles' => Article::all($query)
            ]
        );
    }
    /**
     * Форма редактирования/создания категории
     */
    public function getCategoryEditor(): void
    {
        $this->checkAdmin();

        $request = Request::instance();

        $locale = strval($request->request('locale') ?? getenv('APP_LOCALE'));

        $category = Category::find($this->route->args('id'));

        $parent_id = intval($category->parent_id);

        $locale_options = [];

        foreach (Locale::available() as $value) {
            $locale_options[] = [
                'value' => $value,
                'text' => get_str("locale_{$value}"),
                'selected' => $locale === $value,
                'level' => '0'
            ];
        }

        $parent_options = [
            [
                'value' => '',
                'text' => '',
                'selected' => $parent_id === 0,
                'level' => 0
            ]
        ];

        $all_query = Category::query()
            ->where('locale', $locale)
            ->where('parent_id', null)
            ->where('id', '!=', $category->id)
            ->order('position', 'ASC');

        foreach (Category::all($all_query) as $cat) {
            assert($cat instanceof Category);

            $parent_options = [
                ...$parent_options,
                ...get_category_options($cat, $parent_id, $category->id)
            ];
        }

        $category = Category::find($this->route->args('id'));

        View::send(
            view: 'category_editor',
            args: [
                'category' => $category,
                'locale_options' => $locale_options,
                'parent_options' => $parent_options
            ],
            layout: 'default'
        );
    }

    /**
     * Сортировка категорий
     */
    public function sortCategories()
    {
        $this->checkAdmin(type: 'json');

        try {
            $request = Request::instance();

            $request->validate([
                'categories' => ['required', 'array']
            ]);

            $categories = $request->request('categories') ?? [];

            foreach ($categories as $index => $id) {
                $category = Category::find($id);

                if ($category->exists()) {
                    $category->position = $index;
                    $category->save();
                }
            }

            Response::json();
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Вывод всех категорий в админпанели
     */
    public function showAllCategories(): void
    {
        $this->checkAdmin();

        $request = Request::instance();

        $locale = strval($request->request('locale') ?? getenv('APP_LOCALE'));

        $locale_options = [];

        foreach (Locale::available() as $value) {
            $locale_options[] = [
                'value' => $value,
                'text' => get_str("locale_{$value}"),
                'selected' => $locale === $value,
                'level' => '0'
            ];
        }

        $query = Category::query()
            ->where('parent_id', null)
            ->order('position', 'ASC');

        if ($locale !== "") {
            $query->where('locale', $locale);
        }

        $categories = Category::all($query);

        View::send(
            view: 'categories_list',
            args: [
                'categories' => $categories,
                'locale_options' => $locale_options
            ]
        );
    }

    /**
     * Получение списка категорий
     */
    public function fetchCategories(): void
    {
        $this->checkAdmin(type: 'json');

        try {
            $locale = mb_strtolower($this->route->args('locale') ?? getenv('APP_LOCALE'));

            $selected = intval($this->route->args('category_id') ?? 0);

            $items = [
                [
                    'value' => '',
                    'text' => '',
                    'level' => 0,
                    'selected' => $selected === 0 ? true : false
                ]
            ];

            $query = Category::query()
                ->where('parent_id', null)
                ->where('locale', $locale)
                ->order('locale', 'ASC')
                ->order('title', 'ASC');

            foreach (Category::all($query) as $category) {
                assert($category instanceof Category);

                $items = [
                    ...$items,
                    ...get_category_options($category, $selected)
                ];
            }

            Response::json(['items' => $items]);
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Удаление категории
     */
    public function deleteCategory()
    {
        $this->checkAdmin(type: 'json');

        try {
            $id = $this->route->args('id');

            $category = Category::find($id);

            if (!$category->exists()) {
                throw new Exception(get_str('category_not_found'), 404);
            }

            if (!$category->delete()) {
                throw new Exception(get_str('not_deleted'), 500);
            }

            // Удаление дочерних категорий

            $parent_id = [$category->id];

            while (true) {
                if (!$parent_id) break;

                $query = Category::query()
                    ->where('parent_id', '()', $parent_id);

                $categories = Category::all($query);

                if (!$categories) break;

                $parent_id = [];

                foreach ($categories as $child) {
                    assert($child instanceof Category);
                    if ($child->delete()) $parent_id[] = $child->id;
                }
            }

            Response::json([
                'title' => get_str('attention'),
                'message' => get_str('deleted')
            ]);
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Создание категории
     */
    public function insertCategory()
    {
        $this->checkAdmin(type: 'json');

        $this->updateCategory();
    }

    /**
     * Изменение/создание категории
     */
    public function updateCategory()
    {
        $this->checkAdmin(type: 'json');

        try {
            $request = Request::instance();

            $request->validate([
                'parent_id' => ['required', 'string'],
                'title' => ['required', 'string'],
                'locale' => ['required', 'string'],
                'slug' => ['required', 'string'],
            ]);

            $id = $this->route->args('id');

            $category = new Category();

            if ($id) {
                $category = Category::find($id);

                if (!$category->exists()) {
                    throw new Exception(get_str('acategory_not_found') . ":{$id}", 404);
                }
            }

            $category->title = $request->request('title');

            if (!$category->title) {
                throw new Exception(get_str('field_required') . ": title", 400);
            }

            $category->locale = $request->request('locale');

            if (!$category->locale) {
                throw new Exception(get_str('field_required') . ": locale", 400);
            }

            $category->parent_id = intval($request->request('parent_id'));

            $category->parent_id = $category->parent_id ?: null;

            $category->slug = $request->request('slug');

            if (!$category->slug) {
                throw new Exception(get_str('field_required') . ": slug", 400);
            }

            if (!$category->save()) {
                throw new Exception(get_str('not_saved'));
            }

            Response::json([
                'title' => get_str('attention'),
                'message' => get_str('saved'),
                'id' => $category->id
            ]);
        } catch (Exception $e) {
            Response::json([
                'title' => get_str('warning'),
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }
}
