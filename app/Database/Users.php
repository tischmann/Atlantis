<?php

declare(strict_types=1);

namespace App\Database;

use App\Models\User;

use Tischmann\Atlantis\{Column, Migration};

class Users extends Migration
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
                type: 'varchar',
                default: null,
                index: true,
                description: 'Роль',
            ),
        ]);
    }

    public function seed(): int
    {
        $query = static::query();

        $query->insert([
            'login' => 'administrator',
            'password' => password_hash('Flvbybcnhfn0h', PASSWORD_DEFAULT),
            'role' => User::ROLE_ADMIN,
        ]);

        return 1;
    }
}
