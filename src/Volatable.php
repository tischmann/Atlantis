<?php

namespace Atlantis;

use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

class Volatable extends Model
{
    public stdClass $row;
    public array $layoutConfig = [];
    public bool $pagerEnabled = true;
    public bool $quickFilterEnabled = true;

    function __construct(array|stdClass|int $args = null)
    {
        parent::__construct($args);

        if ($this->pagerEnabled && $this->quickFilterEnabled) {
            foreach ($this->getQuickFilter() as $filter) {
                if ($filter->default) {
                    $this->query = $filter->query;
                    break;
                }
            }
        }

        return $this;
    }

    public function getTableStructure(): array
    {
        return [
            'id' =>  new Column(column: 'id', name: 'ID', title: 'ID')
        ];
    }

    public function getQuickFilter(): array
    {
        return [
            'total' => new QuickFilter(
                label: Language::get('total'),
                title: Language::get('total_title'),
                query: clone $this->getDefaultQuery()
            ),
        ];
    }

    public function getFilterItems(string $column): array
    {
        $column = $this->getTableStructure()[$column];
        $values = [];

        foreach ($this->query->limit(0)->offset(0)->distinct($column->column) as $value) {
            $clone = clone $this;
            $clone->init([$column->column => $value]);

            $values[] = [
                'v' => $clone->getTableFilterValue($column),
                't' => $clone->getTableFilterTitle($column),
            ];
        }

        return [
            'status' => 1,
            'items' => [
                $column->column => $values,
            ],
        ];
    }

    public function getTotal(): int
    {
        $query = clone ($this->query);
        return $query->limit(0)->offset(0)->count();
    }

    public function getTableHeader(): array
    {
        $header = $this->getTableColumns();

        foreach ($header as $index => $column) {
            $header[$index] = $column->volatable();
        }

        return $header;
    }

    public function getTableColumns(): array
    {
        $columns = $this->getTableStructure();

        $array = [];

        if (!$this->layoutConfig) {
            foreach ($columns as $column) {
                $array[] = $column;
            }
        } else {
            foreach ($this->layoutConfig as $column => $config) {
                if (!array_key_exists($column, $columns)) {
                    continue;
                }

                if (!Auth::canSelect($this::$tableName, $column)) {
                    continue;
                }

                $value = $columns[$column];

                if (array_key_exists('width', $config)) {
                    $value->width = $config['width'];
                }

                if (array_key_exists('dir', $config)) {
                    $value->dir = $config['dir'];
                }

                $array[] = $value;
            }
        }

        foreach ($array as $index => $column) {
            if ($column->column == 'id') {
                $array[$index]->edit = false;
            } else if (Auth::canUpdate($this::$tableName, $column->column)) {
                $array[$index]->edit = true;
            }
        }

        return $array;
    }

    public function getTableColumn($column): array
    {
        $value = $this->getTableColumnValue($column);
        $title = $this->getTableColumnTitle($column);

        $column = [
            'c' => $this->getTableColumnStyle($column),
            'v' => $value,
        ];

        if ($title !== $value) {
            $column['t'] = $title;
        }

        return $column;
    }

    function getTableColumnStyle(Column $column): array
    {
        return [];
    }

    public function getTableColumnValue(Column $column): string
    {
        $key = $column->__toString();

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

    public function getTableColumnTitle(Column $column): string|null
    {
        return null;
    }

    public function getTableFilterValue(Column $column): string
    {
        return $this->getTableColumnValue($column);
    }

    public function getTableFilterTitle(Column $column): string|null
    {
        return $this->getTableColumnTitle($column);
    }

    public function getTableRows(): array
    {
        $rows = [];
        $layout = $this->getTableColumns();

        foreach ($this->query->get() as $row) {
            $model = get_class($this);
            $model = new $model($row);

            $columns = [];

            foreach ($layout as $column) {
                $column->edit = Auth::canUpdate($this::$tableName, $column);
                $columns[] = $model->getTableColumn($column);
            }

            $rows[] = [
                'i' => $row->id,
                'c' => $model->getTableRowStyle(),
                'r' => $columns,
            ];
        }

        return $rows;
    }

    public function getTableData(bool $thead = true): array
    {
        $data = [
            'status' => 1,
            'message' => Language::get('success'),
            'tbody' => $this->getTableRows()
        ];

        if ($this->pagination->isset()) {
            $data['pager'] = $this->pagination->total($this->getTotal());
        }

        if ($this->quickFilterEnabled) {
            $data['qfilter'] = [];

            foreach ($this->getQuickFilter() as $key => $qfilter) {
                $data['qfilter'][$key] = $qfilter->volatable();
            }
        }

        if ($thead) {
            $data['thead'] = (object) $this->getTableHeader();
        }

        return $data;
    }

    function getTableRowStyle(): array
    {
        return [];
    }

    function getAddRowForm(): string
    {
        return 'Add form';
    }

    function getRowActions($data): stdClass
    {
        return (object) [
            'deleterow' => (object) [
                'data' => ['id' => $data->id],
                'title' => Language::get('table_delete_row'),
                'text' => Language::get('delete'),
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

        foreach ($this->getTableStructure() as $column) {
            $array["`{$column}`"] = $this->{$column};
        }

        return $array;
    }

    function deleteRow(): bool
    {
        if (!Auth::canDelete($this::$tableName)) {
            Response::response(new Error(
                title: Language::get('warning'),
                message: Language::get('access_denied'),
                type: 'warning'
            ));
        }

        return $this::where('id', '=', $this->id)
            ->limit(1)
            ->delete();
    }

    function updateColumn(Column $column): Column
    {
        if (!Auth::canUpdate($this::$tableName, $column->column)) {
            Response::response(new Error(
                title: Language::get('warning'),
                message: Language::get('access_denied'),
                type: 'warning'
            ));
        }

        $this::where('id', '=', $this->id)
            ->update([$column->column => $column->value]);

        $this->__construct($this->id);

        return $column;
    }

    function getCellType(Column $column): string
    {
        return 'input';
    }

    function getCellOptions(Column $column): array
    {
        return [];
    }

    function getCellInput(Column $column): stdClass
    {
        $columnName = $column->column;

        if (!Auth::canUpdate($this::$tableName, $columnName)) {
            Response::response(new Error(
                title: Language::get('warning'),
                message: Language::get('access_denied'),
                type: 'warning'
            ));
        }

        $raw = $this->{$columnName} ?? null;
        $columns = $this->getTableStructure();
        $column = $columns[$columnName] ?? null;

        if (!$column || $raw === null) {
            Response::response(new Error(
                title: Language::get('warning'),
                message: Language::get('column_not_exists') . ": {$columnName}",
                type: 'warning'
            ));
        }

        $column->value = $this->getTableColumnValue($column);

        $data = (object) [
            'value' => $column->value,
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

    public function render(string $route = null, bool $pager = null): string
    {
        if (!Auth::canSelect($this::$tableName)) {
            Response::response(new Error(
                message: Language::get('access_denied')
            ));
        }

        $route = $route ?? "/volatable/{$this::$tableName}";
        $pager = (int)($pager ?? $this->pagerEnabled);

        return View::render('volatable/table', [
            'route' => $route,
            'pager' => $pager,
        ]);
    }

    public function getTableRowSync(Column $column): array
    {
        return [
            'row' => [
                'css' => $this->getTableRowStyle(),
            ],
            'column' => [
                'css' => $this->getTableColumnStyle($column),
                'title' => $this->getTableColumnTitle($column),
            ]
        ];
    }
}
