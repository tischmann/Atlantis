<?php

use App\Models\Category;

$childrenID = array_keys($category->getAllChildren());

$parents = Category::fill(
    Category::query()
        ->where('locale', $category->locale)
        ->where('id', '!()', [
            $category->id,
            ...$childrenID
        ])
        ->order('title', 'ASC')
);

foreach ([new Category(), ...$parents] as $parent) {
    assert($parent instanceof Category);

    $value = $parent->id ? $parent->id : '';

    $selected = $parent->id === $category->parent_id ? 'selected' : '';

    echo <<<HTML
    <option value="{$value}" {$selected} title="{$parent->title}">{$parent->title}</option>
    HTML;
}
