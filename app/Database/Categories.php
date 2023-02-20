<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\{Column, Migration};

class Categories extends Migration
{
    public static function name(): string
    {
        return 'categories';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
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
                length: 3,
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
                name: 'title',
                type: 'varchar',
                length: 255,
                default: null,
                description: 'Заголовок',
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

    public function seed(): int
    {
        $query = static::query();

        $query->insert([
            'locale' => 'ru',
            'slug' => 'news',
            'title' => "Новости",
        ]);

        return 1;
    }
}
