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

    private function __construct() {}

    public static function add(Route $route): void
    {
        static::$routes[$route->method] ??= [];

        static::$routes[$route->method][] = $route;
    }

    public static function resolve()
    {
        $uri = Request::uri();

        foreach (static::$routes[Request::method()] ?? [] as $route) {
            assert($route instanceof Route);

            if ($route->uri xor $uri) continue;

            if (!$route->uri && !$uri) return $route->resolve();

            if ($route->validate($uri)) return $route->resolve();
        }

        View::send(
            view: 'error',
            layout: 'default',
            args: [
                'exception' => new Exception(get_str('route_not_found') . ": '{$uri}'"),
                'title' => get_str('not_found'),
                'code' => '404'
            ],
            exit: true,
            code: 404
        );
    }
}
