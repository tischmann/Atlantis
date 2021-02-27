<?php

namespace Atlantis;

/**
 * Error class
 *
 * @var int $code Code (default: 0)
 * @var string $title Title
 * @var string $message Message
 * @var string $type success | danger | warning | info | light | primary | secondary | dark
 */
final class Error
{
    public int $code;
    public string $title;
    public string $message;
    public string $type;

    public function __construct(
        string $title = '',
        string $message = '',
        int $code = 0,
        string $type = 'light'
    ) {
        $this->title = $title ?: App::$lang->get('warning');
        $this->message = $message;
        $this->code = $code;
        $this->type = $type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
