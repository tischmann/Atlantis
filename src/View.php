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

    public static function html(string $view, array $args = []): string
    {
        return static::make($view, $args)->render();
    }

    public static function send(string $view, array $args = [])
    {
        return Response::send(static::html($view, $args));
    }

    public static function echo(string $view, array $args = [])
    {
        echo static::make($view, $args)->render();
    }

    public function render(): string
    {
        return $this->template->render();
    }
}
