<?php

namespace Atlantis;

class Router
{
    public static array $parsedRoute;
    public static Route $route;

    public static array $routes = [
        'get' => [],
        'post' => [],
        'put' => [],
        'delete' => []
    ];

    public static function load()
    {
        foreach (glob("../routes/*.php") as $path) {
            require_once $path;
        }
    }

    public static function init()
    {
        $uri = parse_url($_SERVER['REQUEST_URI']);

        static::$parsedRoute = explode('/', $uri['path']);

        array_shift(static::$parsedRoute);

        $lang = strtolower(static::$parsedRoute[0] ?? null);

        if (in_array($lang, Language::available())) {
            Session::cookie('language', $lang,  [
                'expires' => time() + 60 * 60 * 24 * 14,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => false,
                'samesite' => 'Strict'
            ]);
            $_COOKIE['language'] = $lang;
            array_shift(static::$parsedRoute);
        }
    }

    public static function action()
    {
        static::$route->action();
    }

    public static function parse(string $path, string $pathToValidate): false|array
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

    public static function validate(string $pathToValidate): false|Route
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        foreach (static::$routes[$method] as $path => $route) {
            $args = self::parse($path, $pathToValidate);

            if ($args === false) {
                continue;
            }

            $route->args = $args;

            return $route;
        }

        return false;
    }

    public static function add(
        string $method,
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        static::$routes[$method][$path] = new Route(
            method: $method,
            path: $path,
            controller: new $controller(),
            action: $action,
            args: $args
        );

        return static::$routes[$method][$path];
    }

    public static function post(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return self::add('post', $path, $controller, $action, $args);
    }

    public static function get(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return self::add('get', $path, $controller, $action, $args);
    }

    public static function put(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return self::add('put', $path, $controller, $action, $args);
    }

    public static function delete(
        string $path,
        string $controller,
        string $action = 'index',
        array $args = []
    ): Route {
        return self::add('delete', $path, $controller, $action, $args);
    }

    public static function prefix(string $prefix, array $routes)
    {
        foreach ($routes as $route) {
            unset(static::$routes[$route->method][$route->path]);
            static::$routes[$route->method]["{$prefix}{$route->path}"] = $route;
        }
    }

    public static function resolve()
    {
        $route = self::validate(strtolower(implode("/", static::$parsedRoute)));

        if (!$route) {
            Response::response(
                new Error(
                    status: 404,
                    message: lang('error_bad_route')
                )
            );
        } else if (!method_exists($route->controller, $route->action)) {
            Response::response(new Error(
                status: 404,
                message: lang('error_method_not_exists') . ": {$route->action}"
            ));
        }

        static::$route = $route;
    }
}
