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

    /**
     * Производит проверку токенов
     * 
     * @throws Exception Если токен не найден
     */
    public static function verify(Request $request): void
    {
        $verified = true;

        if (in_array($request->method, ['POST', 'PUT'])) {
            $verified = array_intersect(
                static::tokens(),
                [$request->headers('X-Csrf-Token')]
            );

            if (!$verified) {
                $args = $request->request();

                foreach (static::tokens() as $key => $token) {
                    if (!array_key_exists($key, $args)) continue;
                    if (($verified = $args[$key] === $token)) {
                        static::flush($key);
                        break;
                    }
                }
            }
        }

        if (!$verified) {
            CSRF::flush();
            throw new Exception("CSRF token is required!", 403);
        }
    }
}
