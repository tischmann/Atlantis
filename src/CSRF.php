<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Класс для защиты от CSRF атак
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class CSRF
{
    private const SESSION_KEY = 'ATLANTIS_CSRF_TOKENS';

    private function __construct()
    {
    }

    /**
     * Удаляет все токены
     */
    public static function flush(string $key = null)
    {
        if ($key === null) Session::delete(static::SESSION_KEY);
        else {
            $tokens = static::tokens();
            unset($tokens[$key]);
            Session::set(static::SESSION_KEY, $tokens);
        }
    }

    /**
     * Возвращает токены
     * 
     * @return array Токены
     */
    public static function tokens(): array
    {
        if (!Session::has(static::SESSION_KEY)) {
            Session::set(static::SESSION_KEY, []);
        }

        return Session::get(static::SESSION_KEY);
    }

    /**
     * Устанавливает токен
     * 
     * @return array [Ключ, Токен]
     */
    public static function set(): array
    {
        $key = bin2hex(random_bytes(32));

        $token = bin2hex(random_bytes(32));

        $tokens = static::tokens();

        $tokens[$key] = $token;

        Session::set(static::SESSION_KEY, $tokens);

        return [$key, $token];
    }

    public static function generateToken(): string
    {
        return static::set()[1];
    }

    /**
     * Производит проверку токенов
     * 
     * @param Request $request Запрос
     * 
     * @return bool Результат проверки
     */
    public static function verify(Request $request): bool
    {
        $verified = true;

        if (in_array($request->method, ['POST', 'PUT', 'DELETE'])) {
            $found = array_intersect(
                static::tokens(),
                [$request->headers('X-Csrf-Token')]
            );

            $verified = false;

            if (!$found) {
                $args = $request->request();

                foreach (static::tokens() as $key => $token) {
                    if (!array_key_exists($key, $args)) continue;
                    if (($verified = $args[$key] === $token)) {
                        static::flush($key);
                        break;
                    }
                }
            } else {
                $key = array_search(
                    $request->headers('X-Csrf-Token'),
                    static::tokens()
                );

                $verified = true;

                static::flush($key);
            }
        }

        if (!$verified) static::flush();

        return $verified;
    }

    /**
     * Проверяет, что токен не прошел проверку
     *
     * @param Request $request
     * @return boolean
     */
    public static function failed(Request $request): bool
    {
        return !static::verify($request);
    }

    /**
     * Проверяет, что токен прошел проверку
     *
     * @param Request $request
     * @return boolean
     */
    public static function passed(Request $request): bool
    {
        return static::verify($request);
    }
}
