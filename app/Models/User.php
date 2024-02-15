<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{UsersTable};

use Tischmann\Atlantis\{
    Table,
    Model
};

class User extends Model
{
    public const ROLE_ADMIN = 255;

    public const ROLE_USER = 1;

    public const ROLE_GUEST = 0;

    public function __construct(
        public string $login = '',
        public string $name = '',
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
        $user = parent::find($value, $column);

        assert($user instanceof self);

        return $user;
    }

    public static function table(): Table
    {
        return UsersTable::instance();
    }

    /**
     * Проверка на администратора
     *
     * @return bool Возвращает true, если пользователь администратор
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Проверка на пользователя
     *
     * @return bool Возвращает true, если пользователь обычный
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Проверка на гостя
     *
     * @return bool Возвращает true, если пользователь гость
     */
    public function isGuest(): bool
    {
        return $this->role === self::ROLE_GUEST;
    }

    /**
     * Проверка на активность
     *
     * @return bool Возвращает true, если пользователь активен
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    /**
     * Проверка на неактивность
     *
     * @return bool Возвращает true, если пользователь неактивен
     */
    public function isInactive(): bool
    {
        return !$this->status;
    }
}
