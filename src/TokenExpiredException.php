<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Исключение выбрасывоемое если токен просрочен
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class TokenExpiredException extends Exception
{
}
