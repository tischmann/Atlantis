<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Throwable;

/**
 * Класс для хранения сообщений об ошибках
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Alert
{
    public function __construct(
        public int $status = -1,
        public string $message = '',
        public string $html = ''
    ) {
    }

    public function toHtml(): string
    {
        if ($this->status === -1) return '';

        $template = new Template(
            template: 'alert',
            args: [
                'title' => Locale::get('warning'),
                'message' => $this->message,
                'html' => $this->html
            ]
        );

        return $template->render();
    }

    public static function exceptionHandler(Throwable $exception)
    {
        $trace = $exception->getTrace();

        $traceHtml = '';

        if ($trace && boolval(getenv('ADD_DEBUG'))) {
            $items = '';

            foreach ($exception->getTrace() as $key => $item) {
                $items .= Template::html('trace-item', [
                    'id' => $key,
                    'file' => $item['file'],
                    'line' => $item['line'],
                    'function' => $item['function'],
                ]);
            }

            $traceHtml .= Template::html('trace', ['items' => $items]);
        }

        $accept = Request::accept();

        $response = match ($accept) {
            'html' => View::make(
                'error',
                [
                    'message' => $exception->getMessage(),
                    'trace' => $traceHtml
                ]
            )->render(),
            'text' => $exception->getMessage()
                . ". Trace: " . json_encode($trace, 32 | 256),
            default => [
                'status' => 0,
                'message' => $exception->getMessage(),
                'trace' => $trace
            ]
        };

        Response::send($response);
    }

    public static function errorHandler(
        int $errno,
        string $errstr,
        string $errfile = '',
        int $errline = 0
    ) {
        Response::send(
            View::make(
                'error',
                [
                    'message' => "[{$errno}]: {$errstr} in {$errfile} at line {$errline}",
                    'trace' => ''
                ]
            )->render()
        );
    }
}
