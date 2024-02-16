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

    /**
     * Проверка пароля на сложность
     * 
     * Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву, одну цифру и один спецсимвол и быть не менее 8 символов
     *
     * @param string $password Пароль
     * @return bool Возвращает true, если пароль сложный
     */
    public static function checkPasswordComplexity(string $password): bool
    {
        $hasCapitalLetter = preg_match('/[A-Z]/', $password);

        $hasSmallLetter = preg_match('/[a-z]/', $password);

        $hasNumber = preg_match('/[0-9]/', $password);

        $hasSpecialChar = preg_match('/[^A-Za-z0-9]/', $password);

        $hasMinimumLength = strlen($password) >= 8;

        return $hasCapitalLetter
            && $hasSmallLetter
            && $hasNumber
            && $hasSpecialChar
            && $hasMinimumLength;
    }


    /**
     * Проверка имени пользователя на корректность
     *
     * @param string $name Имя пользователя
     * @return bool Возвращает true, если имя пользователя корректное
     */
    public static function checkUserName(string $name): bool
    {
        return boolval(preg_match('/^.{3,255}$/', $name));
    }

    /**
     * Проверка логина пользователя на корректность
     *
     * @param string $login Логин пользователя
     * @return bool Возвращает true, если логин пользователя корректный
     */
    public static function checkUserLogin(string $login): bool
    {
        if (strlen($login) < 3) return false;

        return !boolval(preg_match('/^[^a-z0-9_-]$/i', $login));
    }


    /**
     * Проверка на уникальность логина
     *
     * @param string $login Логин
     * @return bool Возвращает true, если логин уникален
     */
    public static function checkUserLoginExists(string $login): bool
    {
        $query = self::query()->where('login', $login);

        return $query->count() === 0;
    }

    public function getUserRoleText(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => get_str('user_role_admin'),
            self::ROLE_USER => get_str('user_role_user'),
            self::ROLE_GUEST => get_str('user_role_guest'),
            default => get_str('unknown'),
        };
    }

    /**
     * Проверка на последнего администратора
     *
     * @return bool Возвращает true, если пользователь последний администратор
     */
    public function isLastAdmin(): bool
    {
        if (!$this->isAdmin()) return false;

        $query = self::query()->where('role', self::ROLE_ADMIN);

        return $query->count() === 1;
    }
}
