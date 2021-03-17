<?php

namespace Atlantis;

use Closure;

class Blueprint
{
    public string $table;
    public array $columns = [];

    public function __construct($table, Closure $callback = null)
    {
        $this->table = $table;

        if (!is_null($callback)) {
            $callback($this);
        }
    }

    public function incrementPrimary(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'bigint',
            increment: true,
            primary: true,
            null: false
        );

        return $this->columns[$column];
    }

    public function increment(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'bigint',
            increment: true,
            null: true
        );

        return $this->columns[$column];
    }

    public function string(string $column, int $length = 255)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'varchar',
            length: $length
        );

        return $this->columns[$column];
    }

    public function tinyint(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'tinyint'
        );

        return $this->columns[$column];
    }

    public function int(string $column, int $length = 12)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'int',
            length: $length
        );

        return $this->columns[$column];
    }

    public function bigint(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'bigint'
        );

        return $this->columns[$column];
    }

    public function float(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'float'
        );

        return $this->columns[$column];
    }

    public function text(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'text'
        );

        return $this->columns[$column];
    }

    public function mediumText(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'mediumtext'
        );

        return $this->columns[$column];
    }

    public function longText(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'longtext'
        );

        return $this->columns[$column];
    }

    public function datetime(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'datetime'
        );

        return $this->columns[$column];
    }

    public function time(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'time'
        );

        return $this->columns[$column];
    }

    public function date(string $column)
    {
        $this->columns[$column] = new Column(
            column: $column,
            type: 'date'
        );

        return $this->columns[$column];
    }
}
