<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router, Route};

// Для всех методов
Router::add(new Route());

Router::add(new Route(
    path: 'signin',
    method: 'POST',
    action: 'signIn',
));

Router::add(new Route(
    path: 'signout',
    action: 'signOut',
));
