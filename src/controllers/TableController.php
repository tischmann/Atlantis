<?php

namespace Atlantis\Controllers;

use Atlantis\{App, Error, Request, Table};
use Atlantis\Controllers\{Controller};
use Exception;

class TableController extends Controller
{
    public function index()
    {
        $request = new Request();
        $request->validate(['action' => '']);

        $action = $request->action;

        try {
            return $this->$action();
        } catch (Exception $ex) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: "Метод {$action} не существует",
                type: 'warning'
            );
            return $this->getResponse();
        }
    }

    protected function getModel()
    {
        return new Table();
    }

    protected function getResponse($data = []): string
    {
        $data = (object) $data;

        if (App::hasErrors()) {
            $data->message = App::$error->getMessage();
            $data->status = 0;
        }

        header("Content-Type: application/json; charset=UTF-8");

        return json_encode($data, 256, 32);
    }

    protected function get(): string
    {
        $request = new Request();

        return $this->getResponse(
            $this->getModel()
                ->getTableData($request->rowsonly ?? null)
        );
    }

    protected function insertForm(): string
    {
        return $this->getResponse([
            'status' => 1,
            'message' => App::$lang->get('success'),
            'content' => $this->getModel()->getAddForm()
        ]);
    }

    protected function insertRow(): string
    {
        $request = new Request();

        $model = $this->getModel();

        $model->init($request);

        $model->insertRow();

        if (App::$error) {
            return $this->getResponse();
        }

        return $this->getResponse([
            'status' => 1,
            'message' => App::$lang->get('success')
        ]);
    }

    protected function contextMenu(): string
    {
        $request = new Request();

        if (App::$error) {
            return $this->getResponse();
        }

        return $this->getResponse([
            'status' => 1,
            'message' => App::$lang->get('success'),
            'buttons' => $this->getModel()->getRowActions($request),
        ]);
    }

    protected function deleteRow(): string
    {
        $request = new Request();

        $request->validate(['id' => '']);

        $this->getModel()->init($request->id)->deleteRow();

        if (App::$error) {
            return $this->getResponse();
        }

        return $this->getResponse([
            'status' => 1,
            'message' => App::$lang->get('success')
        ]);
    }

    protected function cell(): string
    {
        $request = new Request();

        $request->validate(['id' => '', 'column' => '']);

        $input = $this->getModel()
            ->init($request->id)
            ->getCellInput($request->column);

        if (App::$error) {
            return $this->getResponse();
        }

        $response = (object) [
            'status' => 1,
            'message' => App::$lang->get('success'),
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

        $request->validate(['id' => '', 'column' => '', 'value' => '']);

        $model = $this->getModel();
        $model->init($request->id)
            ->updateColumn(
                $request->column,
                $request->value
            );

        if (App::$error) {
            return $this->getResponse();
        }

        return $this->getResponse([
            'status' => 1,
            'message' => App::$lang->get('success'),
            'value' => $model->getTableColumnValue($request->column)
        ]);
    }
}
