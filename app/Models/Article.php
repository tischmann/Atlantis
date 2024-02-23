<?php

declare(strict_types=1);

namespace App\Models;


use App\Database\{ArticlesTable};

use Tischmann\Atlantis\{DateTime, Model, Table};

class Article extends Model
{
    public function __construct(
        public int $id = 0,
        public ?int $author_id = null,
        public ?int $last_author_id = null,
        public ?int $category_id = null,
        public string $locale = '',
        public string $title = '',
        public ?string $image = null,
        public ?string $text = null,
        public ?array $tags = [],
        public int $views = 0,
        public float $rating = 0,
        public bool $visible = true,
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

    public function __init(): self
    {

        return $this;
    }

    public static function table(): Table
    {
        return ArticlesTable::instance();
    }

    public function getDescription(): string
    {
        if (!strlen($this->text)) return '';

        preg_match('/^(?:[^\.]+\.){3}/', $this->text, $matches);

        if ($matches) return $matches[0];

        $text = strip_tags($this->text);

        $text = preg_replace('/\s+/', ' ', $text);

        $text = mb_substr($text, 0, 200) . '...';

        return $text;
    }

    public function getImage(): string
    {
        if (!is_file(getenv('APP_ROOT') . "/public/images/articles/thumb_{$this->id}.webp")) {
            if (!is_file(getenv('APP_ROOT') . "/public/images/articles/{$this->id}.webp")) {
                return "/images/placeholder.webp";
            }

            return "/images/articles/{$this->id}.webp";
        }

        return "/images/articles/thumb_{$this->id}.webp";
    }

    public function getCategory(): Category
    {
        return Category::find($this->category_id);
    }

    public function getGalleryImages(): array
    {
        $images = [];

        foreach (glob(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/gallery/*.webp") as $file) {
            $filename = basename($file);

            $image = [
                "src" => "/images/articles/{$this->id}/gallery/{$filename}",
                "thumb" => "/images/articles/{$this->id}/gallery/{$filename}",
            ];

            if (is_file(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/gallery/thumb_{$filename}")) {
                $image["thumb"] = "/images/articles/{$this->id}/gallery/thumb_{$filename}";
            }

            $images[] = $image;
        }

        return $images;
    }

    public function getAttachements(): array
    {
        $attachements = [];

        foreach (glob(getenv('APP_ROOT') . "/public/uploads/articles/{$this->id}/*") as $file) {
            $filename = basename($file);

            $attachements[] = [
                "name" => $filename,
                "url" => "/uploads/articles/{$this->id}/{$filename}",
            ];
        }

        return $attachements;
    }
}
