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
        public string $method = '',
        public array $uri = [],
    ) {
        $this->method = $this->method ?: Request::method();
        $this->uri = $this->uri ?: Request::uri();
    }

    /**
     * Запуск маршрутизатора
     */
    public static function bootstrap(): void
    {
        (new static())->resolve();
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

        static::$routes[$route->method][] = $route;
    }

    /**
     * Разрешение маршрута
     *
     * @return mixed
     * @throws Exception
     */
    public function resolve(): mixed
    {
        foreach ($this->routes() as $route) {
            assert($route instanceof Route);

            if ($route->uri xor $this->uri) continue;

            if (!$route->uri && !$this->uri) {
                return $route->resolve();
            }

            if ($route->validate($this->uri)) {
                return $route->resolve();
            }
        }

        $this->routeNotFound();
    }

    /**
     * Маршрут не найден
     *
     * @return void
     */
    protected function routeNotFound(): void
    {
        View::send(
            view: '404',
            layout: 'default',
            args: [
                'exception' => new Exception(
                    get_str('route_not_found') . ": '{$_SERVER['REQUEST_URI']}'"
                )
            ],
            exit: true,
            code: 404
        );
    }

    /**
     * Получение маршрутов
     *
     * @return array Маршруты
     */
    protected function routes(): array
    {
        return static::$routes[$this->method] ?? [];
    }
}
