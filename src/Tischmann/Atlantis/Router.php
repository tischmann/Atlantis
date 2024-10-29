<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Маршрутизатор
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Router
{
    private static array $routes = [];

    private const REGEX_ARGS = "/^\{(\?)?(\w+)\}$/"; // Регулярное выражение для аргументов

    private function __construct() {}

    public static function add(Route $route): void
    {
        static::$routes[$route->method] ??= [];

        static::$routes[$route->method][] = $route;
    }

    public static function bootstrap()
    {
        $uri = Request::uri();

        foreach (static::$routes[Request::method()] ?? [] as $route) {
            assert($route instanceof Route);

            if ($route->uri xor $uri) continue;

            if (!$route->uri && !$uri) return $route->resolve();

            if (static::validate($uri, $route)) return $route->resolve();
        }

        View::send(
            view: 'error',
            layout: 'default',
            args: [
                'exception' => new Exception(get_str('route_not_found') . ": " . implode('/', $uri)),
                'title' => get_str('not_found'),
                'code' => '404'
            ],
            exit: true,
            code: 404
        );
    }

    public static function validate(array $uri, Route &$route): bool
    {
        if (count($uri) !== count($route->uri)) return false;

        $args = [];

        foreach ($route->uri as $index => $chunk) {
            if (!preg_match(static::REGEX_ARGS, $chunk, $matches)) continue;

            $value = strval($uri[$index]);

            $optional = $matches[1] === '?';

            $key = $matches[2];

            if (!mb_strlen($value) && !$optional) continue;

            $route->uri[$index] = $args[$key] = $value;
        }

        if (array_diff($uri, $route->uri)) return false;

        $route->args = array_merge($route->args, $args);

        return true;
    }
}
