<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{CategoriesTable};

use Tischmann\Atlantis\{DateTime, Model, Table};

/**
 * Категория
 */
class Category extends Model
{
    public function __construct(
        public int $id = 0,
        public string $title = '',
        public ?int $parent_id = null,
        public int $position = 0,
        public string $locale = 'ru',
        public string $slug = '',
        public bool $visible = true,
        public int $level = 0,
        public ?array $children = null,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
        parent::__construct(
            id: $id,
            created_at: $created_at,
            updated_at: $updated_at
        );

        $this->__init();
    }

    public static function table(): Table
    {
        return CategoriesTable::instance();
    }

    public static function find(mixed $value, string|array $column = 'id'): self
    {
        $category = parent::find($value, $column);

        assert($category instanceof self);

        return $category;
    }

    public function __init(): self
    {
        $this->children ??= $this->fetchChildren();

        return $this;
    }

    /**
     * Проверка на наличие дочерних категорий
     * 
     * @return bool
     */
    public function hasChildren(): bool
    {
        return (bool) count($this->children);
    }

    /**
     * Получение дочерних категорий
     * 
     * @param bool $recursive Рекурсивное получение дочерних категорий
     * 
     * @return array Дочерние категории
     */
    public function fetchChildren(bool $recursive = true): array
    {
        $query = Category::query()
            ->where('parent_id', $this->id)
            ->order('position', 'ASC');

        $children = [];

        $child_level = $this->level + 1;

        foreach ($query->get() as $fill) {
            $child = Category::instance($fill);

            assert($child instanceof Category);

            $child->level = $child_level;

            assert($child instanceof Category);

            if ($recursive) {
                $child->children = $child->fetchChildren();
            }

            $children[$child->id] = $child;
        }

        return $children;
    }

    /**
     * Получение всех категорий
     * 
     * @param string|null $locale Локаль
     * 
     * @return array Категории
     */
    public static function getAllCategories(
        ?string $locale = null,
        bool $recursive = true
    ): array {
        $query = Category::query()
            ->where('parent_id', null)
            ->order('title', 'ASC')
            ->order('parent_id', 'ASC')
            ->order('position', 'ASC');

        if ($locale) {
            $query->where('locale', $locale);
        }

        $categories = Category::all($query);

        foreach ($categories as $category) {
            assert($category instanceof Category);

            if ($recursive) {
                static::fetchChildCategories($category);
            }
        }

        return $categories;
    }

    protected static function fetchChildCategories(Category &$category): Category
    {
        $category->children = $category->fetchChildren();

        foreach ($category->children as $child) {
            assert($child instanceof Category);
            static::fetchChildCategories($child);
        }

        return $category;
    }
}
