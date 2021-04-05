<?php

namespace Atlantis;

use stdClass;

class Column
{
    public string $column;
    public $value = null;
    public string $name = '';
    public string $title = '';
    public int $width = 60;
    public string $sort = '';
    public string $dir = '';
    public bool $search = true;
    public bool $filter = true;
    public bool $resize = true;
    public bool $edit = false;
    public string $regex = '';
    public string $type = 'varchar';
    public int $length = 255;
    public bool $increment = false;
    public bool $signed = false;
    public bool $primary = false;
    public bool $index = false;
    public bool $unique = false;
    public bool $null = false;
    public int|string|float|null $default = null;
    public int|string|float|null $update = null;
    public array $foreign = [];
    public string $comment = '';
    public string $mask = '';

    public function __construct(
        string $column,
        $value = null,
        string $name = '',
        string $title = '',
        int $width = 60,
        string $sort = '',
        string $dir = '',
        bool $edit = false,
        bool $search = true,
        bool $filter = true,
        bool $resize = true,
        string $regex = '',
        string $type = 'varchar',
        int $length = 255,
        bool $increment = false,
        bool $signed = false,
        bool $primary = false,
        bool $index = false,
        bool $unique = false,
        bool $null = false,
        int|string|float|null $default = null,
        int|string|float|null $update = null,
        array $foreign = [],
        string $comment = '',
        string $mask = '',
    ) {
        $this->column = $column;
        $this->value = $value;
        $this->name = $name;
        $this->title = $title;
        $this->width = $width;
        $this->sort = $sort;
        $this->dir = $dir;
        $this->search = $search;
        $this->filter = $filter;
        $this->edit = $edit;
        $this->resize = $resize;
        $this->regex = $regex;
        $this->type = $type;
        $this->length = $length;
        $this->increment = $increment;
        $this->signed = $signed;
        $this->primary = $primary;
        $this->index = $index;
        $this->unique = $unique;
        $this->null = $null;
        $this->default = $default;
        $this->update = $update;
        $this->foreign = $foreign;
        $this->comment = $comment;
        $this->mask = $mask;

        if ($this->type == 'int' && $length == 255) {
            $this->length = 12;
        }
    }

    public function volatable(): array
    {
        return [
            'column' => $this->column,
            'name' => $this->name,
            'title' => $this->title,
            'width' => $this->width,
            'sort' => $this->sort,
            'dir' => $this->dir,
            'edit' => $this->edit ? 1 : 0,
            'resize' => $this->resize ? 1 : 0,
            'regex' => $this->regex,
            'search' => $this->search ? 1 : 0,
            'filter' => $this->filter ? 1 : 0,
            'mask' => $this->mask,
        ];
    }

    public function typeAndLength(): string
    {
        switch ($this->type) {
            case 'int':
            case 'varchar':
                return "{$this->type}({$this->length})";
            default:
                return $this->type;
        }
    }

    public function getDefault(): string
    {
        if ($this->default === null) {
            return "NULL";
        } else if ($this->default == 'CURRENT_TIMESTAMP') {
            return "CURRENT_TIMESTAMP";
        }

        return "'" . $this->default . "'";
    }

    public function getUpdate(): string
    {
        if ($this->update === null) {
            return "NULL";
        } else if ($this->update == 'CURRENT_TIMESTAMP') {
            return "CURRENT_TIMESTAMP";
        }

        return "'" . $this->update . "'";
    }

    public function foreign(string $table, string $column)
    {
        $this->foreign = [$table => $column];
        return $this;
    }

    public function default(int|string|float|null $value)
    {
        $this->default = $value;
        $this->null = true;
        return $this;
    }

    public function update(int|string|float|null $value)
    {
        $this->update = $value;
        return $this;
    }

    public function comment(string $comment)
    {
        $this->comment = $comment;
        return $this;
    }

    public function null()
    {
        $this->null = true;
        $this->default = null;
        return $this;
    }

    public function unsigned()
    {
        $this->signed = false;
        return $this;
    }

    public function signed()
    {
        $this->signed = true;
        return $this;
    }

    public function unique()
    {
        $this->unique = true;
        $this->index = false;
        $this->primary = false;
        return $this;
    }

    public function index()
    {
        $this->index = true;
        $this->unique = false;
        $this->primary = false;
        return $this;
    }

    public function primary()
    {
        $this->primary = true;
        $this->unique = false;
        $this->index = false;
        return $this;
    }

    public function increment()
    {
        $this->increment = true;
        $this->null = true;
        return $this;
    }

    public function __toString()
    {
        return $this->column;
    }
}
