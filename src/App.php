<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;

/**
 * Класс приложения
 *
 */
final class App
{
    protected static ?User $user = null; // Текущий пользователь

    protected static ?Auth $auth = null; // Авторизация

    public static string $title = ''; // Заголовок страницы

    public static ?string $assets_prefix = null; // Префикс для ассетов

    public static ?bool $is_in_development = null; // Режим разработки

    /**
     * Проверяет, находится ли приложение в режиме разработки
     *
     * @return bool
     */
    public static function isInDevelopment(): bool
    {
        static::$is_in_development ??= boolval(cookies_get('DEV_MODE'));

        return static::$is_in_development;
    }

    /**
     * Возвращает префикс для ассетов
     *
     * @return string
     */
    public static function getAssetsPrefix(): string
    {
        static::$assets_prefix ??= static::isInDevelopment() ? '' : '.min';

        return static::$assets_prefix;
    }

    /**
     * Возвращает текущего пользователя
     *
     * @return User
     */
    public static function getCurrentUser(): User
    {
        static::$user ??= new User();

        static::$auth = new Auth(static::$user);

        static::$user = static::$auth->authorize();

        return static::$user;
    }

    /**
     * Возвращает заголовок страницы
     *
     * @return string
     */
    public static function getTitle(bool $use_postfix = true): string
    {
        if (!$use_postfix) return static::$title;

        if (static::$title === '') return getenv('APP_TITLE');

        return static::$title . ' | ' . getenv('APP_TITLE');
    }

    /**
     * Устанавливает заголовок страницы
     *
     * @param string $title - Заголовок страницы
     * @return string - Заголовок страницы
     */
    public static function setTitle(string $title): string
    {
        return (static::$title = $title);
    }
}
