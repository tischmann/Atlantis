<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router};

require_once "require.php";

require_once "config.php";

require_once __DIR__ . '/../vendor/autoload.php';

require_once "helpers.php";

session_init(name: 'PHPSESSID', id: cookies_get('PHPSESSID'));

cookies_set('PHPSESSID', session_id());

require_once "routes.php";

Router::resolve();
