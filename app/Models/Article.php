<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Articles;

use Tischmann\Atlantis\{Migration, Model};

class Article extends Model
{
    public Category $category;

    public string $image_url = '';

    public int $views = 0;

    public float $rating = 0;

    public static ?float $cached_rating = null;

    public static ?int $cached_views = null;

    public function __construct(
        public ?int $author_id = null,
        public ?int $category_id = null,
        public string $locale = '',
        public string $title = '',
        public string $image = '',
        public string $short_text = '',
        public string $full_text = '',
        public array $tags = [],
        public bool $visible = true,
    ) {
        parent::__construct();

        $this->category = $this->getCategory();

        $this->image_url = $this->getImageUrl();

        $this->rating = $this->getRating();

        $this->views = $this->getViews();
    }

    public function __fill(object|array $traversable): self
    {
        parent::__fill($traversable);

        $this->category = $this->getCategory();

        $this->image_url = $this->getImageUrl();

        $this->rating = $this->getRating();

        $this->views = $this->getViews();

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
        if (!$this->image) {
            return "/images/placeholder.svg";
        }

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

    public function getRating(): float
    {
        if (!$this->id) return 0;

        if (static::$cached_rating === null) {
            $value = 0;

            if (!$this->id) return $value;

            $query = Rating::query()->where('article_id', $this->id);

            $ratings = Rating::fill($query);

            foreach ($ratings as $rating) {
                $value += $rating->rating;
            }

            $value = ceil($value / count($ratings));

            static::$cached_rating = $value;
        }

        return static::$cached_rating;
    }

    public function getViews(): int
    {
        if (!$this->id) return 0;

        if (static::$cached_views === null) {
            static::$cached_views = View::query()->where('article_id', $this->id)->count();
        }

        return static::$cached_views;
    }

    public static function table(): Migration
    {
        return new Articles();
    }
}
