<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use DateTimeZone;

/**
 * Класс для работы с датами
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Date extends DateTime
{
    public function __construct(
        string $datetime = "now",
        ?DateTimeZone $timezone = null
    ) {
        parent::__construct($datetime, $timezone);
        $this->setTime(0, 0, 0);
    }

    public function __toString()
    {
        return $this->format("Y-m-d");
    }
}
