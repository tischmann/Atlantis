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
        $alert = Session::get('alert') ?? new Alert();

        Session::delete('alert');

        $this->template = new Template(
            "layouts/{$layout}",
            [
                ...$args,
                'alert' => $alert->toHtml(),
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
