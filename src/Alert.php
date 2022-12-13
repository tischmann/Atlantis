<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для хранения сообщений об ошибках
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Alert
{
    public function __construct(public int $status = 0, public string $message = '')
    {
    }
}
