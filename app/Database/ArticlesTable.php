<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\{Column, Foreign, Table};

class ArticlesTable extends Table
{
    public static function name(): string
    {
        return 'articles';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            new Column(
                name: 'title',
                type: 'varchar',
                length: 255,
                default: null,
                description: 'Заголовок',
            ),
            new Column(
                name: 'short_text',
                type: 'longtext',
                default: null,
                description: 'Краткий текст статьи',
            ),
            new Column(
                name: 'text',
                type: 'longtext',
                default: null,
                description: 'Полный текст статьи',
            ),
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
                name: 'tags',
                type: 'json',
                default: null,
                description: 'Теги',
            ),
            new Column(
                name: 'visible',
                type: 'tinyint',
                length: 1,
                default: '1',
                description: 'Видимость',
            ),
            new Column(
                name: 'fixed',
                type: 'tinyint',
                length: 1,
                default: 0,
                description: 'Закреплена',
            ),
        ]);
    }
}
