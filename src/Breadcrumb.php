<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Хлебная крошка
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Breadcrumb
{
    public function __construct(
        public string $label,
        public string $url = '',
    ) {
    }
}
