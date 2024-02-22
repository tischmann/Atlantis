<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\{Column, Table};

class CategoriesTable extends Table
{
    public static function name(): string
    {
        return 'categories';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            new Column(
                name: 'title',
                type: 'varchar',
                default: null,
                description: 'Заголовок',
            ),
            new Column(
                name: 'parent_id',
                type: 'bigint',
                default: null,
                index: true,
                description: 'Родительская категория',
            ),
            new Column(
                name: 'position',
                type: 'int',
                default: null,
                index: true,
                description: 'Порядок сортировки',
            ),
            new Column(
                name: 'slug',
                type: 'varchar',
                length: 255,
                default: null,
                index: true,
                description: 'URL категории',
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
                name: 'visible',
                type: 'tinyint',
                default: 1,
                index: true,
                description: 'Видимость',
            ),
        ]);
    }
}
