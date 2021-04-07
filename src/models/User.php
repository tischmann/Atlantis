<?php

namespace Atlantis\Models;

use Atlantis\{Column, Volatable};
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
                width: 300,
                name: lang('users_login'),
                title: lang('users_login'),
                sort: 'text',
            ),
            'role' => new Column(
                column: 'role',
                width: 135,
                name: lang('users_role'),
                title: lang('users_role'),
                sort: 'text',
                resize: false,
            ),
            'name' => new Column(
                column: 'name',
                width: 300,
                name: lang('users_name'),
                title: lang('users_name'),
                sort: 'text',
            ),
            'email' => new Column(
                column: 'email',
                width: 250,
                name: lang('users_email'),
                title: lang('users_email'),
                sort: 'text',
            ),
            'remarks' => new Column(
                column: 'remarks',
                width: 300,
                name: lang('users_remarks'),
                title: lang('users_remarks'),
                sort: 'text',
            ),
            'status' => new Column(
                column: 'status',
                width: 79,
                name: lang('users_status'),
                title: lang('users_status'),
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
                name: lang('users_remarks'),
                title: lang('users_remarks'),
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

                return lang('users_role_' . $this->role);
            case 'status':
                return lang('users_status_' . $this->status);
            default:
                return parent::getTableColumnValue($column);
        }
    }

    function getTableColumnTitle(Column $column): string|null
    {
        switch ($column) {
            case 'role':
                if ($this->role === null) {
                    return '';
                }

                return lang('users_role_' . $this->role);
            case 'status':
                return lang('users_status_' . $this->status);
            default:
                return parent::getTableColumnTitle($column);
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
            case 'status':
                return 'select';
            default:
                return 'input';
        }
    }

    function getCellOptions(Column $column): array
    {
        switch ($column->column) {
            case 'role':
                return [
                    0 => lang('users_role_0'),
                    1 => lang('users_role_1'),
                    2 => lang('users_role_2')
                ];
            case 'status':
                return [
                    0 => lang('users_status_0'),
                    1 => lang('users_status_1')
                ];
            default:
                return [];
        }
    }

    public function setLayoutState(
        string $layout,
        string $window,
        stdClass $state
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
