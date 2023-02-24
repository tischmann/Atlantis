<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\{Column, Foreign, Migration};

class Articles extends Migration
{
    public static function name(): string
    {
        return 'articles';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            new Column(
                name: 'author_id',
                type: 'bigint',
                index: true,
                description: 'Автор',
                foreign: new Foreign(
                    table: 'users',
                    column: 'id',
                    delete: 'SET NULL',
                    update: 'CASCADE'
                ),
            ),
            new Column(
                name: 'last_author_id',
                type: 'bigint',
                index: true,
                description: 'Последний редактор',
                foreign: new Foreign(
                    table: 'users',
                    column: 'id',
                    delete: 'SET NULL',
                    update: 'CASCADE'
                ),
            ),
            new Column(
                name: 'category_id',
                type: 'bigint',
                index: true,
                description: 'Категория',
                foreign: new Foreign(
                    table: 'categories',
                    column: 'id',
                    delete: 'SET NULL',
                    update: 'CASCADE'
                ),
            ),
            new Column(
                name: 'locale',
                type: 'varchar',
                length: 2,
                default: null,
                index: true,
                description: 'Локаль',
            ),
            new Column(
                name: 'title',
                type: 'varchar',
                length: 255,
                default: null,
                description: 'Заголовок',
            ),
            new Column(
                name: 'image',
                type: 'varchar',
                length: 255,
                default: null,
                description: 'Изображение',
            ),
            new Column(
                name: 'short_text',
                type: 'text',
                default: null,
                description: 'Краткое описание',
            ),
            new Column(
                name: 'full_text',
                type: 'longtext',
                default: null,
                description: 'Полное описание',
            ),
            new Column(
                name: 'tags',
                type: 'json',
                default: null,
                description: 'Теги',
            ),
            new Column(
                name: 'views',
                type: 'bigint',
                default: null,
                description: 'Просмотры',
            ),
            new Column(
                name: 'rating',
                type: 'float',
                default: null,
                description: 'Рейтинг',
            ),
            new Column(
                name: 'visible',
                type: 'tinyint',
                length: 1,
                default: '1',
                description: 'Видимость',
            ),
        ]);
    }
}
