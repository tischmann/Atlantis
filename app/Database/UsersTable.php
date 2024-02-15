<?php

declare(strict_types=1);

namespace App\Database;

use App\Models\User;

use Tischmann\Atlantis\{Column, Table};

class UsersTable extends Table
{
    public static function name(): string
    {
        return 'users';
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), [
            new Column(
                name: 'login',
                type: 'varchar',
                default: null,
                unique: true,
                description: 'Логин',
            ),
            new Column(
                name: 'password',
                type: 'varchar',
                default: null,
                description: 'Пароль',
            ),
            new Column(
                name: 'role',
                type: 'tinyint',
                signed: false,
                default: 0,
                index: true,
                description: 'Роль',
            ),
            new Column(
                name: 'remarks',
                type: 'longtext',
                default: null,
                description: 'Примечания',
            ),
            new Column(
                name: 'refresh_token',
                type: 'text',
                default: null,
                description: 'Токен обновления',
            ),
            new Column(
                name: 'status',
                type: 'tinyint',
                signed: false,
                default: 0,
                description: 'Статус',
            ),
        ]);
    }

    public function seed(): int
    {
        $query = static::query();

        return $query->insert([
            'login' => 'administrator',
            'password' => password_hash('Flvbybcnhfn0h', PASSWORD_DEFAULT),
            'role' => User::ROLE_ADMIN,
            'status' => 1
        ]);
    }
}
