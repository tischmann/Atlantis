<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class View
{
    protected Template $template;

    public function __construct(public string $view, public array $args = [])
    {
        $this->template = new Template($view, $args);
    }

    public static function make(string $view, array $args = []): View
    {
        return new static($view, $args);
    }

    public function render(): string
    {
        return $this->template->render();
    }
}
