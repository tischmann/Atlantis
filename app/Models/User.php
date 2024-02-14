<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{Users};

use Tischmann\Atlantis\{
    Table,
    Model
};

class User extends Model
{
    public const ROLE_ADMIN = 255;

    public const ROLE_USER = 0;

    public const ROLE_GUEST = 1;

    public function __construct(
        public string $login = '',
        public string $password = '',
        public int $role = 0,
        public ?string $remarks = null,
        public bool $status = false,
        public ?string $refresh_token = null,
    ) {
        parent::__construct();
    }

    public static function find(mixed $value, string|array $column = 'id'): self
    {
        $model = parent::find($value, $column);

        assert($model instanceof User);

        return $model;
    }

    public static function table(): Table
    {
        return new Users();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }
}
