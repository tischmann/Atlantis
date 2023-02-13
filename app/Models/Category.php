<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Categories;

use Exception;

use Tischmann\Atlantis\{Migration, Model};

class Category extends Model
{
    public const DEPRECATED_SLUGS = ['admin'];

    public array $children = [];

    public function __construct(
        public int $parent_id = 0,
        public int $position = 0,
        public string $locale = '',
        public string $title = '',
        public string $slug = '',
    ) {
        parent::__construct();

        $this->children = $this->getChildren();
    }

    public function __fill(object|array $traversable): self
    {
        parent::__fill($traversable);

        $this->children = $this->getChildren();

        return $this;
    }

    public function getChildren(): array
    {
        return Category::fill(
            Category::query()->where('parent_id', $this->id)
                ->order('position', 'ASC')
        );
    }

    public function getAllChildren(array &$children = []): array
    {
        foreach ($this->children as $child) {
            assert($child instanceof Category);

            $children[$child->id] = $child;

            if ($child->children) $children = $child->getAllChildren($children);
        }

        return $children;
    }

    public function save(): bool
    {
        if (in_array($this->slug, self::DEPRECATED_SLUGS)) {
            throw new Exception("Slug '{$this->slug}' is deprecated");
        }

        return parent::save();
    }

    public static function table(): Migration
    {
        return new Categories();
    }
}
