<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Interfaces;

/**
 * Интерфейс для ответа
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
interface Response
{
    /**
     * Возвращает ответ в виде HTML строки
     *
     * @return string
     */
    public function html(): string;

    /**
     * Возвращает ответ в виде JSON строки
     *
     * @return string
     */
    public function json(): string;

    /**
     * Возвращает ответ в виде простой строки
     *
     * @return string
     */
    public function text(): string;
}
