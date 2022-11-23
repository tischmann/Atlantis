<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы с временем
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Time extends DateTime
{
    public function __toString()
    {
        return $this->format("H:i:s");
    }
}
