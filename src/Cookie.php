<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы с куками
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Cookie
{
    /**
     * Блокируем конструктор
     */
    private function __construct()
    {
    }

    /**
     * Получение значения куки
     *
     * @param string $name Имя куки
     * @return string|null Значение куки или null, если кука не существует
     */
    public static function get(string $name): ?string
    {
        $cookie = array_map('static::sanitize', $_COOKIE);

        return $cookie[$name] ?? null;
    }

    /**
     * Проверка наличия куки
     *
     * @param string $name Имя куки
     * @return boolean Если кука существует, то true, иначе false
     */
    public static function has(string $name): bool
    {
        return key_exists($name, $_COOKIE);
    }

    /**
     * Установка куки
     *
     * @param string $name Имя куки
     * @param string $value Значение куки
     * @param array $options Опции куки
     * @return bool true в случае успеха, иначе false
     */
    public static function set(
        string $name,
        mixed $value,
        array $options = []
    ): bool {
        return setcookie(
            $name,
            strval($value),
            array_merge([
                'expires' => intval(getenv('APP_COOKIE_LIFETIME') ?: 0),
                'path' => strval(getenv('APP_COOKIE_PATH') ?: '/'),
                'secure' => boolval(getenv('APP_COOKIE_SECURE') ?: true),
                'httponly' => boolval(getenv('APP_COOKIE_HTTP_ONLY') ?: true),
                'samesite' => getenv('APP_COOKIE_SAMESITE') ?: 'Strict',
            ], $options)
        );
    }

    /**
     * Удаление куки
     *
     * @param string $name Имя куки
     * @param array $options Опции куки
     * @return bool true в случае успеха, иначе false
     */
    public static function delete(string $name, array $options = []): void
    {
        static::set(
            $name,
            "",
            array_merge(
                [
                    'path' => strval(getenv('APP_COOKIE_PATH') ?: '/'),
                    'secure' => boolval(getenv('APP_COOKIE_SECURE') ?: true),
                    'httponly' => boolval(getenv('APP_COOKIE_HTTP_ONLY') ?: true),
                    'samesite' => getenv('APP_COOKIE_SAMESITE') ?: 'Strict',
                ],
                $options,
                [
                    'expires' => time() - 3600
                ]
            )
        );
    }

    /**
     * Фильтрация данных переменной
     * 
     * @param string $value Переменная
     * @return mixed Отфильтрованная переменная
     */
    private static function sanitize(mixed $value): mixed
    {
        return match (gettype($value)) {
            'integer' => intval(filter_var($value, 519)),
            'double' => floatval(filter_var($value, 520)),
            'string' => strval(filter_var($value, 513)),
            default =>  $value,
        };
    }
}
