<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

use Tischmann\Atlantis\{
    Controller,
    Response
};

/**
 * Контроллер категорий
 */
class CategoriesController extends Controller
{
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
