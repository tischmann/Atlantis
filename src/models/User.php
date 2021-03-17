<?php

namespace Atlantis\Models;

use Atlantis\{App, Column, Error, Response, Volatable, Template};
use stdClass;

class User extends Volatable
{
    public static User $instance;
    public int $id = 0;
    public string $login = '';
    public string $password = '';
    public int $role = 0; // 0 - default, 1 - admin, 2 - user
    public string $name = '';
    public string $email = '';
    public string $remarks = '';
    public array $layouts = [];
    public int $status = 0;

    public static string $tableName = 'users';

    public static function current(self $instance = null)
    {
        if ($instance) {
            static::$instance = $instance;
        }

        return static::$instance;
    }

    function isAdmin(): bool
    {
        return $this->role == 1;
    }

    function getTableStructure(): array
    {
        return [
            'id' => new Column(
                column: 'id',
                width: 60,
                name: 'ID',
                title: 'ID',
                sort: 'number',
            ),
            'login' => new Column(
                column: 'login',
                width: 100,
                name: App::$lang->get('users_login'),
                title: App::$lang->get('users_login'),
                sort: 'text',
            ),
            'role' => new Column(
                column: 'role',
                width: 135,
                name: App::$lang->get('users_role'),
                title: App::$lang->get('users_role'),
                sort: 'text',
                resize: false,
            ),
            'name' => new Column(
                column: 'name',
                width: 150,
                name: App::$lang->get('users_name'),
                title: App::$lang->get('users_name'),
                sort: 'text',
            ),
            'email' => new Column(
                column: 'email',
                width: 250,
                name: App::$lang->get('users_email'),
                title: App::$lang->get('users_email'),
                sort: 'text',
            ),
            'status' => new Column(
                column: 'status',
                width: 79,
                name: App::$lang->get('users_status'),
                title: App::$lang->get('users_status'),
                sort: 'text',
                resize: false,
            )
        ];
    }

    function getTableDetailsStructure(): array
    {
        return [
            'remarks' => new Column(
                column: 'remarks',
                width: 250,
                name: App::$lang->get('users_remarks'),
                title: App::$lang->get('users_remarks'),
                sort: 'text',
            ),
        ];
    }

    function getAddValues(): array
    {
        return [
            'login' => $this->login,
            'role' => $this->role,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this::getHash($this->password),
        ];
    }

    function getTableColumnValue(Column $column): string
    {
        switch ($column) {
            case 'role':
                if ($this->role === null) {
                    return '';
                }

                return App::$lang->get('users_role_' . $this->role);
            case 'status':
                return App::$lang->get('users_status_' . $this->status);
            default:
                return parent::getTableColumnValue($column);
        }
    }

    function getTableRowStyle(): array
    {
        $css = [];

        if ($this->isAdmin()) {
            $css[] = 'user-role-1';
        }

        return $css;
    }

    function getCellType(Column $column): string
    {
        switch ($column->column) {
            case 'role':
                return 'select';
        }

        return 'input';
    }

    function getCellOptions(Column $column): array
    {
        switch ($column->column) {
            case 'role':
                return [
                    1 => App::$lang->get('user_role_1'),
                    2 => App::$lang->get('user_role_2')
                ];
            default:
                return [];
        }
    }

    public function setLayoutState(
        string $layout,
        string $window,
        array $state = []
    ): bool {
        if (!$layout) {
            return true;
        }

        if (!array_key_exists($layout, $this->layouts)) {
            $this->layouts[$layout] = [];
        }

        if (array_key_exists($window, $this->layouts[$layout])) {
            $this->layouts[$layout][$window] = [];
        }

        $this->layouts[$layout][$window] = $state;

        return $this::where('id', $this->id)
            ->update([
                'layouts' => json_encode($this->layouts, 32 | 256)
            ]);
    }
}
