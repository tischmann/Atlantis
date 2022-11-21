<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Exceptions;

use Tischmann\Atlantis\{Locale, View};

use Exception;

final class RouteNotFoundException extends Exception
{
    public function __construct()
    {
        putenv("APP_TITLE=" . Locale::get('error_route_not_found'));

        $view = new View(
            'errors/404',
            ['message' => Locale::get('error_route_not_found')]
        );

        echo $view->render();

        exit;
    }
}
