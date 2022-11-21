<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\Exceptions\{RouteNotFoundException};

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
     * @throws RouteNotFoundException Маршрут не найден
     */
    public function resolve(): mixed
    {
        $routes = array_merge(
            static::$routes['ANY']['any']['any'] ?? [],
            static::$routes[$this->request->method]['any']['any'] ?? [],
            static::$routes['ANY'][$this->request->accept]['any'] ?? [],
            static::$routes['ANY']['any'][$this->request->type] ?? [],
            static::$routes[$this->request->method][$this->request->accept]['any'] ?? [],
            static::$routes[$this->request->method]['any'][$this->request->type] ?? [],
            static::$routes['ANY'][$this->request->accept][$this->request->type] ?? [],
            static::$routes[$this->request->method][$this->request->accept][$this->request->type] ?? []
        );

        foreach ($routes as $route) {
            assert($route instanceof Route);

            if ($route->validate($this->request->uri)) {
                return $route->resolve(request: $this->request);
            }
        }

        throw new RouteNotFoundException();
    }
}
