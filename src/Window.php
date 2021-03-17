<?php

namespace Atlantis;

class Window
{
    public string $type;
    public Model $model;

    public function __construct(string $type, Model $model)
    {
        $this->type = $type;
        $this->model = $model;
    }
}
