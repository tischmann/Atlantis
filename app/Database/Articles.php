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
                default: null,
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
                default: null,
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
        ]);
    }

    public function seed(): int
    {
        $query = static::query();

        $query->insert([
            'locale' => 'ru',
            'title' => "Lorem ipsum dolor sit amet",
            'short_text' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sit amet tempor mauris.",
            'full_text' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut sit amet tempor mauris. Aliquam convallis dui vitae ullamcorper iaculis. Proin tortor dui, rhoncus et tortor eu, suscipit imperdiet sapien. Cras volutpat viverra ligula, quis condimentum lectus commodo non. In eleifend id augue vel blandit. Cras vehicula arcu vitae mauris feugiat, vitae blandit ligula facilisis. Ut laoreet ante quis leo dictum, a bibendum nibh lobortis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Integer et lacinia lectus, vitae sodales tellus. Pellentesque vitae dui eget leo varius vestibulum. Pellentesque fermentum libero sit amet neque feugiat, et ultricies ante pretium.",
            'tags' => json_encode(['lorem', 'ipsum']),
        ]);

        return 1;
    }
}
