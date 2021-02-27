<?php

namespace Atlantis\Controllers;

use Atlantis\{CSRF, Template, View};

class Controller
{
    public string $layout = 'Default';

    public function __construct()
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET'])) {
            CSRF::check();
        }
    }

    final public function layout(string $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function render(string $view, array $args = []): string
    {
        $template = new Template("Core/Layouts/{$this->layout}");
        $template->set('header', View::include('Header'))
            ->set('body', View::include($view, $args))
            ->set('error', View::include('Error'));
        return $template->render();
    }
}
