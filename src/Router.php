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
     * @throws \Exception
     */
    public function resolve(): mixed
    {
        $routes = static::$routes[$this->request->method][$this->request->accept][$this->request->type] ?? null;

        $routes ??= static::$routes[$this->request->method]['any'][$this->request->type] ?? null;

        $routes ??= static::$routes[$this->request->method][$this->request->accept]['any'] ?? null;

        $routes ??= static::$routes[$this->request->method]['any']['any'] ?? null;

        $routes ??= static::$routes['ANY'][$this->request->accept][$this->request->type] ?? null;

        $routes ??= static::$routes['ANY']['any'][$this->request->type] ?? null;

        $routes ??= static::$routes['ANY'][$this->request->accept]['any'] ?? null;

        $routes ??= [];

        $routes = array_merge(
            $routes,
            static::$routes['ANY']['any']['any'] ?? []
        );

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

        throw new Exception("Route not found " . implode("/", $this->request->uri), 404);
    }
}
