<?php

namespace Atlantis\Models;

use Atlantis\{App, Error, Query, Session, Table, Template};
use stdClass;

class User extends Table
{
    public int $id = 0;
    public string $login = '';
    public string $password = '';
    public int $role = 0; // 0 - default, 1 - admin, 2 - user
    public string $name = '';
    public string $email = '';
    public int $sex = 1; // 0 - female, 1 - male
    public array $layouts = [];

    public static string $tableName = 'users';

    function fetchTableRowByLogin(string $login): array
    {
        return $this::orWhere('login', '=', $login)
            ->orWhere('email', '=', $login)
            ->get();
    }

    function login($login, $password): bool
    {
        $this->reset();

        foreach ($this->fetchTableRowByLogin($login) as $row) {
            if (!$this::checkHash($password, $row->password)) {
                App::$error = new Error(
                    title: App::$lang->get('warning'),
                    message: App::$lang->get('bad_password'),
                    type: 'warning'
                );
                return false;
            }

            $this->init($row);

            Session::regenerate();

            Session::set('USER_ID', $this->id);

            return true;
        }

        App::$error = new Error(
            title: App::$lang->get('warning'),
            message: App::$lang->get('bad_login'),
            type: 'warning'
        );

        return false;
    }

    function signedIn(): bool
    {
        if (!App::$user->id || !$this->id) {
            return false;
        }

        return (bool) App::$user->id == $this->id;
    }

    function logout(): bool
    {
        $this->reset();
        Session::destroy();
        return true;
    }

    function isAdmin(): bool
    {
        return $this->role == 1;
    }

    function getAddValues(): array
    {
        return [
            'login' => $this->login,
            'role' => $this->role,
            'sex' => $this->sex,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this::getHash($this->password),
        ];
    }

    function insert(): bool
    {
        if (!$this->checkAdd()) {
            return false;
        }

        return parent::insert();
    }

    function delete(): bool
    {
        if (!$this->isAdmin()) {
            return false;
        }

        return parent::delete();
    }

    function update(string $column, string $value): bool
    {
        $result = parent::update($column, $value);

        if ($result) {
            $this->init([$column => $value]);
        }

        return $result;
    }

    function checkAdd(): bool
    {
        if ($this->isExists()) {
            return false;
        }

        if (!$this->isLoginValid($this->login)) {
            return false;
        }

        if (!$this->isPasswordValid($this->password)) {
            return false;
        }

        if (!$this->isEmailValid($this->email)) {
            return false;
        }

        return true;
    }

    function isLoginValid(string $login)
    {
        if (!preg_match('/[a-zA-Z0-9]+/i', $login)) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_login'),
                type: 'warning'
            );
            return false;
        }

        return true;
    }

    function isEmailValid(string $email)
    {
        if (!$email) {
            return true;
        }

        if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9-]+.+.[A-Z]{2,4}$/i', $email)) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_email'),
                type: 'warning'
            );
            return false;
        }

        return true;
    }

    function isPasswordValid(string $password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $minLength = 8;

        if (!$uppercase || !$lowercase || !$number || strlen($password) < $minLength) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('password_weak'),
                type: 'warning'
            );
            return false;
        }

        return true;
    }

    function isExists(): bool
    {
        if ($this->fetchTableRowByLogin($this->login)) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('user_exists'),
                type: 'warning'
            );
            return true;
        }

        return false;
    }

    static function getHash(string $string): string
    {
        return password_hash($string, PASSWORD_DEFAULT);
    }

    static function checkHash(string $string, $hash): bool
    {
        return password_verify($string, $hash);
    }

    function getTableStructure(): stdClass
    {
        return (object)[
            'id' => (object)[
                'db' => (object) [
                    'type' => 'int',
                    'length' => 11,
                    'primary_key' => true,
                    'auto_increment' => true
                ],
                'width' => 60,
                'name' => App::$lang->get('users_id'),
                'title' => App::$lang->get('user_id'),
            ],
            'login' => (object)[
                'db' => (object) [
                    'type' => 'varchar',
                    'length' => 255
                ],
                'width' => 100,
                'name' => App::$lang->get('users_login'),
                'title' => App::$lang->get('user_login'),
            ],
            'password' => (object)[
                'db' => (object) [
                    'type' => 'varchar',
                    'length' => 255
                ],
                'width' => 60,
                'name' => App::$lang->get('users_password'),
                'title' => App::$lang->get('user_password'),
            ],
            'role' => (object) [
                'db' => (object) [
                    'type' => 'int',
                    'length' => 4,
                    'null' => true,
                    'default' => 0,
                    'index' => true
                ],
                'width' => 50,
                'name' => App::$lang->get('users_role'),
                'title' => App::$lang->get('user_role'),
            ],
            'name' => (object)[
                'db' => (object) [
                    'type' => 'varchar',
                    'length' => 255,
                    'null' => true
                ],
                'width' => 150,
                'name' => App::$lang->get('users_name'),
                'title' => App::$lang->get('user_name'),
            ],
            'email' => (object)[
                'db' => (object) [
                    'type' => 'varchar',
                    'length' => 255,
                    'null' => true
                ],
                'width' => 250,
                'name' => App::$lang->get('users_email'),
                'title' => App::$lang->get('user_email'),
            ],
            'sex' => (object) [
                'db' => (object) [
                    'type' => 'int',
                    'length' => 1,
                    'index' => true,
                    'null' => true
                ],
                'width' => 60,
                'name' => App::$lang->get('users_sex'),
                'title' => App::$lang->get('user_sex'),
            ],
        ];
    }

    function getTableHeader(): stdClass
    {
        $struct = $this->getTableStructure();

        return (object)[
            'login' => $struct->login,
            'name' => $struct->name,
            'sex' => $struct->sex,
            'email' => $struct->email,
        ];
    }

    function getTableColumnValue(string $column): string
    {
        switch ($column) {
            case 'sex':
                return App::$lang->get('user_sex_' . $this->sex);
            case 'role':
                if ($this->role === null) {
                    return '';
                }

                return App::$lang->get('user_role_' . $this->role);
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

    function getAddForm(): string
    {
        return (new Template('User/Add'))->render();
    }

    function getCellType(string $column): string
    {
        switch ($column) {
            case 'role':
            case 'sex':
                return 'select';
        }

        return 'input';
    }

    function getCellOptions(string $column): array
    {
        switch ($column) {
            case 'role':
                return [
                    1 => App::$lang->get('user_role_1'),
                    2 => App::$lang->get('user_role_2')
                ];
            case 'sex':
                return [
                    null => '',
                    0 => App::$lang->get('user_sex_0'),
                    1 => App::$lang->get('user_sex_1')
                ];
            default:
                return [];
        }
    }

    public function canSelect(string $table): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return false;
    }

    public function availableLayouts(): array
    {
        if ($this->isAdmin()) {
            return (new Layout())->getAll();
        }

        return $this::whereIn('id', array_keys($this->layouts))
            ->get();
    }
}
