<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\{Migration};

abstract class Model extends Facade
{
    abstract public static function table(): Migration;

    abstract public static function query(): Query;

    abstract public function exists(): bool;

    public function __construct(
        public int $id = 0,
        public ?DateTime $created_at = new DateTime(),
    ) {
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
        return false;
    }

    /**
     * Обновление модели в базе данных
     *
     * @return boolean true если обновлена, false если нет
     */
    public function update(): bool
    {
        return false;
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
        $query = static::query();

        $query->limit(1);

        $column = is_array($column) ? $column : [$column];

        foreach ($column as $col) $query->orWhere($col, $value);

        return self::make()->__fill($query->first());
    }
}
