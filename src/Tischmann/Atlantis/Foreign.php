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
     * Внешний ключ
     * 
     * @param string $table Таблица
     * @param string $column Колонка
     * @param string $update Действие при обновлении (CASCADE, SET NULL, RESTRICT)
     * @param string $delete Действие при удалении (CASCADE, SET NULL, RESTRICT)
     */
    public function __construct(
        public string $table,
        public string $column,
        public string $update = 'RESTRICT',
        public string $delete = 'RESTRICT',
    ) {
        $this->update = match (strtoupper($this->update)) {
            'CASCADE' => 'CASCADE',
            'SET NULL' => 'SET NULL',
            default => 'RESTRICT',
        };

        $this->delete = match (strtoupper($this->delete)) {
            'CASCADE' => 'CASCADE',
            'SET NULL' => 'SET NULL',
            default => 'RESTRICT',
        };
    }
}
