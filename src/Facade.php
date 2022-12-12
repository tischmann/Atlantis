<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use InvalidArgumentException;

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
            if (property_exists($this, $key)) {
                $value ??= '';

                $value = $this->__typify($value, $this->__type($key));

                $this->{$key} = $value;
            }
        }

        return $this;
    }

    /**
     * Возвращает типизированную переменную
     *
     * @param mixed $variable Переменная
     * @param string $type Тип переменной
     * @return mixed Типизированная переменная
     * @throws InvalidArgumentException Если задан неверный формат
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
            case 'DateTime':
                if ($variable && !DateTimeUtilites::isValid($variable)) {
                    throw new InvalidArgumentException(
                        'Invalid date format',
                        500
                    );
                }

                return new $type(strval($variable));
            default:
                return $variable;
        }
    }

    /**
     * Возвращает представление переменной для записи в БД
     *
     * @param mixed $variable Переменная
     * @return mixed Представление переменной или null
     */
    public function __stringify(mixed $variable): mixed
    {
        switch ($this->__type($variable)) {
            case 'bool':
                return intval($variable);
            case 'int':
            case 'float':
                return $variable;
            case 'array':
            case 'object':
                return json_encode($variable, 32 | 256) ?: null;
            case 'DateTime':
                return $variable ? $variable->format('Y-m-d H:i:s') : null;
            default:
                return strval($variable) ?: null;
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
