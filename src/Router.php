<?php

namespace Atlantis;

use Atlantis\Controllers\{Controller};

class Router
{
    public Route $route;

    public array $routes = [
        'get' => [],
        'post' => [],
        'put' => [],
        'delete' => []
    ];

    public function init()
    {
        foreach (glob("../routes/*.php") as $path) {
            require_once $path;
        }
    }

    public function parse(string $path, string $pathToValidate): false|array
    {
        $paths = explode('/', $path);
        $pathsToValidate = explode('/', $pathToValidate);

        if (count($paths) != count($pathsToValidate)) {
            return false;
        }

        $args = [];

        array_walk(
            $paths,
            function ($value, $index) use ($pathsToValidate, &$paths, &$args) {
                if (preg_match('/^\{[a-zA-Z0-9_-]+\}$/', $value)) {
                    $paths[$index] = $pathsToValidate[$index];
                    $key = preg_replace('/\{|\}/', '', $value);
                    $args[$key] = $pathsToValidate[$index];
                }
            }
        );

        $parsedPath = implode('/', $paths);

        if ($parsedPath != $pathToValidate) {
            return false;
        }

        return $args;
    }

    function validate(string $pathToValidate): false|Route
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        foreach ($this->routes[$method] as $path => $route) {
            $args = $this->parse($path, $pathToValidate);

            if ($args === false) {
                continue;
            }

            foreach ($args as $key => $val) {
                $route->args($key, $val);
            }

            return $route;
        }

        return false;
    }

    public function add(
        string $method,
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        $this->routes[$method][$path] = new Route(
            method: $method,
            path: $path,
            controller: new $controller(),
            action: $action,
            args: $args
        );

        return $this->routes[$method][$path];
    }

    public function post(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return $this->add('post', $path, $controller, $action, $args);
    }

    public function get(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return $this->add('get', $path, $controller, $action, $args);
    }

    public function put(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return $this->add('put', $path, $controller, $action, $args);
    }

    public function delete(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return $this->add('delete', $path, $controller, $action, $args);
    }

    public function prefix(string $prefix, array $routes)
    {
        foreach ($routes as $route) {
            unset($this->routes[$route->method][$route->path]);
            $this->routes[$route->method]["{$prefix}{$route->path}"] = $route;
        }

        return $this;
    }

    public function resolve(): Route
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);

        $route = explode('/', $uri['path']);

        array_shift($route);

        $lang = strtolower($route[0] ?? null);

        if (in_array($lang, Language::available())) {
            if (App::$lang->code != $lang) {
                App::$lang->change($lang);
            }
            array_shift($route);
        }

        $path = strtolower(implode("/", $route));

        $route = $this->validate($path);

        if (!$route) {
            die((new Controller())->render('404'));
        } else if (!$route->methodExists()) {
            die((new Controller())->render('404'));
        }

        $this->route = $route;

        return $this->route;
    }

    public function isHome(): bool
    {
        return $this->route->getControllerName() == '' &&
            strtolower($this->route->action) == 'index';
    }
}
