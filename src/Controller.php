<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use BadMethodCallException;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException("Method {$name} not found", 404);
    }
}
