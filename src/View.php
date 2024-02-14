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

        assert($alert instanceof Alert);

        Session::delete('alert');

        $file = __DIR__ . "/../app/Views/layouts/{$layout}.tpl";

        if (!is_file($file)) {
            $file = __DIR__ . "/../app/Views/layouts/{$layout}.php";

            if (!file_exists($file)) {
                die("Layout {$layout} not found");
            }
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
        string $layout = 'default'
    ) {
        return Response::send(static::html($view, $args, $layout));
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
        return $this->templateInstance->render();
    }
}
