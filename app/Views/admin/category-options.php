<?php

use App\Models\{Article, Category};

if (isset($category)) {
    $locale = isset($locale) ? $locale : ($category->locale ?: getenv('APP_LOCALE'));

    $current = isset($category) ? $category : new Category();

    assert($current instanceof Category);

    $query = Category::query()
        ->where('parent_id', null)
        ->where('locale', $locale)
        ->order('title', 'ASC');

    if ($current->id) $query->where('id', '!=', $current->id);

    $categories = [new Category(), ...Category::fill($query)];
} elseif (isset($article)) {
    assert($article instanceof Article);

    $locale = isset($locale) ? $locale : ($article->locale ?: getenv('APP_LOCALE'));

    $current = new Category();

    if ($article->id) $current = $article->category;

    if ($locale != $article->locale) $current = new Category();

    $categories = [
        new Category(),
        ...Category::fill(
            Category::query()
                ->where('locale', $locale)
                ->where('parent_id', null)
                ->order('title', 'ASC')
        )
    ];
}

function childOptions(Category $parent, Category $current)
{
    $html = '';

    $parentCategory = $parent;

    $parentTitle = $parent->title;

    while ($parentCategory->parent_id) {
        $parentCategory = Category::find($parentCategory->parent_id);

        assert($parentCategory instanceof Category);

        $parentTitle = $parentCategory->title . ' - ' . $parentTitle;
    }

    foreach ($parent->children as $child) {
        assert($child instanceof Category);

        if ($child->id === $current->id) continue;

        $selected = $child->id === $current->id ? 'selected' : '';

        if ($current->parent_id) {
            $selected = $child->id === $current->parent_id ? 'selected' : '';
        }

        $html .= <<<HTML
        <option value="{$child->id}" {$selected} title="{$child->title}">
            {$parentTitle} - {$child->title}
        </option>
        HTML;

        if ($child->children) {
            $html .= childOptions($child, $current);
        }
    }

    return $html;
}

foreach ($categories as $category) {
    assert($category instanceof Category);

    $selected = $category->id === $current->id ? 'selected' : '';

    if ($current->parent_id) {
        $selected = $category->id === $current->parent_id ? 'selected' : '';
    }

    echo <<<HTML
    <option value="{$category->id}" {$selected} title="{$category->title}">
        {$category->title}
    </option>
    HTML;

    if ($category->children) {
        echo childOptions($category, $current);
    }
}
