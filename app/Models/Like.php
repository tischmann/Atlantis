<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{LikesTable, ViewsTable};

use Tischmann\Atlantis\{DateTime, Model, Table};

/**
 * Модель для работы с таблицей "likes"
 */
class Like extends Model
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
        return new LikesTable();
    }

    /**
     * Возвращает количество лайков статьи
     *
     * @param integer $article_id ID статьи
     * @return integer Количество лайков
     */
    public static function getArticleLikes(int $article_id): int
    {
        return static::query()
            ->where('article_id', $article_id)
            ->count();
    }

    /**
     * Добавляет лайк для статьи
     *
     * @param integer $article_id ID статьи
     * @param string $uuid UUID
     * @return boolean true если добавлен, false если нет
     */
    public static function setArticleLike(int $article_id, string $uuid): bool
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

    /**
     * Удаляет лайк для статьи
     *
     * @param integer $article_id ID статьи
     * @param string $uuid UUID
     * @return boolean true если удален, false если нет
     */
    public static function deleteArticleLike(int $article_id, string $uuid): bool
    {
        return static::query()
            ->where('article_id', $article_id)
            ->where('uuid', $uuid)
            ->delete();
    }
}
