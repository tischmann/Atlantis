<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс сортировщика 
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Sorting
{
    /**
     * Конструктор
     * 
     * @param string $type Тип сортировки (имя столбца в таблице)
     * @param string $order Порядок сортировки (ASC, DESC)
     * 
     */
    public function __construct(
        public string $type = '',
        public string $order = ''
    ) {
    }
}
