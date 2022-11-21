<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Tischmann\Atlantis\Interfaces\Response as ResponseInterface;

/**
 * Класс для обработки ошибок
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Error implements ResponseInterface
{
    public function __construct(
        public string $message = '',
        public array $trace = [],
        public ?string $referer  = null,
    ) {
        $this->referer ??= $_SERVER['HTTP_REFERER'] ?? '/';
    }

    /**
     * Обработчик ошибок
     *
     * @param integer $errno  Код ошибки
     * @param string $errstr Сообщение об ошибке
     * @param string $errfile Файл, в котором произошла ошибка
     * @param integer $errline Строка, в которой произошла ошибка
     * @return void
     */
    public static function handler(
        int $errno,
        string $errstr,
        string $errfile = '',
        int $errline = 0
    ): void {
        $message = "[{$errno}]: {$errstr} in {$errfile} at line {$errline}";
        Response::echo(new self(message: $message));
        exit;
    }

    public function html(): string
    {
        $debug = intval(getenv('APP_DEBUG'));

        $this->trace = $this->formatTrace($debug ? $this->trace : []);

        $view = new View(
            view: 'error',
            args: ['error' => $this],
        );

        return $view->render();
    }

    public function text(): string
    {
        return $this->message;
    }

    public function json(): string
    {
        $debug = intval(getenv('APP_DEBUG'));

        return json_encode([
            'message' => $this->message,
            'trace' => $this->formatTrace($debug ? $this->trace : [])
        ], 32 | 128 | 256);
    }

    /**
     * Форматирует трассировку ошибки
     *
     * @param array $trace Трассировка ошибки
     * @return array Массив с отфотматированной трассировкой
     */
    protected function formatTrace(array $trace): array
    {
        return array_map(function ($value) {
            $value['function'] = ($value['class'] ?? '')
                . ($value['type'] ?? '')
                . ($value['function'] ?? '')
                . "()";
            return (object) $value;
        }, $trace);
    }
}
