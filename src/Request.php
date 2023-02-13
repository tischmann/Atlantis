<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use InvalidArgumentException;

/**
 * Класс HTTP запроса
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Request
{
    public array $uri = []; // URI запроса
    public string $method = 'GET'; // Метод запроса
    public string $type = 'html'; // Тип данных
    public string $accept = 'html'; // Тип ответа
    private array $__route = []; // Переменные маршрута
    private array $__get = []; // GET параметры
    private array $__post = []; // POST параметры
    private array $__input = []; // Переменные php://input
    private array $__headers = []; // HTTP-Заголовки
    private array $__files = []; // Файлы
    private array $__args = []; // Аргументы запроса

    public function __construct()
    {
        $this->uri = self::uri();

        $this->method = self::method();

        $this->type = self::type();

        $this->accept = self::accept();

        $this->__get = array_map('static::sanitize', $_GET);

        $this->__post = array_map('static::sanitize', $_POST);

        $this->__input = array_map(
            'static::sanitize',
            json_decode(file_get_contents("php://input"), true) ?: []
        );

        $this->__headers = array_map(
            'static::sanitize',
            apache_request_headers()
        );

        $this->__files = $_FILES;
    }

    /**
     * Возвращает метод запроса
     * 
     * @return string Метод запроса
     */
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
    }

    /**
     * Возвращает URI запроса в виде массива
     * 
     * @return array URI запроса
     */
    public static function uri(): array
    {
        $uri = parse_url(strtolower($_SERVER['REQUEST_URI']));

        $uri = is_array($uri) ? $uri : [];

        $chunks = explode('/', $uri['path'] ?? '');

        $uri = [];

        foreach ($chunks as $chunk) {
            if (empty($chunk)) continue;

            $uri[] = $chunk;
        }

        $chunk = strval(reset($uri));

        if (Locale::exists($chunk)) {
            putenv("APP_LOCALE={$chunk}");
            array_shift($uri);
        }

        if (count($uri) === 1) {
            if ($uri[0] === '') return [];
        }

        return $uri;
    }

    /**
     * Фильтрует переменную
     * 
     * @param string $value Переменная
     * @return mixed Фильтрованная переменная
     */
    public static function sanitize(mixed $value): mixed
    {
        return match (gettype($value)) {
            'integer' => intval(filter_var($value, 519)),
            'double' => floatval(filter_var($value, 520)),
            'string' => htmlspecialchars($value),
            default =>  $value,
        };
    }

    /**
     * Возвращает локаль из URI
     * 
     * @return string Локаль
     */
    public static function locale(): ?string
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);

        if (!is_array($uri)) return null;

        $uri = explode('/', strtolower($uri['path']));

        array_shift($uri);

        $locale = reset($uri);

        return Locale::exists($locale) ? $locale : null;
    }

    /**
     * Возвращает значение переменной авторизации запроса
     * 
     * @return string|null Значение переменной авторизации запроса
     */
    public static function authorization(): ?string
    {
        $header = '';

        $headers = [];

        if (isset($_SERVER['Authorization'])) {
            $header = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $headers = static::sanitize(apache_request_headers());
            $headers = array_combine(
                array_map('ucwords', array_keys($headers)),
                array_values($headers)
            );

            if (isset($headers['Authorization'])) {
                $header = trim($headers['Authorization']);
            }
        }

        $headers = static::sanitize($headers);

        preg_match('/Bearer\s(\S+)/', $header, $matches);

        return $matches[1] ?? null;
    }

    /**
     * Валидация переменных запроса
     *
     * @param array $vars Массив валидации: ['name' => ['type' | 'required'], ...] 
     * @return self
     * @throws InvalidArgumentException В случае ошибки валидации
     */
    public function validate(array $vars): self
    {
        foreach ($vars as $key => $types) {
            if (!in_array('required', $types)) continue;

            $variable = $this->request($key);

            if ($variable === null || $variable === '') {
                throw new InvalidArgumentException("{$key} is required");
            }

            foreach (array_diff($types, ['required']) as $assert) {
                if (!strlen($assert)) continue;

                $assert = strtolower($assert);

                $type = gettype($variable);

                if ($type === $assert) continue;

                throw new InvalidArgumentException(
                    "Invalid type: {$key} ({$type} != {$assert})"
                );
            }
        }

        return $this;
    }

    /**
     * Возвращает или устанавливает значение переменной $_GET
     * 
     * @param mixed ...$args Аргументы:
     * 
     * - Если не передано ни одного значения, то возвращает массив переменных $_GET
     * - Если передано одно значение, то возвращает значение переменной $_GET
     * - Если передано два значения, то устанавливает значение переменной $_GET
     *
     * @return mixed Значение переменной $_GET или сам массив переменных $_GET
     */
    public function get(...$args): mixed
    {
        switch (count($args)) {
            case 0:
                return $this->__get;
            case 1:
                return $this->__get[$args[0]] ?? null;
            case 2:
                $this->__get[$args[0]] = static::sanitize($args[1]);
                return $this->__get[$args[0]];
        }

        return null;
    }

    /**
     * Возвращает или устанавливает значение переменной $_POST
     * 
     * @param mixed ...$args Аргументы:
     * 
     * - Если не передано ни одного значения, то возвращает массив переменных $_POST
     * - Если передано одно значение, то возвращает значение переменной $_POST
     * - Если передано два значения, то устанавливает значение переменной $_POST
     *
     * @return mixed Значение переменной $_POST или сам массив переменных $_POST
     */
    public function post(...$args): mixed
    {
        switch (count($args)) {
            case 0:
                return $this->__post;
            case 1:
                return $this->__post[$args[0]] ?? null;
            case 2:
                $this->__post[$args[0]] = static::sanitize($args[1]);
                return $this->__post[$args[0]];
        }

        return null;
    }

    /**
     * Возвращает значение переменной php://input
     *
     * @param mixed $key Имя переменной php://input
     * @return mixed Значение переменной
     */
    public function input(string $key): mixed
    {
        return $this->__input[$key] ?? null;
    }

    /**
     * Возвращает значение переменной HTTP заголовка
     *
     * @param mixed $key Имя переменной HTTP заголовка
     * @return mixed Значение переменной
     */
    public function headers(string $key): mixed
    {
        return $this->__headers[$key] ?? null;
    }

    /**
     * Возвращает значение переменной запроса, производя поиск во всех источниках
     *
     * @param mixed $key Имя переменной запроса
     * @return mixed Значение переменной запроса, если не найдено, то null
     */
    public function request(?string $key = null): mixed
    {
        if ($key === null) {
            return array_merge(
                $this->__get,
                $this->__post,
                $this->__args,
                $this->__route,
                $this->__input
            );
        }

        return $this->__route[$key]
            ?? $this->__args[$key]
            ?? $this->__input[$key]
            ?? $this->__post[$key]
            ?? $this->__get[$key]
            ?? null;
    }

    /**
     * Возвращает или устанавливает значение переменной маршрута
     * 
     * @param mixed ...$args Аргументы:
     * 
     * - Если не передано ни одного значения, то возвращает массив переменных маршрута
     * - Если передано одно значение, то возвращает значение переменной маршрута
     * - Если передано два значения, то устанавливает значение переменной маршрута
     *
     * @return mixed Значение переменной маршрута или сам массив переменных маршрута
     */
    public function route(...$args): mixed
    {
        switch (count($args)) {
            case 0:
                return $this->__route;
            case 1:
                return $this->__route[$args[0]] ?? null;
            case 2:
                $this->__route[$args[0]] = static::sanitize($args[1]);
                return $this->__route[$args[0]];
        }

        return null;
    }

    /**
     * Возвращает или устанавливает значение переменной запроса
     * 
     * @param mixed ...$args Аргументы:
     * 
     * - Если не передано ни одного значения, то возвращает массив переменных запроса
     * - Если передано одно значение, то возвращает значение переменной запроса
     * - Если передано два значения, то устанавливает значение переменной запроса
     *
     * @return mixed Значение переменной запроса или сам массив переменных запроса
     */
    public function args(...$args): mixed
    {
        switch (count($args)) {
            case 0:
                return $this->__args;
            case 1:
                return $this->__args[$args[0]] ?? null;
            case 2:
                $this->__args[$args[0]] = $args[1];
                return $this->__args[$args[0]];
        }

        return null;
    }

    /**
     * Воозвращает массив файлов, загруженных в запросе ($_FILES)
     *
     * @return array Массив файлов
     */
    public function files(): array
    {
        return $this->__files;
    }

    /**
     * Возвращает тип данных для ответа
     * 
     * @return string Тип данных для ответа (json, html, xml, text, any)
     */
    public static function accept(): string
    {
        $headers = implode(" ", headers_list());

        $headers .= " " . ($_SERVER['HTTP_ACCEPT'] ?? '');

        return match (true) {
            str_contains($headers, 'application/json') => 'json',
            str_contains($headers, 'text/html') => 'html',
            str_contains($headers, 'text/xml') => 'xml',
            str_contains($headers, 'text/plain') => 'text',
            default => 'any',
        };
    }

    /**
     * Возвращает тип данных запроса
     * 
     * @return string Тип данных запроса (json, html, xml, text, form, any)
     */
    public static function type(): string
    {
        $headers = $_SERVER['CONTENT_TYPE']  ?? '';

        $headers .= " " . ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

        return match (true) {
            str_contains($headers, 'application/json') => 'json',
            str_contains($headers, 'text/html') => 'html',
            str_contains($headers, 'text/xml') => 'xml',
            str_contains($headers, 'text/plain') => 'text',
            str_contains($headers, 'application/x-www-form-urlencoded') => 'form',
            str_contains($headers, 'multipart/form-data') => 'form',
            default => 'any',
        };
    }
}
