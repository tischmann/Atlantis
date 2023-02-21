<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Articles;

use Tischmann\Atlantis\{Cookie, Migration, Model};

class Article extends Model
{
    public Category $category;

    public string $image_url = '';

    public int $views = 0;

    public float $rating = 0;

    public function __construct(
        public ?int $author_id = null,
        public ?int $category_id = null,
        public string $locale = '',
        public string $title = '',
        public ?string $image = null,
        public ?string $short_text = null,
        public ?string $full_text = null,
        public ?array $tags = null,
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

        $value = 0;

        if (!$this->id) return $value;

        $query = Rating::query()->where('article_id', $this->id);

        $ratings = Rating::fill($query);

        foreach ($ratings as $rating) {
            $value += $rating->rating;
        }

        if (!count($ratings)) return 0;

        $value = ceil($value / count($ratings));

        return $value;
    }

    public function getViews(): int
    {
        if (!$this->id) return 0;

        $uuid = Cookie::get('uuid');

        if ($uuid !== null) {
            $query = View::query()->where('uuid', $uuid)
                ->where('article_id', $this->id);

            $view = View::make($query->first());

            assert($view instanceof View);

            if (!$view->id) {
                $view = new View();
                $view->uuid = $uuid;
                $view->article_id = $this->id;
                $view->save();
            }
        }

        return View::query()->where('article_id', $this->id)->count();
    }

    public function getKeywords(
        int $min_word_length = 5,
        int $min_word_occurrence = 2,
        int $max_words = 10
    ): array {
        $keyword_count_sort = fn ($first, $sec) => $sec[1] - $first[1];

        $string = preg_replace(
            '/[^\p{L}0-9 ]/',
            ' ',
            $this->full_text ?: $this->short_text
        );

        $string = trim(preg_replace('/\s+/', ' ', $string));

        $words = explode(' ', $string);

        $keywords = array();

        while (($c_word = array_shift($words)) !== null) {

            if (strlen($c_word) < $min_word_length) continue;
            $c_word = strtolower($c_word);

            if (array_key_exists($c_word, $keywords)) $keywords[$c_word][1]++;
            else $keywords[$c_word] = array($c_word, 1);
        }

        usort($keywords, $keyword_count_sort);

        $final_keywords = array();

        foreach ($keywords as $keyword_det) {
            if ($keyword_det[1] < $min_word_occurrence) break;
            array_push($final_keywords, $keyword_det[0]);
        }

        $final_keywords = array_slice($final_keywords, 0, $max_words);

        return $final_keywords;
    }

    public static function table(): Migration
    {
        return new Articles();
    }
}
