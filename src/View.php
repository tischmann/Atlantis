<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class View
{
    protected Template $template;

    public function __construct(
        public string $view,
        public array $args = [],
        string $layout = 'default'
    ) {
        $this->template = new Template(
            "layouts/{$layout}",
            [
                ...$args,
                'body' => Template::make(
                    template: $view,
                    args: $args
                )->render(),
            ]
        );
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
