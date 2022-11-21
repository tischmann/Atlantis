<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\{Error, Response};

use Throwable;

/**
 * Класс для обработки исключений
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Exception extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Обработчик исключений
     */
    public static function handler(Throwable $exception)
    {
        Response::echo(new Error(
            message: $exception->getMessage(),
            trace: $exception->getTrace()
        ));
    }
}
