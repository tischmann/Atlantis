<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Представление
 */
final class View
{
    protected Template $template_instance;

    public function __construct(
        public string $template,
        public array $args = [],
        public string $layout = 'default'
    ) {
        $this->template_instance = new Template(
            template: "layouts/{$this->layout}",
            args: [
                ...$this->args,
                'body' => Template::html(
                    template: $this->template,
                    args: $this->args
                ),
            ]
        );
    }

    public static function make(
        string $view,
        array $args = [],
        string $layout = 'default'
    ): View {
        return new static($view, $args, $layout);
    }

    public static function html(
        string $view,
        array $args = [],
        string $layout = 'default'
    ): string {
        return static::make($view, $args, $layout)->render();
    }

    public static function send(
        string $view,
        array $args = [],
        string $layout = 'default',
        bool $exit = false
    ) {
        Response::send(static::html($view, $args, $layout));

        if ($exit) exit;
    }

    public static function echo(
        string $view,
        array $args = [],
        string $layout = 'default',
        bool $exit = false
    ) {
        echo static::make($view, $args, $layout)->render();

        if ($exit) exit;
    }

    public function render(): string
    {
        return $this->template_instance->render();
    }
}
