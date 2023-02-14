<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use DateTime;

use Exception;

use Tischmann\Atlantis\{Migration};

abstract class Model extends Facade
{
    /**
     * Таблица модели
     *
     * @param Migration
     */
    abstract public static function table(): Migration;

    public function __construct(
        public int $id = 0,
        public ?DateTime $created_at = null,
        public ?DateTime $updated_at = null,
    ) {
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
        if ($this->exists()) {
            throw new Exception('Model already exists');
        }

        $insert = [];

        $this->created_at = new DateTime();

        $columns = [];

        foreach ($this->table()->columns() as $column) {
            $columns[$column->name] = $column;
        }

        foreach ($this as $property => $value) {
            if (!array_key_exists($property, $columns)) continue;

            $insert[$property] = $this->__stringify($property);
        }

        if ($insert) {
            $this->id = self::query()->insert($insert);
        } else {
            throw new Exception('Model is empty');
        }

        return $this->exists();
    }

    /**
     * Обновление модели в базе данных
     *
     * @return boolean true если обновлена, false если нет
     */
    public function update(): bool
    {
        $current = self::find($this->id);

        if (!$current->exists()) {
            throw new Exception('Model not found');
        }

        $this->updated_at = new DateTime();

        $update = [];

        $columns = [];

        foreach ($this->table()->columns() as $column) {
            $columns[$column->name] = $column;
        }

        foreach ($this as $property => $value) {
            if ($property === 'id') continue;

            if ($current->{$property} === $this->{$property}) continue;

            if (!array_key_exists($property, $columns)) continue;

            $update[$property] = $this->__stringify($property);
        }

        if ($update) {
            $query = self::query();

            $query->where('id', $this->id)->limit(1);

            return $query->update($update);
        }

        return true;
    }

    /**
     * Удаление модели
     *
     * @param string $key Ключ
     * @return boolean true если удалена, false если нет
     */
    public function delete(string $key = 'id'): bool
    {
        return static::query()->where($key, $this->{$key})->limit(1)->delete();
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
        $query = self::query();

        $query->limit(1);

        $column = is_array($column) ? $column : [$column];

        foreach ($column as $col) $query->orWhere($col, $value);

        return self::make()->__fill($query->first());
    }

    /**
     * Возвращает массив моделей из запроса
     *
     * @param Query $query Запрос
     * @param string $key Ключ
     * @return array Массив моделей
     */
    public static function fill(Query $query, string $key = 'id'): array
    {
        $array = [];

        foreach ($query->get() as $row) {
            $model = new static();

            $model->__fill($row);

            $array[$model->{$key}] = $model;
        }

        return $array;
    }
}
