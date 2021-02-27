<?php

namespace Atlantis;

use DateTime;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

class Model
{
    public static string $tableName = 'undefined';

    function __construct(array|stdClass|int $args = null)
    {
        if (!$args) {
            return $this;
        }

        if (is_int($args)) {
            $args = $this::where('id', '=', $args)->first();
        }

        if (is_array($args) || is_object($args)) {
            $this->init($args);
        }
    }

    public function getClassName(): string
    {
        $class = explode('\\', get_class($this));
        return end($class);
    }

    function getPropertyName(string $column): string
    {
        $words = explode('_', $column);
        $name = array_shift($words);

        foreach ($words as $word) {
            $name .= ucfirst($word);
        }

        return $name;
    }

    function getPropertyType(string $property): string
    {
        if (!property_exists($this, $property)) {
            return 'default';
        }

        $reflectionProperty = new ReflectionProperty(
            get_class($this),
            $property
        );

        $propertyType = $reflectionProperty->getType();

        assert($propertyType instanceof ReflectionNamedType);

        return $propertyType->getName();
    }

    public function init($args)
    {
        foreach ($args as $prop => $value) {
            switch ($this->getPropertyType($prop)) {
                case 'bool':
                    $this->{$prop} = (bool) $value;
                    break;
                case 'int':
                    $this->{$prop} = (int) $value;
                    break;
                case 'float':
                    $this->{$prop} = (float) $value;
                    break;
                case 'array':
                    $this->{$prop} = json_decode($value, true) ?? [];
                    break;
                case 'string':
                    $this->{$prop} = (string) $value;
                    break;
                case 'DateTime':
                    $this->{$prop} = new DateTime($value ?? 'now');
                    break;
                case 'stdClass':
                    $this->{$prop} = json_decode($value) ?? (object) [];
                    break;
                default:
                    $this->{$prop} = $value;
                    break;
            }
        }

        return $this;
    }

    public function reset()
    {
        $className = get_class($this);
        $clean = new $className();

        foreach ($this as $key => $val) {
            if (isset($clean->$key)) {
                $this->$key = $clean->$key;
            } else {
                unset($this->$key);
            }
        }

        return $this;
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(Query::class, $name)) {
            return (new Query())->table(get_called_class()::$tableName)->$name(...$arguments);
        }
    }
}
