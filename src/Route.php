<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Маршрут
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Route
{
    public array $uri = []; // URI маршрута

    public const REGEX_ARGS = "/^\{(\?)?(\w+)\}$/"; // Регулярное выражение для аргументов
    /**
     * @param Controller $controller Контроллер маршрута
     * 
     * @param string $action Метод контроллера
     * 
     * @param string $method Метод запроса
     * 
     * @param string $path Путь
     * 
     * @param string $title Заголовок страницы 
     * 
     * @param array $args Аргументы запроса
     */
    public function __construct(
        public ?Controller $controller = new Controller(),
        public string $action = 'index',
        public string $method = 'GET',
        public string $path = '',
        public string $title = '',
        public array $args = [],
    ) {
        $this->method = strtoupper($this->method);

        $this->path = trim($this->path);

        $this->uri = $this->path ? explode('/', $this->path) : [];
    }

    /**
     * Валидация маршрута
     *
     * @param array $uri URI запроса
     * 
     * @return bool true - маршрут валиден, false - маршрут не валиден
     */
    public function validate(array $uri): bool
    {
        if (count($uri) !== count($this->uri)) return false;

        $args = [];

        foreach ($this->uri as $index => $chunk) {
            if (!preg_match(self::REGEX_ARGS, $chunk, $matches)) continue;

            $value = strval($uri[$index]);

            $optional = $matches[1] === '?';

            $key = $matches[2];

            if (!strlen($value) && !$optional) {
                continue;
            }

            $this->uri[$index] = $args[$key] = $value;
        }

        if (array_diff($uri, $this->uri)) return false;

        $this->args = array_merge($this->args, $args);

        return true;
    }

    /**
     * Выполнение маршрута
     * 
     * @param Request $request Запрос
     *
     * @return void
     */
    public function resolve(Request $request): void
    {
        foreach ($this->args as $key => $value) {
            $request->route($key, $value);
        }

        if ($this->title) Controller::setTitle($this->title);

        $this->controller->{$this->action}($request);
    }
}
