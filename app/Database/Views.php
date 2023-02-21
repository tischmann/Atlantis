<?php

declare(strict_types=1);

namespace App\Database;

use Tischmann\Atlantis\{Column, Migration};

class Views extends Migration
{
    public static function name(): string
    {
        return 'views';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            new Column(
                name: 'article_id',
                type: 'bigint',
                index: true,
                description: 'Идентификатор статьи',
            ),
            new Column(
                name: 'uuid',
                type: 'varchar',
                default: null,
                index: true,
                description: 'UUID клиента',
            ),
        ]);
    }
}
