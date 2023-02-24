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
    public static array $routes = []; // Маршруты приложения

    public function __construct(
        public Request $request = new Request(),
    ) {
    }

    /**
     * Запуск маршрутизатора
     */
    public static function bootstrap(): void
    {
        (new static(request: new Request()))->resolve();
    }

    /**
     * Добавление маршрута
     *
     * @param Route $route Маршрут
     * @return void
     */
    public static function add(Route $route): void
    {
        static::$routes[$route->method] ??= [];

        static::$routes[$route->method][$route->accept] ??= [];

        static::$routes[$route->method][$route->accept][$route->type] ??= [];

        static::$routes[$route->method][$route->accept][$route->type][] = $route;
    }

    /**
     * Разрешение маршрута
     *
     * @return mixed
     * @throws Exception
     */
    public function resolve()
    {
        $routes = static::$routes[$this->request->method] ?? [];

        $routes = $routes[$this->request->accept] ?? [];

        $routes = $routes[$this->request->type] ?? [];

        foreach ($routes as $route) {
            assert($route instanceof Route);

            if ($route->uri && $this->request->uri) {
                if ($route->validate($this->request->uri)) {
                    return $route->resolve($this->request);
                }
            } else if (!$route->uri && !$this->request->uri) {
                return $route->resolve($this->request);
            } else {
                continue;
            }
        }

        $path = '"' . implode("/", $this->request->uri) . '"';

        throw new Exception(Locale::get('route_not_found') . ": {$path}", 404);
    }
}
