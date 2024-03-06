<?php

declare(strict_types=1);

namespace App\Models;


use App\Database\{ArticlesTable};

use Tischmann\Atlantis\{DateTime, Model, Table};

class Article extends Model
{
    public const IMAGE_WIDTH = 1280;

    public const IMAGE_HEIGHT = 720;

    public const IMAGE_THUMB_WIDTH = 320;

    public const IMAGE_THUMB_HEIGHT = 180;

    public function __construct(
        public int $id = 0,
        public ?int $author_id = null,
        public ?int $last_author_id = null,
        public ?int $category_id = null,
        public string $locale = '',
        public string $title = '',
        public ?string $image = null,
        public ?string $short_text = null,
        public ?string $text = null,
        public ?array $tags = [],
        public bool $visible = true,
        public bool $fixed = false,
        public bool $moderated = false,
        protected int $views = 0,
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

    public function getImage(): string
    {
        $src = "";

        foreach (glob(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/image/thumb_*.webp") as $file) {
            $filename = basename($file);
            $filename = str_replace('thumb_', '', $filename);
            return $filename;
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

        $files = glob(getenv('APP_ROOT') . "/public/images/articles/{$this->id}/gallery/thumb_*.webp");

        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        foreach ($files as $file) {
            $filename = basename($file);
            $filename = str_replace('thumb_', '', $filename);
            $images[] = $filename;
        }

        return $images;
    }

    public function getAttachements(): array
    {
        $attachements = [];

        $files = glob(getenv('APP_ROOT') . "/public/uploads/articles/{$this->id}/attachements/*.*");

        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        foreach ($files as $file) {
            $filename = basename($file);

            $attachements[] = $filename;
        }

        return $attachements;
    }

    public function getVideos(): array
    {
        $videos = [];

        $files = glob(getenv('APP_ROOT') . "/public/uploads/articles/{$this->id}/video/*.*");

        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        foreach ($files as $file) {
            $filename = basename($file);

            $videos[] = $filename;
        }

        return $videos;
    }

    public static function removeOldTempImagesAndUploads(int $minutes = 3600): void
    {
        $temp_dir = getenv('APP_ROOT') . "/public/images/articles/temp";

        $uploads_temp_dir = getenv('APP_ROOT') . "/public/uploads/articles/temp";

        $time = time();

        foreach (glob("{$temp_dir}/*") as $file) {
            if (filemtime($file) < $time - $minutes) {
                unlink($file);
            }
        }

        foreach (glob("{$uploads_temp_dir}/*") as $file) {
            if (filemtime($file) < $time - $minutes) {
                unlink($file);
            }
        }
    }

    /**
     * Возвращает размеры изображения статьи
     * 
     * @param bool $thumb - если true, то возвращает размеры миниатюры
     * @return array - массив с ключами width и height
     */
    public function getImageSizes(bool $thumb = false, ?string $file = null): array
    {
        if ($file) return getimagesize($file);

        $dir = getenv('APP_ROOT') . "/public/images/articles/{$this->id}/image/";

        $prefix = $thumb ? 'thumb_' : '';

        foreach (glob("{$dir}/{$prefix}*.webp") as $file) {
            return getimagesize($file);
        }

        return [
            $thumb ? static::IMAGE_THUMB_WIDTH : static::IMAGE_WIDTH,
            $thumb ? static::IMAGE_THUMB_HEIGHT : static::IMAGE_HEIGHT
        ];
    }

    /**
     * Возвращает количество просмотров статьи
     * 
     * @return int - количество просмотров
     */
    public function getViews(): int
    {
        $this->views = static::getCache(
            "article_{$this->id}_views",
            function () {
                return View::getArticleViews($this->id);
            }
        );

        return $this->views;
    }

    /**
     * Устанавливает просмотр статьи
     * 
     * @param string $uuid - UUID
     * @return void
     */
    public function setView(string $uuid): void
    {
        if ($uuid) {
            if (View::setArticleView($this->id, $uuid)) {
                $this->views = View::getArticleViews($this->id);
            }
        }
    }
}
