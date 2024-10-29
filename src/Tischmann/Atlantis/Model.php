<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\{Table};

abstract class Model
{
    public static $cache = [];
    /**
     * Таблица модели
     * 
     * @return Table Таблица
     */
    abstract public static function table(): Table;


    /**
     * Проверяет, существует ли кеш
     *
     * @param string $key Ключ
     * @return boolean true если существует, false если нет
     */
    public static function hasCache(string $key): bool
    {
        return isset(static::$cache[$key]);
    }

    /**
     * Возвращает кеш
     *
     * @param string $key Ключ
     * @param callable|null $setter Функция для установки значения
     * @return mixed Значение кеша или null если его нет
     */
    public static function getCache(string $key, ?callable $setter = null): mixed
    {
        if (static::hasCache($key)) return static::$cache[$key];

        if (is_callable($setter)) static::setCache($key, $setter());

        return static::$cache[$key] ?? null;
    }

    /**
     * Устанавливает кеш
     *
     * @param string $key Ключ
     * @param mixed $value Значение кеша
     * @return mixed Значение кеша
     */
    public static function setCache(string $key, mixed $value): mixed
    {
        return static::$cache[$key] = $value;
    }

    /**
     * Удаляет кеш
     *
     * @param string $key Ключ
     * @return void
     */
    public static function delCache(string $key): void
    {
        unset(static::$cache[$key]);
    }

    public function __construct(
        public int $id = 0,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
        $this->__init();
    }

    /**
     * Запрос для модели
     *
     * @param Query
     */
    public static function query(): Query
    {
        return static::table()::query();
    }

    /**
     * Инициализация класса
     *
     * @return self
     */
    public function __init(): self
    {
        return $this;
    }

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
     * @param array|object|null $fill Данные для заполнения свойств класса
     * @return self Экземпляр класса
     */
    public static function instance(array|object|null $fill = null): static
    {
        $model = new static();

        if ($fill === null) return $model;

        return $model->__fill($fill);
    }

    /**
     * Заполняет свойства класса данными
     * Если свойство не существует или недоступно для записи, то оно игнорируется
     *
     * @param array|object|null $traversable Объект, который можно перебрать
     * 
     * @return self
     */
    public function __fill(array|object|null $traversable = null): self
    {
        if ($traversable === null) return $this->__init();

        if ($traversable instanceof Query) $traversable = $traversable->first();

        foreach ($traversable as $property => $value) {
            if (property_exists($this, $property)) {
                $value ??= null;

                $type = get_property_type($this, $property);

                $value = typify($value, $type);

                $this->{$property} = $value;
            }
        }

        return $this->__init();
    }

    /**
     * Возвращает все модели из базы данных по запросу
     *
     * @param Query $query Запрос
     * @return array Модели
     */
    public static function all(Query $query): array
    {
        $models = [];

        foreach ($query->get() as $fill) {
            $models[] = static::instance($fill);
        }

        return $models;
    }

    /**
     * Проверяет, существует ли модель в базе данных
     *
     * @return boolean true если существует, false если нет
     */
    public function exists(): bool
    {
        return $this->id > 0;
    }

    /**
     * Сохраняет состояние модели в базе данных
     * 
     * если модель не определена, то добавляет ее, если определена, то обновляет
     *
     * @return boolean true если сохранена, false если нет
     */
    public function save(): bool
    {
        return $this->exists() ? $this->update() : $this->insert();
    }

    /**
     * Добавляет модель в базу данных
     *
     * @return boolean true если добавлена, false если нет
     */
    public function insert(): bool
    {
        if ($this->exists()) return $this->update();

        $values = [];

        $this->created_at = new DateTime();

        foreach ($this->table()->columnsNames() as $property) {
            if (property_exists($this, $property)) {
                $values[$property] = stringify_property($this, $property);
            }
        }

        $this->id = self::query()->insert($values);

        return $this->exists();
    }

    /**
     * Обновление модели в базе данных
     *
     * @return boolean true если обновлена, false если нет
     */
    public function update(): bool
    {
        $before = self::find($this->id);

        if (!$before->exists()) return false;

        $this->updated_at = new DateTime();

        $update = [];

        foreach ($this->table()->columnsNames() as $property) {
            if (!property_exists($this, $property)) continue;

            $old_value = stringify_property($before, $property);

            $new_value = stringify_property($this, $property);

            if ($old_value === $new_value) continue;

            $update[$property] = $new_value;
        }

        if (!$update) return true;

        $query = self::query()
            ->where('id', $this->id)
            ->limit(1);

        return $query->update($update);
    }

    /**
     * Удаление модели
     *
     * @param string $key Ключ
     * @return boolean true если удалена, false если нет
     */
    public function delete(string $key = 'id'): bool
    {
        if (!property_exists($this, $key)) return false;

        return static::query()
            ->where($key, $this->{$key})
            ->limit(1)
            ->delete();
    }

    /**
     * Поиск модели в базе данных по значению столбца(ов)
     *
     * @param mixed $value Значение столбца(ов)
     * @param string|array $column Столбец(цы)
     * @return self Модель
     */
    public static function find(mixed $value, string|array $column = 'id'): self
    {
        $column = is_array($column) ? $column : [$column];

        $query = self::query()->limit(1);

        foreach ($column as $col) $query->orWhere($col, $value);

        return self::instance($query);
    }

    /**
     * Возвращает последние n моделей из базы данных
     *
     * @param Query|null $query Запрос
     * @param int $amount Количество моделей
     * @param string $order_by Столбец сортировки
     * @return self Модель
     */
    public static function last(?Query $query = null, ?int $amount = 1, ?string $order_by = 'id'): self
    {
        $amount = max(1, $amount);

        $query ??= self::query();

        $query->order($order_by, 'desc')->limit($amount);

        return self::instance($query);
    }

    /**
     * Возвращает первые n моделей из базы данных
     *
     * @param Query|null $query Запрос
     * @param int $amount Количество моделей
     * @param string $order_by Столбец сортировки
     * @return self Модель
     */
    public static function first(?Query $query = null, ?int $amount = 1, ?string $order_by = 'id'): self
    {
        $amount = max(1, $amount);

        $query ??= self::query();

        $query->order($order_by, 'asc')->limit($amount);

        return self::instance($query);
    }
}
