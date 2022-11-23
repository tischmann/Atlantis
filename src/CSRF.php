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
    public static function flush()
    {
        $_SESSION[static::SESSION_KEY] = [];
    }

    /**
     * Возвращает токены
     * 
     * @return array Токены
     */
    public static function tokens(): array
    {
        $_SESSION[static::SESSION_KEY] ??= [];
        return $_SESSION[static::SESSION_KEY];
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
                    if (($verified = $args[$key] === $token)) break;
                }
            }
        }

        CSRF::flush();

        if (!$verified) throw new Exception(Locale::get('error_403'), 403);
    }
}
