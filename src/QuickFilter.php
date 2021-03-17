<?php

namespace Atlantis;

class QuickFilter
{
    public Query $query;
    public string $label;
    public string $title;
    public string $css;
    public int $count = 0;
    public bool $skip_count;
    public bool $default = false;

    public function __construct(
        Query $query,
        string $label,
        string $title = '',
        string $css = '',
        bool $skip_count = false,
        bool $default = false
    ) {
        $this->query = $query;
        $this->label = $label;
        $this->title = $title;
        $this->css = $css;
        $this->skip_count = $skip_count;
        $this->default = $default;
    }

    public function count()
    {
        $this->count = $this->query->count();
        return $this->count;
    }

    public function volatable()
    {
        return [
            'label' => $this->label,
            'title' => $this->title,
            'css' => $this->css,
            'count' => $this->skip_count ? 0 : $this->count(),
            'default' => $this->default,
        ];
    }
}
