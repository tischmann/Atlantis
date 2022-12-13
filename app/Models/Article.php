<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Articles;

use Tischmann\Atlantis\{Migration, Model};

class Article extends Model
{
    public string $category_title = '';

    public string $category_slug = '';

    public function __construct(
        public int $author_id = 0,
        public int $category_id = 0,
        public string $locale = '',
        public string $title = '',
        public string $image = '',
        public string $short_text = '',
        public string $full_text = '',
        public array $tags = [],
        public int $views = 0,
        public float $rating = 0,
    ) {
        parent::__construct();

        $this->defineCategory();
    }

    public function __fill(object|array $traversable): self
    {
        parent::__fill($traversable);

        return $this->defineCategory();
    }

    public function defineCategory(): self
    {
        if ($this->category_id) {
            $category = Category::find($this->category_id);

            $this->category_title = $category->title;

            $this->category_slug = $category->slug;
        }

        return $this;
    }

    public static function table(): Migration
    {
        return new Articles();
    }
}
