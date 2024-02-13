<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

use Throwable;

final class AccessDeniedException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(Locale::get('access_denied'), 404);
    }
}
