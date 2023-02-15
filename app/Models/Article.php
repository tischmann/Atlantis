<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Articles;

use Tischmann\Atlantis\{Migration, Model};

class Article extends Model
{
    public string $category_title = '';

    public string $category_slug = '';

    public string $image_url = '';

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

        $this->defineImage();

        if (!$this->short_text) {
            $this->short_text = $this->defineShortText();
        }
    }

    public function __fill(object|array $traversable): self
    {
        parent::__fill($traversable);

        $this->defineCategory();

        $this->defineImage();

        if (!$this->short_text) {
            $this->short_text = $this->defineShortText();
        }

        return $this;
    }

    public function defineShortText(): string
    {
        if (!strlen($this->full_text)) return '';

        preg_match('/^(?:[^\.]+\.){3}/', $this->full_text, $matches);

        if ($matches) return $matches[0];

        $text = strip_tags($this->full_text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = mb_substr($text, 0, 200) . '...';

        return $text;
    }

    public function defineImage(): self
    {
        $placeholder = "/images/placeholder.svg";

        $this->image_url = "/images/articles/{$this->id}/{$this->image}";

        if (!is_file(getenv('APP_ROOT') . "/public{$this->image_url}")) {
            $this->image_url = $placeholder;
        }

        return $this;
    }

    public function defineCategory(): self
    {
        if ($this->category_id) {
            $category = Category::find($this->category_id);

            assert($category instanceof Category);

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
