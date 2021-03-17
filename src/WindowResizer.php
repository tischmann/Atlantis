<?php

namespace Atlantis;

use Atlantis\Controllers\Controller;

class WindowResizer
{
    public string $type;
    public string $axis;

    public function __construct(string $type, string $axis)
    {
        $this->type = $type;
        $this->axis = $axis;
    }
}
