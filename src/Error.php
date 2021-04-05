<?php

namespace Atlantis;

/**
 * Error class
 *
 * @var int $status Status (default: 0)
 * @var string $title Title
 * @var string $message Message
 * @var string $type success | danger | warning | info | light | primary | secondary | dark
 */
final class Error
{
    public int $status;
    public string $title;
    public string $message;
    public string $type;

    public function __construct(
        string $title = '',
        string $message = '',
        int $status = 0,
        string $type = 'light'
    ) {
        $this->title = $title ?: lang('warning');
        $this->message = $message;
        $this->status = $status;
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

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function html(): string
    {
        return View::render('error', ['error' => $this]);
    }

    public function json()
    {
        return $this;
    }

    public function __toString(): string
    {
        return $this->html();
    }
}
