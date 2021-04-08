<?php

namespace Atlantis\Controllers;

use Atlantis\{Column, Error, Request, Response, Volatable};
use Atlantis\Controllers\{Controller};
use Atlantis\Models\{User};
use Exception;

class VolatableController extends Controller
{
    public Volatable $model;

    public function index(...$args)
    {
        $request = new Request();
        $request->validate([
            'action' => 'string'
        ]);

        $action = $request->action;
        $layout = $request->layout ?? 0;
        $window = $request->window ?? 0;
        $layoutConfig = User::current()->layouts[$layout][$window] ?? [];

        $this->model = $this->getModel();
        $this->model->layoutConfig = $layoutConfig;

        $page = $request->page ?? null;
        $limit = $request->limit ?? null;
        $order = (array) ($request->order ?? []);
        $search = (array) ($request->search ?? []);
        $filter = (array) ($request->filter ?? []);
        $qfilter = $request->qfilter ?? null;

        if ($search) {
            foreach ($search as $column => $value) {
                $this->model->query->where($column, 'LIKE', "%{$value}%");
            }
        } else if ($filter) {
            foreach ($filter as $column => $values) {
                $this->model->query->whereIn($column, $values);
            }
        } else if ($qfilter) {
            $this->model->query = $this->model->getQuickFilter()[$qfilter]->query;
        }

        if ($order) {
            $this->model->query->order(
                array_keys($order)[0],
                array_values($order)[0],
                true
            );
        }

        if ($page && $limit) {
            $this->model->pagination->__construct(
                total: $this->model->getTotal(),
                page: $page,
                limit: $limit
            );

            $this->model->query->limit($this->model->pagination->limit)
                ->offset($this->model->pagination->offset);
        }

        try {
            return $this->$action();
        } catch (Exception $ex) {
            Response::response(new Error(
                title: lang('warning'),
                message: $ex->getMessage(),
                type: 'danger'
            ));
        }
    }

    protected function resetPagination()
    {
        $this->model->pagination->page(1);
        $this->model->query->limit($this->model->pagination->limit)
            ->offset($this->model->pagination->offset);
    }

    protected function getModel()
    {
        return new Volatable();
    }

    protected function getResponse($data = []): string
    {
        $response = new Response($data);
        return $response->json();
    }

    protected function saveState()
    {
        $request = new Request();
        $request->validate([
            'columns' => 'object',
            'layout' => 'int',
            'window' => 'string'
        ]);

        User::current()->setLayoutState(
            $request->layout,
            $request->window,
            $request->columns
        );

        return $this->getResponse([
            'status' => 1,
            'message' => lang('success')
        ]);
    }

    protected function get(): string
    {
        $request = new Request();

        return $this->getResponse(
            $this->model->getTableData($request->pager ?? true)
        );
    }

    protected function getFilter(): string
    {
        $request = new Request();
        $request->validate([
            'column' => 'string'
        ]);
        return $this->getResponse($this->model->getFilterItems($request->column));
    }

    protected function insertForm(): string
    {
        return $this->getResponse([
            'status' => 1,
            'message' => lang('success'),
            'content' => $this->model->getAddRowForm()
        ]);
    }

    protected function insertRow(): string
    {
        $request = new Request();

        $this->model->init((array) $request)->insertRow();

        return $this->getResponse([
            'status' => 1,
            'message' => lang('success')
        ]);
    }

    protected function contextMenu(): string
    {
        $request = new Request();

        return $this->getResponse([
            'status' => 1,
            'message' => lang('success'),
            'buttons' => $this->model->getRowActions($request),
        ]);
    }

    protected function deleteRow(): string
    {
        $request = new Request();

        $request->validate(['id' => '']);

        $this->model->init($request->id)->deleteRow();

        return $this->getResponse([
            'status' => 1,
            'message' => lang('success')
        ]);
    }

    protected function getCell(): string
    {
        $request = new Request();

        $request->validate(['id' => 'int', 'column' => 'string']);

        $this->model->__construct($request->id);

        $column = new Column($request->column);

        $input = $this->model->getCellInput($column);

        $response = (object) [
            'status' => 1,
            'message' => lang('success'),
            'type' => $input->type,
            'data' => $input->data,
            'value' => $input->value
        ];

        if ($input->type == 'select') {
            $response->options = $input->options;
        }

        return $this->getResponse($response);
    }

    protected function updateCell(): string
    {
        $request = new Request();

        $request->validate([
            'id' => 'int',
            'column' => 'string',
            'value' => ''
        ]);

        $column = new Column(
            column: $request->column,
            value: $request->value
        );

        $this->model->__construct($request->id);

        $column = $this->model->updateColumn($column);

        return $this->getResponse([
            'status' => 1,
            'message' => lang('success'),
            'value' => $this->model->getTableColumnValue($column),
            'sync' => $this->model->getTableRowSync($column)
        ]);
    }
}
