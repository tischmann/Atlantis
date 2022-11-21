<?php

declare(strict_types=1);

namespace App\Models;

use Tischmann\Atlantis\{
    Model,
    DateTime,
    Query,
    Migration
};

use Tischmann\Atlantis\Migrations\{Users};

class User extends Model
{
    public const GUEST = 0;

    public const USER = 1;

    public const ADMIN = 2;

    public function __construct(
        public int $id = 0,
        public ?string $login = null,
        public ?string $password = null,
        public ?int $role = null,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
        parent::__construct(id: $id, created_at: $created_at);
    }

    public static function table(): Migration
    {
        return new Users();
    }

    public static function query(): Query
    {
        return static::table()->query();
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::USER;
    }

    public function isGuest(): bool
    {
        return $this->role === self::GUEST;
    }

    public function exists(): bool
    {
        return $this->id > 0;
    }
}
