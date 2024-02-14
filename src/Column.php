<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы со столбцом таблицы
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Column
{
    /**
     * Столбец таблицы
     * 
     * @param string $name Имя столбца в БД
     * @param string $description Описание столбца
     * @param string $type Тип данных в столбце
     * @param int $length Длина данных в столбце
     * @param bool $autoincrement Флаг автоинкремента
     * @param bool $signed Флаг поддержки отрицательного значения
     * @param bool $primary Флаг первичного ключа
     * @param bool $index Флаг индекса
     * @param bool $unique Флаг уникального значения
     * @param bool $null Флаг нулевого значения
     * @param ?string $update Значение при обновлении (CASCADE, SET NULL, RESTRICT, CURRENT_TIMESTAMP, etc.)
     * @param mixed $default Значение по умолчанию
     * @param ?Foreign $foreign Список внешних ключей
     */
    public function __construct(
        public string $name,
        public string $description = '',
        public string $type = 'varchar',
        public int $length = 0,
        public bool $autoincrement = false,
        public bool $signed = false,
        public bool $primary = false,
        public bool $index = false,
        public bool $unique = false,
        public bool $null = true,
        public ?string $update = null,
        public mixed $default = null,
        public ?Foreign $foreign = null,
    ) {
        $this->length = abs($this->length);

        $this->length = min($this->length, $this->getMaxLength());

        $this->length = $this->length ?: $this->getMaxLength();
    }

    /**
     * Возвращает максимально допустимую длину данных в столбце
     * 
     * @return int Максимально допустимая длина данных в столбце
     */
    public function getMaxLength(): int
    {
        return  match ($this->type) {
            'tinyint' => 1,
            'smallint' => 5,
            'mediumint' => 8,
            'int' => 10,
            'bigint' => 20,
            'char' => 255,
            'varchar' => 255,
            default => $this->length
        };
    }

    /**
     * Устанавливает внешний ключ
     * 
     * @param string $table Таблица внешнего ключа
     * @param string $column Столбец внешнего ключа
     * @param string $update Действие при обновлении
     * @param string $delete Действие при удалении
     * @return self
     */
    public function foreign(
        string $table,
        string $column,
        string $update = 'RESTRICT',
        string $delete = 'RESTRICT'
    ): self {
        $this->foreign = new Foreign(
            table: $table,
            column: $column,
            update: $update,
            delete: $delete,
        );

        return $this;
    }

    /**
     * Устанавливает значение при обновлении
     *
     * @param mixed $value Значение при обновлении
     * @return self
     */
    public function onUpdate(?string $value): self
    {
        $this->update = $value;

        return $this;
    }

    /**
     * Устанавливает значение по умолчанию
     * 
     * @param mixed $value Значение по умолчанию
     * @return self
     */
    public function default(?string $value): self
    {
        $this->default = $value;

        $this->null = true;

        return $this;
    }

    /**
     * Устанавливает null по умолчанию для столбца
     *
     * @return self
     */
    public function null(): self
    {
        $this->null = true;

        $this->default = null;

        return $this;
    }

    /**
     * Убирает null по умолчанию для столбца
     *
     * @return self
     */
    public function notNull(): self
    {
        $this->null = false;

        return $this;
    }

    /**
     * Устанавливает столбцу поддержку отрицательных значений
     *
     * @return self
     */
    public function unsigned(): self
    {
        $this->signed = false;

        return $this;
    }


    /**
     * Отключает столбцу поддержку отрицательных значений
     *
     * @return self
     */
    public function signed(): self
    {
        $this->signed = true;

        return $this;
    }

    /**
     * Устанавливает столбцу уникальность
     *
     * @return self
     */
    public function unique(): self
    {
        $this->unique = true;

        $this->index = false;

        $this->primary = false;

        return $this;
    }

    /**
     * Включает столбец в индекс
     *
     * @return self
     */
    public function index(): self
    {
        $this->index = true;

        $this->unique = false;

        $this->primary = false;

        return $this;
    }

    /**
     * Устанавливает столбец в качесве первичного ключа
     *
     * @return self
     */
    public function primary(): self
    {
        $this->primary = true;

        $this->unique = false;

        $this->index = false;

        $this->null = false;

        return $this;
    }

    /**
     * Устанавливает столбцу автоинкремент
     *
     * @return self
     */
    public function autoincrement(): self
    {
        $this->autoincrement = true;

        $this->null = true;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Возвращает столбец идентификатора
     * 
     * @return Column Столбец идентификатора
     */
    public static function id(): self
    {
        return new self(
            name: 'id',
            type: 'bigint',
            autoincrement: true,
            primary: true,
            null: false,
            description: 'Идентификатор',
        );
    }

    /**
     * Возвращает столбец даты создания
     * 
     * @return Column Столбец даты создания
     */
    public static function createdTimestamp(): self
    {
        return new self(
            name: 'created_at',
            type: 'datetime',
            default: 'CURRENT_TIMESTAMP',
            description: 'Дата создания',
        );
    }

    /**
     * Возвращает столбец даты обновления
     * 
     * @return Column Столбец даты обновления
     */
    public static function updatedTimestamp(): self
    {
        return new self(
            name: 'updated_at',
            type: 'datetime',
            default: 'CURRENT_TIMESTAMP',
            update: 'CURRENT_TIMESTAMP',
            description: 'Дата обновления',
        );
    }
}
