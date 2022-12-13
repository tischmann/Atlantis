<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Исключение выбрасываемое при отсутсвии доступа
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class AccessDeniedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Access denied!', 404);
    }
}
