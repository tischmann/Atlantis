<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{ViewsTable};

use Tischmann\Atlantis\{DateTime, Model, Table};

/**
 * Модель для работы с таблицей "views"
 */
class View extends Model
{
    public function __construct(
        public int $id = 0,
        public int $article_id = 0,
        public string $uuid = '',
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
    }

    public static function table(): Table
    {
        return new ViewsTable();
    }

    /**
     * Возвращает количество просмотров статьи
     *
     * @param integer $article_id ID статьи
     * @return integer Количество просмотров
     */
    public static function getArticleViews(int $article_id): int
    {
        return static::query()
            ->where('article_id', $article_id)
            ->count();
    }

    /**
     * Добавляет просмотр статьи
     *
     * @param integer $article_id ID статьи
     * @param string $uuid UUID
     * @return boolean true если просмотр добавлен, false если нет
     */
    public static function setArticleView(int $article_id, string $uuid): bool
    {
        $exist = static::query()
            ->where('article_id', $article_id)
            ->where('uuid', $uuid)
            ->exist();

        if ($exist) return false;

        $view = new static(
            article_id: $article_id,
            uuid: $uuid
        );

        return $view->save();
    }
}
