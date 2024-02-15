<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

abstract class Table
{
    /**
     * Массив внешних ключей
     *
     * @param array
     */
    protected static array $foreigns = [];

    /**
     * Возвращает имя таблицы
     * 
     * @return string Имя таблицы
     */
    abstract public static function name(): string;

    /**
     * Возвращает массив столбцов таблицы
     * 
     * @return array Массив столбцов таблицы
     */
    public function columns(): array
    {
        return [
            new Column(
                name: 'id',
                type: 'bigint',
                autoincrement: true,
                primary: true,
                null: false,
            ),
            new Column(
                name: 'created_at',
                type: 'datetime',
                default: 'CURRENT_TIMESTAMP',
            ),
            new Column(
                name: 'updated_at',
                type: 'datetime',
                default: 'CURRENT_TIMESTAMP',
                update: 'CURRENT_TIMESTAMP',
            )
        ];
    }

    public function columnsNames(): array
    {
        return array_map(
            fn ($column) => $column->name,
            $this->columns()
        );
    }

    /**
     * Возвращает объект запроса
     * 
     * @return Query Объект запроса
     */
    public static function query(): Query
    {
        $query = new Query();

        $query->table(static::name());

        return $query;
    }

    /**
     * Заполняет таблицу данными
     * 
     * @return int Количество добавленных записей
     */
    public function seed(): int
    {
        return 0;
    }

    /**
     * Создание таблицы в базе данных
     *
     * @return boolean true в случае успеха, false в случае ошибки
     */
    public function create(): bool
    {
        $query = $this::query();

        $result = $query->execute($this->getCreateTableSQL());

        if ($result) $result = static::setForeignKeys();

        return $result;
    }

    /**
     * Получение SQL строки создания таблицы
     * 
     * @return string SQL строка
     */
    protected function getCreateTableSQL(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this::name()}` (" . PHP_EOL;

        $primary = null;

        $indexes = [];

        $uniques = [];

        static::$foreigns[$this::name()] ??= [];

        foreach ($this->columns() as $column) {
            assert($column instanceof Column);

            if ($column->primary && !$primary) $primary = $column;

            if ($column->index) $indexes[] = $column;

            if ($column->unique) $uniques[] = $column;

            if ($column->foreign instanceof Foreign) {
                static::$foreigns[$this::name()][$column->name] = $column;
            }

            $sql .= "`{$column}` {$this->getColumnTypeSQL($column)} ";

            $sql .= !$column->null
                ? "NOT NULL"
                : "DEFAULT {$this->getColumnDefaultSQL($column)}";

            if ($column->autoincrement) $sql .= " AUTO_INCREMENT";

            if ($column->update) {
                $sql .= " ON UPDATE {$this->getColumnUpdateSQL($column)}";
            }

            if ($column->description) {
                $sql .= " COMMENT '{$column->description}'";
            }

            $sql .= "," . PHP_EOL;
        }

        if ($primary) $sql .= "PRIMARY KEY (`{$primary}`)";

        if ($uniques) {
            if ($primary) $sql .= "," . PHP_EOL;

            foreach ($uniques as $key) {
                $sql .= "UNIQUE KEY `{$key}` (`{$key}`)," . PHP_EOL;
            }

            $sql = substr($sql, 0, -2);
        }

        if ($indexes) {
            if ($primary || $uniques) $sql .= "," . PHP_EOL;

            foreach ($indexes as $key) {
                $types = ['tinytext', 'text', 'mediumtext', 'longtext'];

                if (in_array($column->type, $types)) {
                    $sql .= "FULLTEXT (`{$key}`)," . PHP_EOL;
                } else {
                    $sql .= "KEY (`{$key}`)," . PHP_EOL;
                }
            }

            $sql = substr($sql, 0, -2);
        }

        $sql .= ") ENGINE=INNODB,"
            . " DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        return $sql;
    }

    /**
     * Получение типа данных столбца в формате SQL
     * 
     * @param Column $column Столбец 
     * @return string Тип данных
     */
    protected function getColumnTypeSQL(Column $column): string
    {
        switch (strtolower($column->type)) {
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'int':
            case 'bigint':
                return $column->type;
            case 'char':
            case 'varchar':
                if ($column->length) {
                    return "{$column->type}({$column->length})";
                }
            default:
                return $column->type;
        }
    }

    /**
     * Получение данных по умолчанию для столбца в формате SQL
     * 
     * @param Column $column Столбец 
     * @return string Данные по умолчанию
     */
    protected function getColumnDefaultSQL(Column $column): string
    {
        return match ($column->default) {
            null => "NULL",
            'CURRENT_TIMESTAMP' => "CURRENT_TIMESTAMP",
            default => "'{$column->default}'"
        };
    }

    /**
     * Получение данных при обновлении столбца в формате SQL
     * 
     * @param Column $column Столбец 
     * @return string Данные при обновлении
     */
    protected function getColumnUpdateSQL(Column $column): string
    {
        return match ($column->update) {
            null => "NULL",
            'CURRENT_TIMESTAMP' => "CURRENT_TIMESTAMP",
            default => "'{$column->update}'"
        };
    }

    /**
     * Установка связей между таблицами
     *
     * @return void
     */
    public static function setForeignKeys(): bool
    {
        $result = true;

        foreach (static::$foreigns as $table => $columns) {
            $foreigns = [];

            foreach ($columns as $name => $column) {
                if (!assert($column instanceof Column)) continue;

                if (!assert($column->foreign instanceof Foreign)) continue;

                $foreigns[] = "ADD FOREIGN KEY ({$column->name}) "
                    . "REFERENCES {$column->foreign->table}({$column->foreign->column}) "
                    . "ON UPDATE {$column->foreign->update} ON DELETE {$column->foreign->delete}";
            }

            $sql = "ALTER TABLE {$table} " . implode(', ', $foreigns) . ";";

            $query = new Query();

            $query->table($table);

            if (!($result = $query->execute($sql))) break;
        }

        return $result;
    }

    /**
     * Удаление таблицы из базы данных
     *
     * @return self
     */
    public function drop(bool $foreignKeyChecks = false): self
    {
        $query = $this::query();

        if (!$foreignKeyChecks) $query->execute("SET FOREIGN_KEY_CHECKS = 0");

        $query->execute("DROP TABLE IF EXISTS `{$this::name()}`;");

        $query->execute("SET FOREIGN_KEY_CHECKS = 1");

        return $this;
    }

    /**
     * Очистка данных таблицы и сброс индексов автоприращения
     * 
     * @return bool
     */
    public function truncate(): bool
    {
        return $this::query()->execute("TRUNCATE TABLE `{$this::name()}`;");
    }
}
