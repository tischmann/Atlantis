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
    private static ?User $user = null; // Текущий пользователь

    public static string $title = ''; // Заголовок страницы

    public static string $tags = ''; // Теги

    public static ?string $resource_version = null; // Версия ресурсов

    /**
     * Возвращает версию ресурсов
     * 
     * @return string  - Версия ресурсов (по умолчанию 1.0)
     */
    public static function getResourcesVersion(): string
    {
        static::$resource_version ??= getenv('VERSION') ?: '1.0';

        return static::$resource_version;
    }

    /**
     * Возвращает текущего пользователя
     *
     * @return User
     */
    public static function getUser(): User
    {
        static::$user ??= new User();

        static::$user = Auth::authorize();

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

        return static::$title . ' • ' . getenv('APP_TITLE');
    }

    /**
     * Возвращает теги страницы
     *
     * @return string - Теги страницы, разделенные запятыми
     */
    public static function getTags(): string
    {
        return static::$tags
            ? static::$tags . (", " . getenv('APP_KEYWORDS') ?: 'atlantis')
            : getenv('APP_KEYWORDS');
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

    /**
     * Устанавливает теги страницы
     *
     * @param array $tags - Теги страницы
     * @return string - Теги страницы, разделенные запятыми
     */
    public static function setTags(array $tags): string
    {
        return (static::$tags = implode(', ', $tags));
    }
}
