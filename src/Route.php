<?php

namespace Atlantis;

use Atlantis\Controllers\Controller;

class Route
{
    public string $method;
    public string $path;
    public Controller $controller;
    public string $action;
    public array $args = [];

    public function __construct(
        string $method,
        string $path,
        Controller $controller,
        string $action = 'index',
        array $args = []
    ) {
        $this->method = $method;
        $this->path = $path;
        $this->controller = $controller;
        $this->action = $action;
        $this->args = $args;
    }

    public function args(...$args)
    {
        switch (count($args)) {
            case 1:
                return $this->args[$args] ?? null;
            case 2:
                $this->args[$args[0]] = $args[1];
                return $args[1];
        }
    }

    public function methodExists()
    {
        return method_exists($this->controller, $this->action);
    }

    public function action()
    {
        $action = $this->action;
        return $this->controller->$action(...$this->args);
    }

    public function getControllerName(): string
    {
        $controller = str_replace("\\", "/", get_class($this->controller));
        return basename($controller);
    }
}
