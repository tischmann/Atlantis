<?php

namespace Atlantis\Models;

use Atlantis\{App, Date, Query, Table, Template};
use DateTime;
use DateTimeZone;
use stdClass;

class Layout extends Table
{
    public int $id = 0;
    public string $name = '';
    public string $title = '';
    public string $remarks = '';
    public array $layout = [];
    public array $access = [];

    public static string $tableName = 'layouts';

    public DateTime $date;
    static array $config = [
        0 => [
            'label' => '[lang=windows_label_c]',
            'windows' => ['c'],
            'resizers' => []
        ],
        1 => [
            'label' => '[lang=windows_label_l_r]',
            'windows' => ['l', 'r'],
            'resizers' => ['lr']
        ],
        2 => [
            'label' => '[lang=windows_label_t_b]',
            'windows' => ['t', 'b'],
            'resizers' => ['tb']
        ],
        3 => [
            'label' => '[lang=windows_label_tl_r_bl]',
            'windows' => ['tl', 'r', 'bl'],
            'resizers' => ['tbl', 'lr']
        ],
        4 => [
            'label' => '[lang=windows_label_l_tr_br]',
            'windows' => ['l', 'tr', 'br'],
            'resizers' => ['tbr', 'lr']
        ],
        5 => [
            'label' => '[lang=windows_labelL_tl_tr_b]',
            'windows' => ['tl', 'tr', 'b'],
            'resizers' => ['tlr', 'tb']
        ],
        6 => [
            'label' => '[lang=windows_label_t_bl_br]',
            'windows' => ['t', 'bl', 'br'],
            'resizers' => ['blr', 'tb']
        ],
        7 => [
            'label' => '[lang=windows_label_tl_tr_bl_br]',
            'windows' => ['tl', 'tr', 'bl', 'br'],
            'resizers' => ['lr', 'tbl', 'tbr']
        ]
    ];

    function getAll(): array
    {
        $array = [];

        foreach ($this::get() as $row) {
            $layout = new self($row);
            $array[$layout->id] = $layout;
        }

        return $array;
    }

    function date($date)
    {
        if (Date::valid($date)) {
            $this->date = new DateTime($date, new DateTimeZone('UTC'));
        }

        return $this;
    }

    function getTableStructure(): stdClass
    {
        return (object)[
            'id' => (object)[
                'width' => 60,
                'name' => 'ID',
                'title' => 'ID',
                'edit' => false
            ],
            'name' => (object)[
                'name' => App::$lang->get('views_name'),
                'title' => App::$lang->get('views_name_title'),
                'width' => 200,
                'length' => 255,
                'regex' => "/^[\p{L}\p{N}\s\-]{3,}$/u"
            ],
            'title' => (object)[
                'name' => App::$lang->get('views_title'),
                'title' => App::$lang->get('views_title_title'),
                'width' => 200,
                'length' => 255,
                'regex' => "/^[\p{L}\p{N}\s\-]{3,}$/u"
            ],
            'remarks' => (object) [
                'name' => App::$lang->get('views_remarks'),
                'title' => App::$lang->get('views_remarks_title'),
                'width' => 200,
                'length' => 1024
            ],
            'layout' => (object)[
                'name' => App::$lang->get('views_layout'),
                'title' => App::$lang->get('views_layout_title'),
                'width' => 21,
                'edit' => false,
            ],
            'access' => (object) [
                'name' => App::$lang->get('views_access'),
                'title' => App::$lang->get('views_access_title'),
                'width' => 21,
                'edit' => false
            ]
        ];
    }

    function getTableHeader(): stdClass
    {
        $struct = $this->getTableStructure();

        return (object)[
            'name' => $struct->name,
            'title' => $struct->title,
            'remarks' => $struct->remarks,
        ];
    }

    function type()
    {
        $layout = array_keys($this->layout);
        $windows = array_column(static::$config, 'windows');

        foreach ($windows as $index => $values) {
            if (!array_diff($values, $layout)) {
                return $index;
            }
        }

        return $index;
    }

    function axis(string $type): string
    {
        switch ($type) {
            case 'tb':
            case 'tbl':
            case 'tbr':
                return 'y';
            default:
                return 'x';
        }
    }

    function resizers(): array
    {
        $resizers = static::$config[$this->type()]['resizers'] ?? [];
        return $resizers;
    }

    public function render(): string
    {
        $tpl = new Template('Layout/Layout');

        foreach ($this->resizers() as $type) {
            $axis = 'x';
            $resizer = new Template('Layout/Resizer');
            $resizer->set('type', $type)
                ->set('axis', $this->axis($type));
            $tpl->set('resizers', $resizer->render());
        }

        foreach ($this->layout as $type => $args) {
            $model = new $args['class']();

            if (App::$user->canSelect($model::$tableName)) {
                $window = new Template('Layout/Window');
                $window->set('type', $type)
                    ->set('content', $model->renderContent());
                $tpl->set('windows', $window->render());
            }
        }

        return $tpl->render();
    }

    // public function init(array $row = [])
    // {
    //     parent::init($row);

    //     $this->id = (int) ($row['id'] ?? 0);

    //     $this->name = $row['name'] ?? null;

    //     $this->title = $row['title'] ?? null;

    //     $this->remarks = $row['remarks'] ?? null;

    //     $this->layout = [];

    //     if (array_key_exists('layout', $row)) {
    //         $this->layout = json_decode($row['layout'], true) ?? [];
    //     }

    //     $this->ipAccess = [];

    //     if (array_key_exists('ipaccess', $row)) {
    //         $ipaccess = json_decode($row['ipaccess'], true) ?? [];

    //         foreach ($ipaccess as $ip => $comment) {
    //             $this->ipAccess[$ip] = $comment;
    //         }
    //     }

    //     return $this;
    // }

    // public function getRow(): array
    // {
    //     $layout = "";
    //     $ipaccess = "";

    //     if ($this->layout) {
    //         $layout = json_encode($this->layout, 32 | 256);
    //     }

    //     if ($this->ipAccess) {
    //         $ipaccess = json_encode($this->ipAccess, 32 | 256);
    //     }

    //     $this->row = [
    //         "id" => (int) $this->id,
    //         "name" => (string) $this->name,
    //         "title" => (string) $this->title,
    //         "remarks" => (string) $this->remarks,
    //         "layout" => $layout,
    //         "ipaccess" => $ipaccess
    //     ];

    //     return $this->row;
    // }

    // public function checkValue(string $column, string $value, string $regex = ""): bool
    // {
    //     if (!parent::checkValue($column, $value, $regex)) return false;

    //     if ($column == "name") {
    //         if (parent::isColumnExists($column, mb_strtoupper($value, 'UTF-8'))) {
    //             $this->lastError = $this->user->lang('VIEWS_ERROR_VIEW_EXIST');
    //             return false;
    //         }
    //     }

    //     return true;
    // }

    // public function update(string $column, $value): bool
    // {
    //     if (parent::update($column, $value)) {
    //         $select = $this->pdo->prepare("SELECT id FROM users WHERE views REGEXP ? || role = 'ADMIN'");
    //         $select->execute(["[^0-9]{$this->id}[^0-9]"]);

    //         foreach ($select->fetchAll() as $row) {
    //             Sync::put(["userinit" => $row['id'], "once" => true]);
    //         }

    //         return true;
    //     }

    //     return false;
    // }

    // public function addRowForm(): string
    // {
    //     $layout = json_encode($this->layout, 256 | 32);
    //     $ipaccess = json_encode($this->ipAccess, 256 | 32);
    //     return <<<HTML
    //         <table>
    //             <tr>
    //                 <td width="300px">{$this->getInputField('name', (string)$this->name)}</td>
    //                 <td width="300px">{$this->getInputField('title', (string)$this->title)}</td>
    //             </tr>
    //             <tr>
    //                 <td colspan=2>{$this->getTextareaField('remarks', (string)$this->remarks)}</td>
    //             </tr>
    //         </table>
    //         <input type="hidden" data-col="layout" value="{$layout}"/>
    //         <input type="hidden" data-col="ipaccess" value="{$ipaccess}"/>
    //     HTML;
    // }

    // public function add(bool $skipCheck = false): int
    // {
    //     $row = $this->getRow();

    //     if (!$this->checkRow()) return 0;

    //     $result = parent::add(true);

    //     if ($result) {
    //         $select = $this->pdo->query("SELECT id FROM users WHERE role = 'ADMIN'");

    //         foreach ($select->fetchAll() as $row) {
    //             Sync::put(["userinit" => $row['id'], "once" => true]);
    //         }
    //     }

    //     return $result;
    // }

    // public function delete(): bool
    // {
    //     if (!parent::delete()) return false;

    //     $select = $this->pdo->prepare("SELECT * FROM users WHERE views REGEXP ? || role = 'ADMIN'");
    //     $select->execute(["[^0-9]{$this->id}[^0-9]"]);

    //     foreach ($select->fetchAll() as $row) {
    //         $user = new User($this->pdo);
    //         $user->init($row);

    //         if ($user->isAdmin()) {
    //             Sync::put(["userinit" => $user->id, "once" => true]);
    //             continue;
    //         }

    //         if (!$user->views) continue;

    //         $views = array_diff($user->views, [$this->id]);

    //         if (count($views) == count($user->views)) continue;

    //         $user->disableLog();
    //         $user->update("views", json_encode($views, 32));
    //         $user->enableLog();

    //         Sync::put(["userinit" => $user->id, "once" => true]);
    //     }

    //     return true;
    // }
}
