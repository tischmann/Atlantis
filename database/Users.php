<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Migrations;

use Tischmann\Atlantis\{Query, Column, Migration};

final class Users extends Migration
{
    public static function name(): string
    {
        return 'users';
    }

    public static function query(): Query
    {
        return new Query(table: self::name());
    }

    public function columns(): array
    {
        return [
            'id' => Column::id(),
            'login' => new Column(
                name: 'login',
                type: 'varchar',
                length: 255,
                unique: true,
                index: true,
                description: 'Логин'
            ),
            'password' => new Column(
                name: 'password',
                type: 'varchar',
                length: 255,
                description: 'Пароль'
            ),
            'role' => new Column(
                name: 'role',
                type: 'tinyint',
                length: 1,
                description: 'Роль'
            ),
            'created_at' => Column::createdTimestamp(),
            'updated_at' => Column::updatedTimestamp(),
        ];
    }

    public function seed(): int
    {
        // Добавление администратора по умолчанию
        return self::query()->insert([
            'login' => 'atlantis',
            'password' => password_hash('Flvbybcnhfn0h', PASSWORD_DEFAULT),
            'role' => 1,
        ]);
    }
}
