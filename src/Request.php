<?php

namespace Atlantis;

use Atlantis\Controllers\Controller;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

class Request
{
    public function __construct()
    {
        self::sanitize();

        foreach ($_REQUEST as $key => $val) {
            $this->{$key} = $val;
        }

        foreach (json_decode(file_get_contents("php://input")) ?? [] as $key => $val) {
            $this->{$key} = $val;
        }
    }

    public function getVariableType(string $key): string|false
    {
        $property = new ReflectionProperty($this, $key);
        $type = $property->getType();

        $value = $this->{$key};

        if ($type === null) {
            if ((int)$value === $value) {
                return 'int';
            } else if ((string)$value === $value) {
                return 'string';
            } else if ((array)$value === $value) {
                return 'array';
            } else if ((object)$value === $value) {
                return 'object';
            } else {
                return false;
            }
        }

        assert($type instanceof ReflectionNamedType);

        return $type->getName();
    }

    public function validate(array $array): bool
    {
        foreach ($array as $key => $validation) {
            $variable = $this->{$key} ?? self::request($key) ?? null;

            if ($variable === null) {
                App::$error = new Error(
                    message: "Параметр {$key} не задан"
                );
                return false;
            }

            foreach (explode('|', $validation) as $isType) {
                if (!$isType) {
                    continue;
                }

                $type = $this->getVariableType($key);

                if ($type === false) {
                    continue;
                }

                if (strtolower($type) != strtolower($isType)) {
                    App::$error = new Error(
                        message: "Тип данных {$key}: {$type} вместо {$isType}"
                    );
                    return false;
                }
            }
        }

        return true;
    }

    private static function sanitize()
    {
        $_GET = (array) filter_input_array(INPUT_GET, 513);
        $_POST = (array) filter_input_array(INPUT_POST, 513);
        $_COOKIE = (array) filter_input_array(INPUT_COOKIE, 513);
        $_REQUEST = array_merge($_POST, $_GET);
    }

    public static function get(string $key)
    {
        return $_GET[$key] ?? null;
    }

    public static function post(string $key)
    {
        return $_POST[$key] ?? null;
    }

    public static function request(string $key)
    {
        return $_REQUEST[$key] ?? null;
    }

    public static function cookie(string $key)
    {
        return $_COOKIE[$key] ?? null;
    }

    public static function files(string $key)
    {
        return $_FILES[$key] ?? null;
    }

    public static function headers()
    {
        return apache_request_headers();
    }

    public static function bearer()
    {
        $header = '';

        if (isset($_SERVER['Authorization'])) {
            $header = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $headers = self::headers();
            $headers = array_combine(
                array_map('ucwords', array_keys($headers)),
                array_values($headers)
            );

            if (isset($headers['Authorization'])) {
                $header = trim($headers['Authorization']);
            }
        }

        preg_match('/Bearer\s(\S+)/', $header, $matches);

        return $matches[1] ?? null;
    }
}
