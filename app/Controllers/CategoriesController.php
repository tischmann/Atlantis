<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

use Exception;

use Tischmann\Atlantis\{
    Controller,
    Locale,
    Request,
    Response,
    View
};

/**
 * Контроллер категорий
 */
class CategoriesController extends Controller
{
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
            ->order('position', 'ASC');

        foreach (Category::all($all_query) as $cat) {
            assert($cat instanceof Category);

            $parent_options = [
                ...$parent_options,
                ...get_category_options($cat, $parent_id)
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


    public function showAllCategories(): void
    {
        $this->checkAdmin();

        $request = Request::instance();

        $locale = strval($request->request('locale'));

        $locale_options = [];

        foreach (['', ...Locale::available()] as $value) {
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
        $items = [];

        $locale = mb_strtolower($this->route->args('locale'));

        $selected = intval($this->route->args('category_id'));

        $query = Category::query()
            ->where('parent_id', null)
            ->where('locale', $locale)
            ->order('locale', 'ASC')
            ->order('title', 'ASC');

        $items[] = [
            'value' => '',
            'label' => '',
            'level' => 0,
            'selected' => $selected === 0 ? true : false,
            'disabled' => false
        ];

        foreach (Category::all($query) as $category) {
            assert($category instanceof Category);

            $items = [
                ...$items,
                ...$this->fetchChildren(
                    category: $category,
                    selected: $selected,
                    level: 0
                )
            ];
        }

        Response::json(['items' => $items]);
    }

    /**
     * Получение дочерних категорий
     */
    protected function fetchChildren(
        Category $category,
        int $selected = 0,
        int $level = 0
    ): array {
        $children = [
            [
                'value' => $category->id,
                'label' => $category->title,
                'level' => $level,
                'selected' => $selected === $category->id ? true : false,
                'disabled' => false
            ]
        ];

        $category->children = $category->fetchChildren();

        if ($category->children) $level++;

        foreach ($category->children as $child) {
            assert($child instanceof Category);

            $children[] = [
                'value' => $child->id,
                'label' => $child->title,
                'level' => $level,
                'selected' => $selected === $child->id ? true : false,
                'disabled' => false
            ];

            $child->children = $child->fetchChildren();


            if ($child->children) {
                $children = [
                    ...$children,
                    ...$this->fetchChildren($child, $selected, ++$level)
                ];
            }
        }

        return $children;
    }
}
