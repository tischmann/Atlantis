<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Внешний ключ
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Foreign
{
    /**
     * 
     */
    public function __construct(
        public string $table,
        public string $column,
        public string $update = 'RESTRICT',
        public string $delete = 'RESTRICT',
    ) {
        $this->update = strtoupper($this->update);
        $this->delete = strtoupper($this->delete);
    }
}
