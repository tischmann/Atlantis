<?php

namespace Atlantis\Controllers;

use Atlantis\{App, CSRF, Template, View};

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
        if (preg_match('/application\/json/i', $_SERVER['HTTP_ACCEPT'])) {
            header("Content-Type: application/json; charset=UTF-8");

            if (App::hasErrors()) {
                return json_encode(App::$error);
            }

            return json_encode(View::include($view, $args));
        } else {
            header("Content-Type: text/html; charset=UTF-8");
            $template = new Template("Core/Layouts/{$this->layout}");
            $template->set('header', View::include('Header'))
                ->set('body', View::include($view, $args))
                ->set('error', App::hasErrors() ? View::include('Error') : '');
            return $template->render();
        }
    }
}
