<?php

namespace Atlantis\Models;

use Atlantis\{App, Column, Volatable};
use stdClass;

class Layout extends Volatable
{
    public int $id = 0;
    public string $name = '';
    public string $title = '';
    public string $remarks = '';
    public array $layout = [];
    public array $access = [];

    public static string $tableName = 'layouts';

    function getTableStructure(): array
    {
        return [
            'id' => new Column(
                column: 'id',
                width: 60,
                name: 'ID',
                title: 'ID',
                edit: false
            ),
            'name' => new Column(
                column: 'name',
                name: App::$lang->get('layouts_name'),
                title: App::$lang->get('layouts_name_title'),
                width: 200,
                sort: 'text',
            ),
            'title' => new Column(
                column: 'title',
                name: App::$lang->get('layouts_title'),
                title: App::$lang->get('layouts_title_title'),
                width: 200,
                sort: 'text',
            ),
            'remarks' => new Column(
                column: 'remarks',
                name: App::$lang->get('layouts_remarks'),
                title: App::$lang->get('layouts_remarks_title'),
                width: 200,
                sort: 'text',
            )
        ];
    }
}
