<?php

namespace Atlantis\Controllers;

use Atlantis\{CSRF, Response, View};

class Controller
{
    public function render(string $view, array $args = []): string
    {
        if (!in_array($_SERVER['REQUEST_METHOD'], ['GET'])) {
            CSRF::check();
        }

        return Response::response(View::render($view, $args));
    }
}
