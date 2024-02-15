<?php

declare(strict_types=1);

use Tischmann\Atlantis\Request;

/**
 * Получение значения куки
 *
 * @param string $name Имя куки
 * @return string|null Значение куки или null, если кука не существует
 */
function cookies_get(string $name): ?string
{
    $cookie = array_map('sanitize', $_COOKIE);

    return $cookie[$name] ?? null;
}

/**
 * Проверка наличия куки
 *
 * @param string $name Имя куки
 * @return boolean Если кука существует, то true, иначе false
 */
function cookies_has(string $name): bool
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
function cookies_set(
    string $name,
    mixed $value,
    array $options = []
): bool {
    return setcookie(
        $name,
        strval($value),
        array_merge([
            'expires' => 0,
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
function cookies_del(string $name, array $options = []): void
{
    cookies_set(
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
function sanitize(mixed $value): mixed
{
    return match (gettype($value)) {
        'integer' => intval(filter_var($value, 519)),
        'double' => floatval(filter_var($value, 520)),
        'string' => strval(filter_var($value, 513)),
        default =>  $value,
    };
}

/**
 * Запуск сессии
 *
 * @return bool
 */
function session_init(
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
function session_set(string $key, mixed $value): void
{
    $_SESSION[$key] = $value;
}

/**
 * Проверка наличия значения в сессии
 *
 * @param string $key Ключ
 * @return bool true - если значение есть, false - если значения нет
 */
function session_has(string $key): bool
{
    return key_exists($key, $_SESSION ?? []);
}

/**
 * Проверка наличия значения в сессии
 *
 * @param string $key Ключ
 * 
 * @param callable $setter Функция, которая устанавливает значение в сессию
 * 
 * @return mixed Значение
 */
function session_find(string $key, callable $setter): mixed
{
    if (!session_has($key)) {
        $value = $setter();

        session_set($key, $value);

        return $value;
    }

    return session_get($key);
}

/**
 * Получение значения из сессии
 *
 * @param string $key Ключ
 * @return mixed Значение или null, если значения нет
 */
function session_get(string $key): mixed
{
    return $_SESSION[$key] ?? null;
}

/**
 * Удаление значения из сессии
 *
 * @param string $key Ключ
 * @return void
 */
function session_del(string $key): void
{
    if (session_has($key)) unset($_SESSION[$key]);
}

/**
 * Удаление всех значений из сессии
 *
 * @return void
 */
function session_kill(): void
{
    session_unset();
    session_regenerate_id();
}

function csrf_session_key(): string
{
    return 'ATLANTIS_CSRF_TOKENS_' . strval(getenv('APP_ID'));
}

/**
 * Удаляет все токены
 */
function csrf_flush(string $key = null)
{
    if ($key === null) {
        session_del(csrf_session_key());
    } else {
        $tokens = csrf_tokens();
        unset($tokens[$key]);
        session_set(csrf_session_key(), $tokens);
    }
}

/**
 * Возвращает токены
 * 
 * @return array Токены
 */
function csrf_tokens(): array
{
    return session_find(csrf_session_key(), function () {
        return [];
    });
}

/**
 * Устанавливает токен
 * 
 * @return object (object)[key => 'Key', token => 'Token']
 */
function csrf_set(): object
{
    $key = bin2hex(random_bytes(128));

    $token = bin2hex(random_bytes(128));

    $tokens = csrf_tokens();

    $tokens[$key] = $token;

    session_set(csrf_session_key(), $tokens);

    return (object)['key' => $key, 'token' => $token];
}

/**
 * Производит проверку токенов
 * 
 * @return bool Результат проверки
 */
function csrf_verify(): bool
{
    $method = mb_strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    if (!in_array($method, ['POST', 'PUT', 'DELETE'])) return true;

    $token = apache_request_headers()['X-Csrf-Token'] ?? null;

    $key = $token !== null ? array_search($token, csrf_tokens()) : false;

    if ($key !== false) {
        csrf_flush($key);
        return true;
    }

    foreach (csrf_tokens() as $key => $token) {
        if (array_key_exists($key, $_REQUEST)) {
            if ($_REQUEST[$key] === $token) {
                csrf_flush($key);
                return true;
            }
        }
    }

    csrf_flush();

    return false;
}

/**
 * Проверяет, что токен не прошел проверку
 * 
 * @return boolean
 */
function csrf_failed(): bool
{
    return !csrf_verify();
}

/**
 * Проверяет, что токен прошел проверку
 * 
 * @return boolean
 */
function csrf_passed(): bool
{
    return csrf_verify();
}
