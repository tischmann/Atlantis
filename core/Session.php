<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\Exceptions\{NotFoundException};

final class Session
{
    private function __construct()
    {
    }

    /**
     * Запуск сессии
     *
     * @return bool
     */
    public static function start(
        string $name = 'PHPSESSID',
        ?string $id = null
    ): void {
        session_name($name);

        $id ??= session_id();

        session_id($id);

        session_start();
    }

    /**
     * Установка значения в сессию
     *
     * @param string $key Ключ
     * @param mixed $value Значение
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Проверка наличия значения в сессии
     *
     * @param string $key Ключ
     * @return bool true - если значение есть, false - если значения нет
     */
    public static function has(string $key): bool
    {
        return key_exists($key, $_SESSION);
    }

    /**
     * Получение значения из сессии
     *
     * @param string $key Ключ
     * @return mixed Значение 
     * @throws NotFoundException Если значения нет
     */
    public static function get(string $key): mixed
    {
        if (!static::has($key)) throw new NotFoundException();

        return $_SESSION[$key];
    }

    /**
     * Удаление значения из сессии
     *
     * @param string $key Ключ
     * @return void
     */
    public static function delete(string $key): void
    {
        if (static::has($key)) unset($_SESSION[$key]);
    }

    /**
     * Удаление всех значений из сессии
     *
     * @return void
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
    }
}
