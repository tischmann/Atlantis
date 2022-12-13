<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Исключение выбрасывоемое если подпись токена не верна
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class SignatureInvalidException extends Exception
{
    public function __construct()
    {
        parent::__construct('Signature invalid!', 401);
    }
}
