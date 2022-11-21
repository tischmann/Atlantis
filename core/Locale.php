<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы с локализацией
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Locale
{
    private static array $locales = []; // Массив с локализациями (ключи - локали, значения - массивы с переводами)

    private function __construct()
    {
    }

    /**
     * Возвращает строку из файла локализации по ключу
     *
     * @param string $key Ключ
     * @param string $locale Локаль
     * @return string Строка из файла локализации
     */
    public static function get(string $key, string $locale = ''): string
    {
        $locale = $locale ?: getenv('APP_LOCALE');

        self::$locales[$locale] ??= static::load($locale);

        return self::$locales[$locale][$key] ?? $key;
    }

    /**
     * Возвращает массив доступных локалей
     *
     * @return array Массив доступных локалей
     */
    public static function available(): array
    {
        return array_map(
            function ($path) {
                return strtolower(basename($path));
            },
            glob(getenv('APP_ROOT') . "/lang/*", GLOB_ONLYDIR)
        );
    }

    /**
     * Проверяет, существует ли локаль
     * 
     * @param string $locale Локаль
     * @return bool true - если локаль существует, иначе false
     */
    public static function exists(string $locale): bool
    {
        return in_array(strtolower($locale), static::available());
    }

    /**
     * Загружает файл локализации
     *
     * @param string $locale Локаль
     * @return array Массив строк из файла локализации
     */
    public static function load(string $locale = ''): array
    {
        $locale = $locale ?: getenv('APP_LOCALE');

        $strings = [];

        foreach (glob(getenv('APP_ROOT') . "/lang/{$locale}/*.php") as $path) {
            $array = require_once $path;
            if (is_array($array)) $strings = array_merge($array, $strings);
        }

        return $strings;
    }
}
