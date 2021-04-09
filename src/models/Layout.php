<?php

namespace Atlantis\Models;

use Atlantis\{Column, Language, Volatable};

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
                name: Language::get('layouts_name'),
                title: Language::get('layouts_name_title'),
                width: 200,
                sort: 'text',
            ),
            'title' => new Column(
                column: 'title',
                name: Language::get('layouts_title'),
                title: Language::get('layouts_title_title'),
                width: 200,
                sort: 'text',
            ),
            'remarks' => new Column(
                column: 'remarks',
                name: Language::get('layouts_remarks'),
                title: Language::get('layouts_remarks_title'),
                width: 200,
                sort: 'text',
            )
        ];
    }
}
