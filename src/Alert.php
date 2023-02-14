<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для хранения сообщений об ошибках
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class Alert
{
    public function __construct(public int $status = -1, public string $message = '')
    {
    }

    public function toHtml(): string
    {
        if ($this->status === -1) return '';

        $view = match ($this->status) {
            0 => 'alert-warning',
            default => 'alert-danger'
        };

        $template = new Template(
            template: $view,
            args: [
                'title' => getenv('APP_TITLE'),
                'message' => $this->message,
                'nonce' => getenv('APP_NONCE')
            ]
        );

        return $template->render();
    }
}
