<?php

declare(strict_types=1);

use App\Controllers\{IndexController};

use Tischmann\Atlantis\{Router, Route};

Router::add(new Route(
    controller: new IndexController(),
));
