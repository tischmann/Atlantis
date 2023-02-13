<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\{Category};

trait CategoriesTrait
{
    /**
     * Обработка категории для вывода в представлении
     * 
     * @param Category $category 
     * @return Category 
     */
    protected function processCategory(Category $category): object
    {
        $category->children = [];

        foreach (Category::query()->where('parent_id', $category->id)->get() as $row) {
            $category->children[] = $this->processCategory(Category::make()->__fill($row));
        }

        return $category;
    }
}
