<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use ReflectionClass;

/**
 * Класс SQL запроса
 *
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Query
{
    private string $table = ''; // Имя таблицы
    private int $uniqid = 0; // Уникальный идентификатор
    private array $select = []; // Список полей для выборки
    private array $order = []; // Список полей для сортировки
    private int $offset = 0; // Смещение выборки
    private int $limit = 0; // Количество записей в выборке
    private array $whereAnd = []; // Список полей для условий AND
    private array $whereOr = []; // Список полей для условий OR
    private array $values = []; // Список значений для вставки в запрос
    private array $group = []; // Список полей для группировки
    private array $insert = []; // Список полей для вставки
    private array $upsert = []; // Список полей для вставки или обновления
    private array $update = []; // Список полей для обновления
    private array $innerJoin = []; // Список полей для внешнего объединения
    private array $leftJoin = []; // Список полей для левого объединения
    private array $rightJoin = []; // Список полей для правого объединения
    private array $fullJoin = []; // Список полей для полного объединения

    /**
     * Конструктоор
     * 
     * @param ?Database $database Объект базы данных
     */
    public function __construct(private ?Database $database = null)
    {
        $this->database ??= Database::connect();
    }

    public function __clone()
    {
        foreach ($this as $property => $value) {
            if (is_object($value)) {
                $reflectionClass = new ReflectionClass($value);

                if ($reflectionClass->isCloneable()) {
                    $this->{$property} = clone $value;
                }
            }
        }
    }

    /**
     * Журналирование
     * 
     * @param bool $status true - включить, false - выключить
     * @return self
     */
    public function log(bool $status = true): self
    {
        $this->database->execute(
            "SET global general_log = ?;",
            [intval($status)]
        );

        return $this;
    }

    /**
     * Сброс значений по умолчанию, кроме имени таблицы
     */
    public function reset(): self
    {
        $this->uniqid = 0;
        $this->select = [];
        $this->order = [];
        $this->offset = 0;
        $this->limit = 0;
        $this->whereAnd = [];
        $this->whereOr = [];
        $this->values = [];
        $this->group = [];
        $this->insert = [];
        $this->upsert = [];
        $this->update = [];
        $this->innerJoin = [];
        $this->leftJoin = [];
        $this->rightJoin = [];
        $this->fullJoin = [];
        return $this;
    }

    /**
     * Установка таблицы для выборки
     * 
     * @param string $table Таблица для выборки
     * @return self
     */
    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Выполняет запрос к базе данных
     * 
     * @param string $statement Запрос к базе данных
     * @param array $values Список значений для вставки в запрос
     * @return bool true - выполнено успешно, false - выполнено неуспешно     * 
     */
    public function execute(string $statement, array $values = []): bool
    {
        return $this->database->execute($statement, $values);
    }

    /**
     * Добавляет внутреннее объединение
     * 
     * @param string $table Таблица для внутреннего объединения
     * @param string $joinColumn Столбец для внутреннего объединения
     * @param string $sourceColumn Столбец для внешнего объединения
     * @return self
     */
    public function innerJoin(
        string $table,
        string $joinColumn,
        string $sourceColumn
    ): self {
        $this->innerJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    /**
     * Добавляет левое объединение
     * 
     * @param string $table Таблица для левого объединения
     * @param string $joinColumn Столбец для левого объединения
     * @param string $sourceColumn Столбец для внешнего объединения
     * @return self
     */
    public function leftJoin(
        string $table,
        string $joinColumn,
        string $sourceColumn
    ): self {
        $this->leftJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    /**
     * Добавляет правое объединение
     * 
     * @param string $table Таблица для правого объединения
     * @param string $joinColumn Столбец для правого объединения
     * @param string $sourceColumn Столбец для внешнего объединения
     * @return self
     */
    public function rightJoin(
        string $table,
        string $joinColumn,
        string $sourceColumn
    ): self {
        $this->rightJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    /**
     * Добавляет полное объединение
     * 
     * @param string $table Таблица для полного объединения
     * @param string $joinColumn Столбец для полного объединения
     * @param string $sourceColumn Столбец для внешнего объединения
     * @return self
     */
    public function fullJoin(
        string $table,
        string $joinColumn,
        string $sourceColumn
    ): self {
        $this->fullJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    /**
     * Устанавливает поля для выборки
     * 
     * @param mixed $columns Поля для выборки
     */
    public function select(...$columns): self
    {
        $this->select = $columns;

        return $this;
    }

    /**
     * Связывает переменные со значениями и возвращает массив связанных переменных
     * 
     * @param array $values Значения
     * @return array Массив связанных переменных
     */
    protected function getPrepareValues(array $values): array
    {
        $prepared = [];

        foreach ($values as $value) {
            $prepared[] = $this->getPrepareValue(strval($value));
        }

        return $prepared;
    }

    /**
     * Связывает переменную со значением и возвращает связанную переменную
     * 
     * @param string $value Значение
     * @return string Связанная переменная
     */
    protected function getPrepareValue(string $value): string
    {
        $preparedStatement = $this->getPreparedStatement();

        $this->values[$preparedStatement] = $value;

        return ":{$preparedStatement}";
    }

    /**
     * Генерирует имя связанной переменную
     * 
     * @return string Имя связанной переменной
     */
    private function getPreparedStatement(): string
    {
        $id = "ps{$this->uniqid}";

        $this->uniqid++;

        return $id;
    }

    /**
     * Добавляет AND условие для выборки
     * 
     * @param mixed $args Аргументы условия:
     * 
     * function(Query &$nested){} - вложенный запрос
     * 
     * $column, $value - условие вида `column` = 'value'
     * 
     * $column, $sign, $value - условие вида `column` $sign `value`
     * 
     * $column, $sign, $min, $max - условие вида `column` BETWEEN 'min' AND 'max'
     *
     * @return self
     */
    public function where(...$args): self
    {
        switch (count($args)) {
            case 1:
                if (is_callable($args[0])) {
                    list($closure) = $args;

                    $nested = clone $this;

                    $nested->reset();

                    $nested->uniqid = $this->uniqid;

                    $closure($nested);

                    $this->whereAnd = array_merge(
                        $this->whereAnd,
                        $nested->whereAnd,
                    );

                    if ($nested->whereOr) {
                        $this->whereAnd = array_merge(
                            $this->whereAnd,
                            ["((" . implode(") || (", $nested->whereOr) . "))"]
                        );
                    }

                    $this->values = array_merge($this->values, $nested->values);

                    $this->uniqid = $nested->uniqid;

                    return $this;
                } else {
                    $value = null;
                    $sign = $value === null ? 'IS NOT' : '!=';
                    list($column) = $args;
                }
                break;
            case 2:
                list($column, $value) = $args;
                $sign = $value === null ? 'IS' : '=';
                break;
            case 3:
                list($column, $sign, $value) = $args;

                if (in_array($sign, ['![]', '[]'])) {
                    $this->whereAnd[] = $this->getJsonContainsStatement(
                        $column,
                        (string) $value,
                        $sign === '![]'
                    );

                    return $this;
                } elseif (in_array($sign, ['!()', '()'])) {
                    if ($value) {
                        $prepared = $this->getPrepareValues($value);

                        $not = $sign === '!()' ? "NOT " : '';

                        $this->whereAnd[] = "`{$column}` {$not}IN ("
                            . implode(',', $prepared) . ")";
                    }

                    return $this;
                }
                break;
            case 4:
                list($column, $sign, $min, $max) = $args;

                if (!in_array(strtoupper($sign), ['BETWEEN', '<=>'])) {
                    return $this;
                }

                $min = $this->getPrepareValue((string) $min);

                $max = $this->getPrepareValue((string) $max);

                $this->whereAnd[] = "`{$column}` BETWEEN {$min} AND {$max}";

                return $this;
            default:
                return $this;
        }

        $prepared = $value !== null
            ? $this->getPrepareValue((string) $value)
            : 'NULL';

        $this->whereAnd[] = "`{$column}` {$sign} {$prepared}";

        return $this;
    }

    /**
     * Возвращает условие типа 'value' MEMBER OF(`column`)
     * 
     * @param string $column Имя столбца
     * @param string $value Значение
     * @param bool $not Отрицательное условие (не содержит)
     * @return string Условие
     */
    public function getJsonContainsStatement(
        string $column,
        string $value,
        bool $not = false
    ): string {
        $prepared = $value !== null ? $this->getPrepareValue($value) : 'null';

        return ($not ? 'NOT' : '') . " {$prepared} MEMBER OF(`{$column}`)";
    }

    /**
     * Добавляет AND условие для выборки в виде SQL-запроса
     * 
     * @param string $statement Условие 
     * @return self
     */
    public function whereRaw(string $statement): self
    {
        $this->whereAnd[] = $statement;
        return $this;
    }

    /**
     * Добавляет OR условие для выборки в виде SQL-запроса
     * 
     * @param string $statement Условие 
     * @return self
     */
    public function orWhereRaw(string $statement): self
    {
        $this->whereOr[] = $statement;
        return $this;
    }

    /**
     * Добавляет OR условие для выборки
     * 
     * @param mixed $args Аргументы условия:
     * 
     * function(Query &$nested){} - вложенный запрос
     * 
     * $column, $value - условие вида `column` = 'value'
     * 
     * $column, $sign, $value - условие вида `column` $sign `value`
     * 
     * $column, $sign, $min, $max - условие вида `column` BETWEEN 'min' AND 'max'
     * 
     * @return self
     */
    public function orWhere(...$args): self
    {
        switch (count($args)) {
            case 1:
                if (is_callable($args[0])) {
                    list($closure) = $args;

                    $nested = clone $this;
                    $nested->reset();
                    $nested->uniqid = $this->uniqid;

                    $closure($nested);

                    $this->whereOr = array_merge(
                        $this->whereOr,
                        $nested->whereOr
                    );

                    if ($nested->whereAnd) {
                        $this->whereOr = array_merge(
                            $this->whereOr,
                            ["(" . implode(" && ", $nested->whereAnd) . ")"],
                        );
                    }

                    $this->values = array_merge($this->values, $nested->values);
                    $this->uniqid = $nested->uniqid;

                    return $this;
                } else {
                    $value = null;
                    $sign = $value === null ? 'IS NOT' : '!=';
                    list($column) = $args;
                }
                break;
            case 2:
                list($column, $value) = $args;
                $sign = $value === null ? 'IS' : '=';
                break;
            case 3:
                list($column, $sign, $value) = $args;

                if (in_array($sign, ['![]', '[]'])) {
                    $this->whereOr[] = $this->getJsonContainsStatement(
                        $column,
                        (string) $value,
                        $sign === '![]'
                    );

                    return $this;
                } elseif (in_array($sign, ['!()', '()'])) {
                    if ($value) {
                        $prepared = $this->getPrepareValues($value);

                        $not = $sign === '!()' ? "NOT " : '';

                        $this->whereOr[] = "`{$column}` {$not}IN ("
                            . implode(',', $prepared) . ")";
                    }

                    return $this;
                }
                break;
            case 4:
                list($column, $sign, $min, $max) = $args;

                if (!in_array(strtoupper($sign), ['BETWEEN', '<=>'])) {
                    return $this;
                }

                $min = $this->getPrepareValue((string) $min);
                $max = $this->getPrepareValue((string) $max);

                $this->whereOr[] = "`{$column}` BETWEEN {$min} AND {$max}";

                return $this;
            default:
                return $this;
        }

        $prepared = $value !== null
            ? $this->getPrepareValue((string) $value)
            : 'NULL';

        $this->whereOr[] = "`{$column}` {$sign} {$prepared}";

        return $this;
    }

    /**
     * Устанавливает смещение для выборки
     * 
     * @param int $offset Смещение
     * @return self
     */
    public function offset(int $offset = 0): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Добавляет группировку для выборки
     * 
     * @param string $column Колонка для группировки
     * @param bool $reset Сброс группировки перед добавлением
     * @return self
     */
    public function group(string $column = '', bool $reset = false): self
    {
        if ($reset) $this->group = [];

        if ($column) $this->group[] = $column;

        return $this;
    }

    /**
     * Добавляет сортировку для выборки
     * 
     * @param string $column Колонка для сортировки
     * @param string $direction Направление сортировки
     * @param bool $reset Сброс сортировки перед добавлением
     * @return self
     */
    public function order(
        string $column,
        string $order = 'ASC',
        bool $reset = false
    ): self {
        if ($reset) $this->order = [];
        $this->order[$column] = strtoupper($order);
        return $this;
    }

    /**
     * Устанавливает лимит для выборки
     * 
     * @param int $limit Лимит
     * @return self
     */
    public function limit(int $limit = 0): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Возвращает результат выборки
     * 
     * @return array Результат выборки
     */
    public function get(): array
    {
        $query = clone $this;

        return $query->database->fetchAll($query->getSelectQuery(), $query->values);
    }

    /**
     * Возвращает первую строку из результата выборки
     * 
     * @return object Первая строка из результата выборки
     */
    public function first(): object
    {
        $query = clone $this;

        $query->offset(0)->limit(1);

        $rows = $query->database->fetchAll($query->getSelectQuery(), $query->values);

        return $rows[0] ?? (object) [];
    }

    /**
     * Ищет значение столбца в таблице и возвращает строку с найденными данными
     * 
     * @param string $value Значение для поиска
     * @param string $column Колонка для поиска значения в таблице БД (по умолчанию "id")
     * @return object|array Строка с найденными данными
     */
    public function find(mixed $value, string $column = 'id'): object|array
    {
        $query = clone $this;

        $array = $query->reset()->where($column, $value)->get();

        return array_shift($array) ?? [];
    }

    /**
     * Возвращает количество строк в результате выборки
     * 
     * @return int Количество строк в результате выборки
     */
    public function count(?string $column = null): int
    {
        $query = clone $this;

        if ($column !== null) {
            return $this->countDistinct($column);
        }

        return intval($query->database->fetchColumn(
            $query->getCountQuery(),
            $query->values
        ));
    }

    /**
     * Возвращает количество уникальных значений в столбце
     * 
     * @param string $column Колонка для подсчета уникальных значений     * 
     * @return int Количество уникальных значений
     */
    public function countDistinct(string $column): int
    {
        $query = clone $this;

        return intval($query->database->fetchColumn(
            $query->getCountDistinctQuery($column),
            $query->values
        ));
    }

    /**
     * Проверяет, существует ли строка в результате выборки
     * 
     * @return bool true если строка существует, иначе false
     */
    public function exist(): bool
    {
        $query = clone $this;
        return (bool) $query->limit(1)->offset(0)->count();
    }

    /**
     * Возвращает массив значений столбца в результате выборки
     * 
     * @param string $column Колонка для выборки значений
     * @return array Массив значений столбца
     */
    public function pluck(string $column): array
    {
        $query = clone $this;
        $query->select = [$column];

        $array = [];

        foreach ($query->get() as $key => $obj) {
            $array[$key] = $obj->{$column};
        }

        return $array;
    }

    /**
     * Возвращает уникальные значения столбца в результате выборки
     * 
     * @param string $column Колонка для выборки значений
     * @return array Массив уникальных значений столбца
     */
    public function distinct(string $column): array
    {
        $query = clone $this;
        $query->select = [$column];
        $query->order($column, 'ASC', true)
            ->group($column, true);

        $array = [];

        foreach ($query->get() as $obj) {
            $array[] = $obj->{$column};
        }

        return $array;
    }

    /**
     * Выполняет запрос к базе данных на удаление данных
     * 
     * @return bool true если запрос выполнен успешно, иначе false
     */
    public function delete(): bool
    {
        $result = $this->database->execute($this->getDeleteQuery(), $this->values);

        $this->reset();

        return $result;
    }

    /**
     * Выполняет запрос к базе данных на очистку таблицы (TRUNCATE)
     * 
     * @return bool true если запрос выполнен успешно, иначе false
     */
    public function truncate(): bool
    {
        $result = $this->database->execute($this->getTruncateQuery());

        $this->reset();

        return $result;
    }

    /**
     * Выполняет запрос к базе данных на добавление данных
     * 
     * @param array $insert Данные для добавления 
     * @return int Идентификатор добавленной строки
     */
    public function insert(array $insert): int
    {
        $this->insert = $insert;

        if (!$this->insert) return 0;

        $this->database->execute($this->getInsertQuery(), $this->values);

        $this->reset();

        return (int) $this->database->lastInsertId();
    }

    /**
     * Выполняет запрос к базе данных на добавление или обновление данных
     * 
     * @param array $update Данные для обновления
     * @return bool true если запрос выполнен успешно, иначе false
     */
    public function upsert(...$upsert): bool
    {
        $this->upsert = $upsert;

        if (!$this->upsert) return false;

        $result = $this->database->execute($this->getUpsertQuery(), $this->values);

        $this->reset();

        return $result;
    }

    /**
     * Выполняет запрос к базе данных на обновление данных
     * 
     * @param array $update Данные для обновления
     * @return bool true если запрос выполнен успешно, иначе false
     */
    public function update(array $update): bool
    {
        $this->update = $update;

        if (!$this->update) return true;

        $sql = $this->getUpdateQuery();

        $result = $this->database->execute($sql, $this->values);

        $this->reset();

        return $result;
    }

    /**
     * Возвращает код SQL-запроса на внутреннее объединение данных
     * 
     * @return string Код SQL-запроса
     */
    private function getInnerJoinSQL(): string
    {
        $sql = "";

        foreach ($this->innerJoin as $join) {
            $sql .= " INNER JOIN {$join->table} ON "
                . "{$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на левое объединение данных
     * 
     * @return string Код SQL-запроса
     */
    private function getLeftJoinSQL(): string
    {
        $sql = "";

        foreach ($this->leftJoin as $join) {
            $sql .= " LEFT JOIN {$join->table} ON "
                . "{$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на правое объединение данных
     * 
     * @return string Код SQL-запроса
     */
    private function getRightJoinSQL(): string
    {
        $sql = "";

        foreach ($this->rightJoin as $join) {
            $sql .= " RIGHT JOIN {$join->table} ON "
                . "{$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на полное объединение данных
     * 
     * @return string Код SQL-запроса
     */
    private function getFullJoinSQL(): string
    {
        $sql = "";

        foreach ($this->innerJoin as $join) {
            $sql .= " FULL OUTER JOIN {$join->table} ON "
                . "{$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на условие
     * 
     * @return string Код SQL-запроса
     */
    private function getWhereSQL(): string
    {
        $sql = "";

        if ($this->whereAnd || $this->whereOr) {
            $sql = " WHERE ";

            $where = [];

            if ($this->whereOr) {
                $where[] = "(( " . implode(" ) || ( ", $this->whereOr) . " )) ";
            }

            if ($this->whereAnd) {
                $where = array_merge($this->whereAnd, $where);
            }

            if ($where) {
                $sql .= implode(' && ', $where);
            }
        }

        return $sql ?: " WHERE 1 ";
    }

    /**
     * Возвращает код SQL-запроса на группировку
     * 
     * @return string Код SQL-запроса
     */
    private function getGroupSQL(): string
    {
        $sql = "";

        if ($this->group) {
            $sql .= " GROUP BY " . implode(', ', $this->group);
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на сортировку
     * 
     * @return string Код SQL-запроса
     */
    private function getOrderSQL(): string
    {
        $sql = '';

        if ($this->order) {
            $sql .= " ORDER BY ";
            $array = [];

            foreach ($this->order as $column => $direction) {
                $array[] = "{$column} {$direction}";
            }

            $sql .= implode(', ', $array);
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на ограничение количества записей
     * 
     * @return string Код SQL-запроса
     */
    private function getLimitSQL(): string
    {
        $sql = '';

        if ($this->limit) {
            $sql .= " LIMIT {$this->limit} ";

            if ($this->offset) {
                $sql .= " OFFSET {$this->offset} ";
            }
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса с полями для выборки
     * 
     * @return string Код SQL-запроса
     */
    private function getSelectSQL(): string
    {
        return $this->select ? implode(', ', $this->select) : " * ";
    }

    /**
     * Возвращает код SQL-запроса с полями для вставки
     * 
     * @return string Код SQL-запроса
     */
    private function getInsertSQL(): string
    {
        $sql = '';

        if ($this->insert) {
            $array = [];

            foreach ($this->insert as $column => $value) {
                if ($value === null) {
                    $array[] = "`{$column}` = NULL";
                } else {
                    $prepared = $this->getPreparedStatement();
                    $array[] = "`{$column}` = :{$prepared}";
                    $this->values[$prepared] = strval($value);
                }
            }

            $sql .= implode(', ', $array);
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса с полями для обновления или вставки
     * 
     * @return string Код SQL-запроса
     */
    private function getUpsertSQL(): string
    {
        $sql = '';

        if (count($this->upsert) == 2) {
            if ($this->upsert[0] && $this->upsert[1]) {
                $array = [];

                foreach ($this->upsert[0] as $column => $value) {
                    if ($value === null) {
                        $array[$column] = 'NULL';
                    } else {
                        $prepared = $this->getPreparedStatement();
                        $array[$column] = ":{$prepared}";
                        $this->values[$prepared] = (string) $value;
                    }
                }

                $upsertKey = array_keys($this->upsert[1])[0];

                $upsertPrepared = $this->getPreparedStatement();

                $this->values[$upsertPrepared] = strval(
                    $this->upsert[1][$upsertKey]
                );

                $sql .= " (" . implode(', ', array_keys($array)) . ") VALUES ("
                    . implode(', ', $array) . ") ON DUPLICATE KEY UPDATE "
                    . " {$upsertKey} = :{$upsertPrepared}";
            }
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса с полями для обновления
     * 
     * @return string Код SQL-запроса
     */
    private function getUpdateSQL(): string
    {
        $sql = '';

        if ($this->update) {
            $array = [];

            foreach ($this->update as $column => $value) {
                if ($value === null) {
                    $array[] = "{$column} = NULL";
                } else {
                    $prepared = $this->getPreparedStatement();
                    $array[] = "{$column} = :{$prepared}";
                    $this->values[$prepared] = (string) $value;
                }
            }

            $sql .= implode(', ', $array);
        }

        return $sql;
    }

    /**
     * Возвращает код SQL-запроса на подсчёт количества записей
     * 
     * @return string Код SQL-запроса
     */
    private function getCountQuery(): string
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` ";

        $innerJoin = trim($this->getInnerJoinSQL());
        $leftJoin = trim($this->getLeftJoinSQL());
        $rightJoin = trim($this->getRightJoinSQL());
        $fullJoin = trim($this->getFullJoinSQL());
        $where = trim($this->getWhereSQL());
        $limit = trim($this->getLimitSQL());

        $sql .= $innerJoin ? "{$innerJoin} " : "";
        $sql .= $leftJoin ? "{$leftJoin} " : "";
        $sql .= $rightJoin ? "{$rightJoin} " : "";
        $sql .= $fullJoin ? "{$fullJoin} " : "";
        $sql .= $where ? "{$where} " : "";
        $sql .= $limit ? $limit : "";

        return "{$sql};";
    }

    /**
     * Возвращает код SQL-запроса на подсчёт количества уникальных записей
     * 
     * @return string Код SQL-запроса
     */
    private function getCountDistinctQuery(string $column): string
    {
        $sql = "SELECT COUNT(DISTINCT(`{$column}`)) FROM `{$this->table}` ";

        $innerJoin = trim($this->getInnerJoinSQL());
        $leftJoin = trim($this->getLeftJoinSQL());
        $rightJoin = trim($this->getRightJoinSQL());
        $fullJoin = trim($this->getFullJoinSQL());
        $where = trim($this->getWhereSQL());
        $limit = trim($this->getLimitSQL());

        $sql .= $innerJoin ? "{$innerJoin} " : "";
        $sql .= $leftJoin ? "{$leftJoin} " : "";
        $sql .= $rightJoin ? "{$rightJoin} " : "";
        $sql .= $fullJoin ? "{$fullJoin} " : "";
        $sql .= $where ? "{$where} " : "";
        $sql .= $limit ? $limit : "";

        return "{$sql};";
    }

    /**
     * Возвращает код SQL-запроса на получение записей
     * 
     * @return string Код SQL-запроса
     */
    private function getSelectQuery(): string
    {
        $sql = "SELECT " . trim($this->getSelectSQL())
            . " FROM `{$this->table}` ";

        $innerJoin = trim($this->getInnerJoinSQL());
        $leftJoin = trim($this->getLeftJoinSQL());
        $rightJoin = trim($this->getRightJoinSQL());
        $fullJoin = trim($this->getFullJoinSQL());
        $where = trim($this->getWhereSQL());
        $group = trim($this->getGroupSQL());
        $order = trim($this->getOrderSQL());
        $limit = trim($this->getLimitSQL());

        $sql .= $innerJoin ? "{$innerJoin} " : "";
        $sql .= $leftJoin ? "{$leftJoin} " : "";
        $sql .= $rightJoin ? "{$rightJoin} " : "";
        $sql .= $fullJoin ? "{$fullJoin} " : "";
        $sql .= $where ? "{$where} " : "";
        $sql .= $group ? "{$group} " : "";
        $sql .= $order ? "{$order} " : "";
        $sql .= $limit ? $limit : "";

        return "{$sql};";
    }

    /**
     * Возвращает код SQL-запроса на удаление записей
     * 
     * @return string Код SQL-запроса
     */
    private function getDeleteQuery(): string
    {
        $sql = "DELETE FROM `{$this->table}` ";

        $where = trim($this->getWhereSQL());

        $limit = trim($this->getLimitSQL());

        $sql .= $where ? "{$where} " : "";

        $sql .= $limit ? $limit : "";

        return "{$sql};";
    }

    /**
     * Возвращает код SQL-запроса на очистку таблицы
     * 
     * @return string Код SQL-запроса
     */
    private function getTruncateQuery(): string
    {
        return "TRUNCATE TABLE `{$this->table}`;";
    }

    /**
     * Возвращает код SQL-запроса на вставку записей
     * 
     * @return string Код SQL-запроса
     */
    private function getInsertQuery(): string
    {
        return "INSERT INTO `{$this->table}` SET "
            . trim($this->getInsertSQL()) . ";";
    }

    /**
     * Возвращает код SQL-запроса на обновление или вставку записей
     * 
     * @return string Код SQL-запроса
     */
    private function getUpsertQuery(): string
    {
        $sql = "INSERT INTO `{$this->table}` " . trim($this->getUpsertSQL());

        $where = trim($this->getWhereSQL());
        $limit = trim($this->getLimitSQL());

        $sql .= $where ? " {$where}" : "";
        $sql .= $limit ? " {$limit}" : "";

        return "{$sql};";
    }

    /**
     * Возвращает код SQL-запроса на обновление записей
     * 
     * @return string Код SQL-запроса
     */
    private function getUpdateQuery(): string
    {
        $sql = "UPDATE `{$this->table}` SET " . trim($this->getUpdateSQL());

        $where = trim($this->getWhereSQL());
        $limit = trim($this->getLimitSQL());

        $sql .= $where ? " {$where}" : "";
        $sql .= $limit ? " {$limit}" : "";

        return "{$sql};";
    }
}
