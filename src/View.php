<?php

namespace Atlantis;

class View
{
    static function include(string $view, array $args = []): string
    {
        $path = "../views/{$view}.php";

        if (!file_exists($path)) {
            App::$error = new Error(
                message: "View {$view} not found",
                type: 'warning'
            );
            return '';
        }

        ob_start(!in_array('ob_gzhandler', ob_list_handlers()) ? 'ob_gzhandler' : null);

        extract($args);

        require $path;

        return ob_get_clean();
    }
}
