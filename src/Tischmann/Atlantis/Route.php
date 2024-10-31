<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Маршрут
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Route
{
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
     * 
     * @param  array  $uri URI маршрута
     */
    public function __construct(
        public ?Controller $controller = new Controller(),
        public string $action = 'index',
        public string $method = 'GET',
        public string $path = '',
        public string $title = '',
        public array $tags = [],
        public array $args = [],
        public array $uri = []
    ) {
        $this->action = trim($this->action);

        $this->method = mb_strtoupper($this->method);

        $this->path = trim($this->path);

        $this->title = trim($this->title);

        if (!$this->uri && $this->path) {
            $this->uri = $this->path ? explode('/', $this->path) : [];
        }
    }

    public function resolve()
    {
        if ($this->title) App::setTitle($this->title);

        if ($this->tags) App::setTags($this->tags);

        $this->controller->route = $this;

        if (!method_exists($this->controller, $this->action)) {
            die(get_str("bad_route"));
        }

        $this->controller->{$this->action}();
    }

    /**
     * Возвращает значение аргумента запроса
     * 
     * @param string $key Ключ аргумента
     * 
     * @return mixed Значение аргумента
     */
    public function args(string $key): mixed
    {
        return $this->args[$key] ?? null;
    }
}
