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
                'message' => $this->message,
                'html' => $this->html
            ]
        );

        return $template->render();
    }
}
