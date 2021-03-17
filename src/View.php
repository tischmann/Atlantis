<?php

namespace Atlantis;

class View
{
    static function render(string $view, array $args = []): string
    {
        if (file_exists("../views/{$view}.php")) {
            ob_start(!in_array('ob_gzhandler', ob_list_handlers()) ? 'ob_gzhandler' : null);
            extract($args);
            require "../views/{$view}.php";
            return ob_get_clean();
        } else if (file_exists("../views/{$view}.tpl.php")) {
            $tpl = new Template($view, $args);
            return $tpl->render();
        } else {
            Response::response(new Error(
                status: 401,
                message: App::$lang->get('error_view_not_found') . ": $view",
                type: 'warning'
            ));
        }
    }
}
