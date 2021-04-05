<?php

namespace Atlantis;

use Atlantis\Models\{User, Layout};

class Windows
{
    public Layout $layout;
    public array $windows = [];
    public array $resizers = [];
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

    function __construct(Layout $layout = null)
    {
        if ($layout) {
            $this->layout = $layout;
        }

        foreach ($this->layout->layout as $type => $class) {
            $this->windows[] = (object) [
                'type' => $type,
                'model' =>  new $class()
            ];
        }

        foreach ($this->getResizers() as $type) {
            $this->resizers[] = (object) [
                'type' => $type,
                'axis' => self::getAxis($type)
            ];
        }
    }

    public static function available(): array
    {
        if (Auth::isAdmin()) {
            $layouts = Layout::get();
        } else {
            $layouts = Layout::whereIn(
                'id',
                array_keys(User::current()->layouts)
            )->get();
        }

        foreach ($layouts as $key => $layout) {
            $layouts[$key] = new Layout($layout);
        }

        return $layouts;
    }

    private function getType()
    {
        $layout = array_keys($this->layout->layout);
        $windows = array_column(static::$config, 'windows');

        foreach ($windows as $index => $values) {
            if (!array_diff($values, $layout)) {
                return $index;
            }
        }

        return $index;
    }

    private static function getAxis(string $type): string
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

    private function getResizers(): array
    {
        return static::$config[$this->getType()]['resizers'] ?? [];
    }
}
