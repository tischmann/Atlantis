<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Exceptions;

use PDOException;

use Tischmann\Atlantis\Exception;

/**
 * Исключение выбрасываемое при ошибке выполнения запроса к базе данных
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class DatabaseException extends PDOException
{
    public function __construct(string $message = "")
    {
        $this->message = $message;

        Exception::handler($this);
    }
}
