<?php

declare(strict_types=1);

use App\Controllers\{
    UsersController
};

use Tischmann\Atlantis\{
    App,
    Router,
    Route
};

if (App::getUser()->exists()) {
    Router::add(new Route(
        controller: new UsersController(),
        path: 'signout',
        action: 'signout',
        method: 'GET'
    ));
}
