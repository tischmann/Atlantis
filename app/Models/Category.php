<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Categories;

use Exception;

use Tischmann\Atlantis\{Migration, Model};

class Category extends Model
{
    public const DEPRECATED_SLUGS = ['admin'];

    public function __construct(
        public int $parent_id = 0,
        public string $locale = '',
        public string $title = '',
        public string $slug = '',
    ) {
        parent::__construct();
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
        return  new Categories();
    }
}
