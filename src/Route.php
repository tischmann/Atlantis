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

    public function action()
    {
        return $this->controller->{$this->action}(...$this->args);
    }
}
