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

        static::$routes[$route->method][] = $route;
    }

    /**
     * Разрешение маршрута
     *
     * @return mixed
     * @throws Exception
     */
    public function resolve()
    {
        foreach ($this->routes() as $route) {
            assert($route instanceof Route);

            if ($route->uri xor $this->request->uri) {
                continue;
            } elseif (!$route->uri && !$this->request->uri) {
                return $route->resolve($this->request);
            } elseif ($route->validate($this->request->uri)) {
                return $route->resolve($this->request);
            }
        }

        $title = Locale::get('route_not_found') . " - " . getenv('APP_TITLE');

        putenv("APP_TITLE={$title}");

        View::echo(
            view: 'exception',
            args: [
                'code' => 404,
                'message' => Locale::get('route_not_found'),
            ]
        );

        exit;
    }

    /**
     * Получение маршрутов
     *
     * @return array Маршруты
     */
    protected function routes(): array
    {
        return static::$routes[$this->request->method] ?? [];
    }
}
