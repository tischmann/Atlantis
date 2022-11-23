<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

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

        $routes ??= static::$routes['ANY']['any']['any'] ?? null;

        $routes ??= [];

        foreach ($routes as $route) {
            assert($route instanceof Route);

            if ($route->validate($this->request->uri)) {
                return $route->resolve($this->request);
            }
        }

        throw new \Exception(Locale::get('error_404'), 404);
    }
}
