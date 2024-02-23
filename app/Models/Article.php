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

    public static function find(mixed $value, string|array $column = 'id'): self
    {
        $model = parent::find($value, $column);

        assert($model instanceof self);

        return $model;
    }

    public function __init(): self
    {

        return $this;
    }

    public static function table(): Table
    {
        return ArticlesTable::instance();
    }

    public function getImage(bool $thumb = true): string
    {
        $src = "/images/article_image_placeholder.webp";

        if ($thumb) {
            foreach (glob(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/image/thumb_*.webp") as $file) {
                return "/images/articles/{$this->id}/image/" . basename($file);
            }
        }

        foreach (glob(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/image/*.webp") as $file) {
            return "/images/articles/{$this->id}/image/" . basename($file);
        }

        return $src;
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

        foreach (glob(getenv('APP_ROOT') . "/public/uploads/articles/{$this->id}/attachements/*.*") as $file) {
            $filename = basename($file);

            $attachements[] = [
                "name" => $filename,
                "url" => "/uploads/articles/{$this->id}/attachements/{$filename}",
            ];
        }

        return $attachements;
    }

    public function getVideos(): array
    {
        $videos = [];

        foreach (glob(getenv('APP_ROOT') . "/public/uploads/articles/{$this->id}/video/*.*") as $file) {
            $filename = basename($file);

            $videos[] = [
                "name" => $filename,
                "url" => "/uploads/articles/{$this->id}/video/{$filename}",
            ];
        }

        return $videos;
    }
}
