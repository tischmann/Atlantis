<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class View
{
    protected Template $template;

    public function __construct(public string $view, public array $args = [])
    {
        $alert = Session::get('alert');

        if ($alert) Session::delete('alert');

        $this->template = new Template(
            $view,
            [
                'alert' => $alert ?: new Alert(),
                'breadcrumbs' => [],
                ...$args
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
