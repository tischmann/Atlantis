<?php

namespace Atlantis;

use DateTime;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

/**
 * Atlantis table class
 *
 * Table column related property names MUST be declared in camelCase;
 *
 * Table columns MUST be declared in snake_case;
 */
class Table extends Model
{
    public stdClass $row;
    const DEFAULT_COLUMN_WIDTH = 100;

    function getTableStructure(): stdClass
    {
        return (object)[
            'id' => (object)[
                'db' => (object) [
                    'type' => 'int',
                    'length' => 11,
                    'null' => false,
                    'primary_key' => true,
                    'default' => null,
                    'unique' => false,
                    'index' => false,
                    'auto_increment' => true,
                    'foreign_key' => false,
                    'comment' => null
                ],
                'width' => self::DEFAULT_COLUMN_WIDTH,
                'name' => 'ID',
                'title' => 'ID'
            ]
        ];
    }

    function fetchTableRows(): array
    {
        return $this::get();
    }

    function getTotal(): int
    {
        return $this::count();
    }

    function getTableHeader(): stdClass
    {
        $struct = $this->getTableStructure();

        return (object) [
            'id' => $struct->id
        ];
    }

    function getTableRows(): array
    {
        $rows = [];

        foreach ($this->fetchTableRows() as $row) {

            $className = get_class($this);

            $model = new $className();
            $model->init($row);

            $rowClass = $model->getTableRowStyle();
            $columns = [];

            foreach ($this->getTableHeader() as $column => $args) {
                $columns[$column] = [
                    'class' => $model->getTableColumnStyle($column),
                    'value' => $model->getTableColumnValue($column),
                ];
            }

            $rows[] = [
                'id' => $row->id,
                'class' => $rowClass,
                'columns' => $columns,
            ];
        }

        return $rows;
    }

    function getTableData($rowsOnly): stdClass
    {
        $pagination = new Pagination($this->getTotal(), 1, 10);

        $data = (object) [
            'pager' => (object) [
                'total' => $pagination->total,
                'page' => $pagination->page,
                'limit' => $pagination->limit,
                'first' => $pagination->first,
                'prev' => $pagination->prev,
                'next' => $pagination->next,
                'last' => $pagination->last,
            ],
            'status' => 1,
            'message' => App::$lang->get('success'),
            'tbody' => $this->getTableRows()
        ];

        if (!$rowsOnly) {
            $data->thead = $this->getTableHeader();
        }

        return $data;
    }

    function getTableColumnValue(string $column): string
    {
        $key = $column;

        if (!property_exists($this, $key)) {
            return '?';
        }

        $property = new ReflectionProperty(get_class($this), $key);
        $type = $property->getType();
        assert($type instanceof ReflectionNamedType);

        switch ($type->getName()) {
            case 'object':
            case 'iterable':
            case 'array':
            case 'stdClass':
                return json_encode($this->{$key}, 256 | 32);
            case 'DateTime':
                return $this->{$key} ? $this->{$key}->format("Y-m-d H:i:s") : '';
            default:
                return (string) $this->{$key};
        }
    }

    function getTableRowStyle(): array
    {
        return [];
    }

    function getTableColumnStyle(string $column): array
    {
        return [];
    }

    function getAddForm(): string
    {
        $add = new Template('Tablesorter/Add');
        $add->set('controller', $this::$tableName);

        foreach ($this->getTableStructure() as $column => $args) {
            if ($args->edit ?? true) {
                continue;
            }

            $input = new Template('Tablesorter/Input');
            $input->set('label', $args->name)
                ->set('column', $column);
            $add->set('inputs', $input->render());
        }

        return $add->render();
    }

    function getRowActions($data): stdClass
    {
        return (object) [
            'deleterow' => (object) [
                'data' => ['id' => $data->id],
                'title' => App::$lang->get('table_delete_row'),
                'text' => App::$lang->get('delete'),
                'icon' => null,
            ],
        ];
    }

    function insertRow(): bool
    {
        return $this::insert($this->getAddValues());
    }

    function getAddValues(): array
    {
        $array = [];

        foreach ($this->getTableStructure() as $column => $args) {
            $array["`{$column}`"] = $this->{$column};
        }

        return $array;
    }

    function deleteRow(): bool
    {
        return $this::where('id', '=', $this->id)
            ->limit(1)
            ->delete();
    }

    function updateColumn(string $column, string $value): bool
    {
        return $this::where('id', '=', $this->id)
            ->update([$column => $value]);
    }

    function getCellType(string $column): string
    {
        return 'input';
    }

    function getCellOptions(string $column): array
    {
        return [];
    }

    function getCellInput(string $column): stdClass
    {
        $raw = $this->{$column} ?? null;

        $data = (object) [
            'value' => $this->getTableColumnValue($column),
            'data' => (object) [],
            'type' => $this->getCellType($column)
        ];

        if ($data->value != $raw) {
            $data->data->value = $raw;
        }

        switch ($data->type) {
            case 'select':
                $data->options = $this->getCellOptions($column);
                break;
        }

        return $data;
    }

    public function renderContent(): string
    {
        $tpl = new Template('Tablesorter/Table');
        $tpl->set('url', $this::$tableName);
        return $tpl->render();
    }
}
