<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Articles;

use Tischmann\Atlantis\{Migration, Model};

class Article extends Model
{
    public Category $category;

    public string $image_url = '';

    public function __construct(
        public ?int $author_id = null,
        public ?int $category_id = null,
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

        $this->category = $this->getCategory();

        $this->image_url = $this->getImageUrl();
    }

    public function __fill(object|array $traversable): self
    {
        parent::__fill($traversable);

        $this->category = $this->getCategory();

        $this->image_url = $this->getImageUrl();

        return $this;
    }

    public function getDescription(): string
    {
        if (!strlen($this->full_text)) return '';

        preg_match('/^(?:[^\.]+\.){3}/', $this->full_text, $matches);

        if ($matches) return $matches[0];

        $text = strip_tags($this->full_text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = mb_substr($text, 0, 200) . '...';

        return $text;
    }

    public function getImageUrl(): string
    {
        $image_url = "/images/articles/{$this->id}/{$this->image}";

        if (!is_file(getenv('APP_ROOT') . "/public{$this->image_url}")) {
            return "/images/placeholder.svg";
        }

        return $image_url;
    }

    public function getCategory(): Category
    {
        return Category::find($this->category_id);
    }

    public static function table(): Migration
    {
        return new Articles();
    }
}
