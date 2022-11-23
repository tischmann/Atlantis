<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Фасад для классов приложения.
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Facade
{
    public function __construct()
    {
    }

    /**
     * Магический метод для клонирования объекта
     */
    public function __clone()
    {
        foreach ($this as $property => $value) {
            if (is_object($value)) {
                $reflectionClass = new \ReflectionClass($value);

                if ($reflectionClass->isCloneable()) {
                    $this->{$property} = clone $value;
                }
            }
        }
    }

    /**
     * Создаёт экземпляр класса
     *
     * @return self Экземпляр класса
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Заполняет свойства класса данными
     * Если свойство не существует или недоступно для записи, то оно игнорируется
     *
     * @param object|array $traversable Объект, который можно перебрать
     * 
     * @return self
     */
    public function __fill(object|array $traversable): self
    {
        foreach ($traversable as $key => $value) {
            if (!property_exists($this, $key)) continue;
            $this->{$key} = $this->__typify($value ??= '', $this->__type($key));
        }

        return $this;
    }

    /**
     * Возвращает типизированную переменную
     *
     * @param mixed $variable Переменная
     * @param string $type Тип переменной
     * @return mixed Типизированная переменная
     */
    public function __typify(mixed $variable, string $type): mixed
    {
        switch ($type) {
            case 'bool':
                return boolval($variable);
            case 'int':
                return intval($variable);
            case 'float':
                return floatval($variable);
            case 'array':
                return json_decode($variable, true) ?? [];
            case 'object':
                return json_decode($variable) ?? (object) [];
            case 'Tischmann\Atlantis\Time':
            case 'Tischmann\Atlantis\Date':
            case 'Tischmann\Atlantis\DateTime':
            case 'DateTime':
                return $type::validate($variable) ? new $type($variable) : null;
            default:
                return $variable;
        }
    }

    /**
     * Возвращает тип данных свойства
     *
     * @param string $property Имя свойства
     * @return string Тип данных
     */
    public function __type(string $property): string
    {
        if (!property_exists($this, $property)) return 'mixed';

        $reflectionProperty = new \ReflectionProperty($this, $property);

        $reflectionNamedType = $reflectionProperty->getType();

        assert($reflectionNamedType instanceof \ReflectionNamedType);

        return $reflectionNamedType?->getName() ?? 'mixed';
    }
}
