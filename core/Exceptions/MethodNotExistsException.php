<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Exceptions;

use Tischmann\Atlantis\{Exception, Locale};

final class MethodNotExistsException extends Exception
{
    public function __construct(object $class, string $method)
    {
        $this->message = Locale::get('error_method_not_exists')
            . " - " . get_class($class) . "()->{$method}()";
    }
}
