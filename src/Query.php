<?php

namespace Atlantis;

use Exception;
use stdClass;

class Query
{
    public Database $db;
    public string $table = '';
    public array $select = [];
    public array $order = [];
    public int $offset = 0;
    public int $limit = 0;
    public array $where = [];
    public array $orWhere = [];
    public array $values = [];
    public array $group = [];
    public array $insert = [];
    public array $upsert = [];
    public array $update = [];
    public array $innerJoin = [];
    public array $leftJoin = [];
    public array $rightJoin = [];
    public array $fullJoin = [];

    function __construct(Database $db = null)
    {
        if ($db) {
            $this->db = $db;
        } else if (App::$db ?? null) {
            $this->db = App::$db;
        }
    }

    public function init(array $args)
    {
        try {
            foreach ($args as $property => $value) {
                $this->{$property} = $value;
            }
        } catch (Exception $exception) {
            // Exception response
        }

        return $this;
    }

    public function reset()
    {
        $db = $this->db;
        $table = $this->table;

        $className = get_class($this);
        $clean = new $className();

        foreach ($this as $key => $val) {
            if (isset($clean->$key)) {
                $this->$key = $clean->$key;
            } else {
                unset($this->$key);
            }
        }

        $this->db = $db;
        $this->table = $table;

        return $this;
    }

    public function table(string $table)
    {
        $this->table = $table;

        return $this;
    }

    function innerJoin(string $table, string $joinColumn, string $sourceColumn)
    {
        $this->innerJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    function leftJoin(string $table, string $joinColumn, string $sourceColumn)
    {
        $this->leftJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    function rightJoin(string $table, string $joinColumn, string $sourceColumn)
    {
        $this->rightJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    function fullJoin(string $table, string $joinColumn, string $sourceColumn)
    {
        $this->fullJoin[] = (object) [
            'table' => $table,
            'joinColumn' => $joinColumn,
            'sourceColumn' => $sourceColumn
        ];

        return $this;
    }

    function select(...$columns)
    {
        $this->select = $columns;

        return $this;
    }

    function whereIn(string $column, $array)
    {
        if ($array) {
            $prepared = [];

            foreach ($array as $key => $value) {
                $preparedKey = md5($column . $key);
                $prepared[] = ":{$preparedKey}";
                $this->values[$preparedKey] = $value;
            }

            $this->where[] = (object) [
                'column' => $column,
                'prepared' => '(' . implode(',', $prepared) . ')',
                'sign' => 'IN'
            ];
        }

        return $this;
    }

    function whereNotIn(string $column, $array)
    {
        if ($array) {
            $prepared = [];

            foreach ($array as $key => $value) {
                $preparedKey = md5($column . $key);
                $prepared[] = ":{$preparedKey}";
                $this->values[$preparedKey] = $value;
            }

            $this->where[] = (object) [
                'column' => $column,
                'prepared' => '(' . implode(',', $prepared) . ')',
                'sign' => 'NOT IN'
            ];
        }

        return $this;
    }

    function orWhereIn(string $column, $array)
    {
        if ($array) {
            $prepared = [];

            foreach ($array as $key => $value) {
                $preparedKey = md5($column . $key);
                $prepared[] = ":{$preparedKey}";
                $this->values[$preparedKey] = $value;
            }

            $this->orWhere[] = (object) [
                'column' => $column,
                'prepared' => '(' . implode(',', $prepared) . ')',
                'sign' => 'IN'
            ];
        }

        return $this;
    }

    function orWhereNotIn(string $column, $array)
    {
        if ($array) {
            $prepared = [];

            foreach ($array as $key => $value) {
                $preparedKey = md5($column . $key);
                $prepared[] = ":{$preparedKey}";
                $this->values[$preparedKey] = $value;
            }

            $this->orWhere[] = (object) [
                'column' => $column,
                'prepared' => '(' . implode(',', $prepared) . ')',
                'sign' => 'NOT IN'
            ];
        }

        return $this;
    }

    function where(...$args)
    {
        switch (count($args)) {
            case 2:
                $sign = '=';
                list($column, $value) = $args;
                break;
            case 3:
                list($column, $sign, $value) = $args;
                break;
        }

        $prepared = ':' . md5($column);

        $this->where[] = (object) [
            'column' => $column,
            'prepared' => $prepared,
            'sign' => $sign
        ];

        $this->values[$prepared] = $value;

        return $this;
    }

    function orWhere(string $column, string $sign, $value)
    {
        $prepared = ':' . md5($column);

        $this->orWhere[] = (object) [
            'column' => $column,
            'prepared' => $prepared,
            'sign' => $sign
        ];

        $this->values[$prepared] = $value;

        return $this;
    }

    function offset($offset)
    {
        $this->offset = (int) $offset;

        return $this;
    }

    function group(string $column)
    {
        $this->group[] = $column;

        return $this;
    }

    function order(string $column, string $order = 'ASC', bool $reset = false)
    {
        if ($reset) {
            $this->order = [];
        }

        $this->order[$column] = strtoupper($order);
        return $this;
    }

    function limit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    function get(): array
    {
        return $this->db->fetchAll($this->getSelectQuery(), $this->values);
    }

    function first(): false|stdClass
    {
        $this->offset(0);
        $this->limit(1);
        return  $this->db->fetch($this->getSelectQuery(), $this->values);
    }

    function find(int|string $id): stdClass|null
    {
        $query = clone ($this);

        foreach ($query->reset()->where('id', $id)->get() as $obj) {
            return $obj;
        }

        return null;
    }

    function count(): int
    {
        return (int) $this->db->fetchColumn($this->getCountQuery(), $this->values);
    }

    function exists(): bool
    {
        return (bool) $this->count();
    }

    function pluck(string $column): array
    {
        $this->select = [$column];
        $array = [];

        foreach ($this->get() as $key => $row) {
            $array[$key] = $row->{$column};
        }

        return $array;
    }

    function distinct(string $column): array
    {
        $this->select = ["DISTINCT {$column}"];
        $this->order($column, 'ASC', true);

        $array = [];

        foreach ($this->get() as $key => $row) {
            $array[$key] = $row->{$column};
        }

        return $array;
    }

    function delete()
    {
        $result = $this->db->execute($this->getDeleteQuery(), $this->values);
        return $result;
    }

    function insert(array $insert)
    {
        $this->insert = $insert;

        if (!$this->insert) {
            return false;
        }

        $this->db->execute($this->getInsertQuery(), $this->values);

        return $this->db->lastInsertId();
    }

    function upsert(array ...$upsert)
    {

        $this->upsert = $upsert;

        if (!$this->upsert) {
            return false;
        }
        $result = $this->db->execute($this->getUpsertQuery(), $this->values);

        return $result;
    }

    function update(array $update)
    {
        $this->update = $update;

        if (!$this->update) {
            return false;
        }

        $result = $this->db->execute($this->getUpdateQuery(), $this->values);

        return $result;
    }

    protected function getInnerJoinSql(): string
    {
        $sql = "";

        foreach ($this->innerJoin as $join) {
            $sql .= " INNER JOIN {$join->table} ON {$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    protected function getLeftJoinSql(): string
    {
        $sql = "";

        foreach ($this->leftJoin as $join) {
            $sql .= " LEFT JOIN {$join->table} ON {$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    protected function getRightJoinSql(): string
    {
        $sql = "";

        foreach ($this->rightJoin as $join) {
            $sql .= " RIGHT JOIN {$join->table} ON {$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    protected function getFullJoinSql(): string
    {
        $sql = "";

        foreach ($this->innerJoin as $join) {
            $sql .= " FULL OUTER JOIN {$join->table} ON {$join->joinColumn} = {$join->sourceColumn} ";
        }

        return $sql;
    }

    protected function getWhereSql(): string
    {
        $sql = "";

        if ($this->where) {
            $sql .= " WHERE ";

            $array = [];

            foreach ($this->where as $where) {
                $array[] = "{$where->column} {$where->sign} {$where->prepared}";
            }

            $sql .= implode(' && ', $array);
        }

        if ($this->orWhere) {
            $array = [];

            $sql .= $sql ? ' || ( ' : ' WHERE (';


            foreach ($this->orWhere as $where) {
                $array[] = "{$where->column} {$where->sign} {$where->prepared}";
            }

            $sql .= implode(' ) || ( ', $array) . ' ) ';
        }

        return $sql;
    }

    protected function getGroupSql(): string
    {
        $sql = "";

        if ($this->group) {
            $sql .= " GROUP BY " . implode(', ', $this->group);
        }

        return $sql;
    }

    protected function getOrderSql(): string
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

    protected function getLimitSql(): string
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

    protected function getSelectSql(): string
    {
        return $this->select ? implode(', ', $this->select) : " * ";
    }

    protected function getInsertSql(): string
    {
        $sql = '';

        if ($this->insert) {
            $array = [];

            foreach ($this->insert as $column => $value) {
                $prepared = md5("query_insert_{$column}");
                $array[] = "{$column} = :{$prepared}";
                $this->values[$prepared] = $value;
            }

            $sql .= implode(', ', $array);
        }

        return $sql;
    }

    protected function getUpsertSql(): string
    {
        $sql = '';

        if (count($this->upsert) == 2) {
            if ($this->upsert[0] && $this->upsert[1]) {
                $array = [];

                foreach ($this->upsert[0] as $column => $value) {
                    $prepared = md5("query_upsert_{$column}");
                    $array[$column] = ":{$prepared}";
                    $this->values[$prepared] = $value;
                }

                $upsertKey = array_keys($this->upsert[1])[0];
                $upsertPrepared = md5("query_upsert_" . $upsertKey);
                $this->values[$upsertPrepared] = $this->upsert[1][$upsertKey];

                $sql .= " (" . implode(', ', array_keys($array)) . ") VALUES ("
                    . implode(', ', $array) . ") ON DUPLICATE KEY UPDATE "
                    . " {$upsertKey} = :{$upsertPrepared}";
            }
        }

        return $sql;
    }

    protected function getUpdateSql(): string
    {
        $sql = '';

        if ($this->update) {
            $array = [];

            foreach ($this->update as $column => $value) {
                $prepared = md5("query_update_{$column}");
                $array[] = "{$column} = :{$prepared}";
                $this->values[$prepared] = $value;
            }

            $sql .= implode(', ', $array);
        }

        return $sql;
    }

    protected function getCountQuery(): string
    {
        return <<<EOL
        SELECT COUNT(*) FROM {$this->table}
        {$this->getInnerJoinSql()}
        {$this->getLeftJoinSql()}
        {$this->getRightJoinSql()}
        {$this->getFullJoinSql()}
        {$this->getWhereSql()}
        {$this->getGroupSql()}
        {$this->getOrderSql()}
        {$this->getLimitSql()};
        EOL;
    }

    protected function getSelectQuery(): string
    {
        return <<<EOL
        SELECT {$this->getSelectSql()}
        FROM {$this->table}
        {$this->getInnerJoinSql()}
        {$this->getLeftJoinSql()}
        {$this->getRightJoinSql()}
        {$this->getFullJoinSql()}
        {$this->getWhereSql()}
        {$this->getGroupSql()}
        {$this->getOrderSql()}
        {$this->getLimitSql()};
        EOL;
    }

    protected function getDeleteQuery(): string
    {
        return <<<EOL
        DELETE FROM {$this->table}
        {$this->getWhereSql()}
        {$this->getLimitSql()};
        EOL;
    }

    protected function getInsertQuery(): string
    {
        return <<<EOL
        INSERT INTO {$this->table} SET {$this->getInsertSql()};
        EOL;
    }

    protected function getUpsertQuery(): string
    {
        return <<<EOL
        INSERT INTO {$this->table}
        {$this->getUpsertSql()}
        {$this->getWhereSql()}
        {$this->getLimitSql()};
        EOL;
    }

    protected function getUpdateQuery(): string
    {
        return <<<EOL
        UPDATE {$this->table} SET {$this->getUpdateSql()}
        {$this->getWhereSql()}
        {$this->getLimitSql()};
        EOL;
    }
}
