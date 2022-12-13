<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Исключение выбрасываемое если дата раньше действующего периода
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class BeforeValidException extends Exception
{
}
