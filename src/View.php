<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

final class View
{
    protected Template $templateInstance;

    public function __construct(
        public string $template,
        public array $args = [],
        string $layout = 'default'
    ) {
        $alert = Session::get('alert') ?? new Alert();

        Session::delete('alert');

        if (!is_file(__DIR__ . "/../app/Views/layouts/{$layout}.tpl")) {
            die("Layout {$layout} not found");
        }

        $this->templateInstance = new Template(
            "layouts/{$layout}",
            [
                ...$this->args,
                'alert' => $alert->toHtml(),
                'body' => Template::html($this->template, $this->args),
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
        return $this->templateInstance->render();
    }
}
