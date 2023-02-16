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
                'title' => getenv('APP_TITLE'),
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

        if ($trace && boolval(getenv('APP_DEBUG'))) {
            $items = '';

            foreach ($exception->getTrace() as $key => $item) {
                $items .= Template::make('trace-item', [
                    'id' => $key,
                    'file' => $item['file'],
                    'line' => $item['line'],
                    'function' => $item['function'],
                ])->render();
            }

            $traceHtml .= Template::make('trace', ['items' => $items])
                ->render();
        }

        $accept = Request::accept();

        $response = [
            'message' => $exception->getMessage(),
            'trace' => $trace
        ];

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
                view: 'error',
                args: [
                    'message' => "[{$errno}]: {$errstr} in {$errfile} at line {$errline}",
                    'trace' => ''
                ]
            )->render()
        );
    }
}
