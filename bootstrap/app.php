<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router, Session};

require_once "require.php";

require_once "config.php";

require_once __DIR__ . '/../vendor/autoload.php';

require_once "helpers.php";

Session::start(name: 'PHPSESSID', id: cookies_get('PHPSESSID'));

cookies_set('PHPSESSID', session_id());

cookies_set('DEV_MODE', 1); // Установка куки для режима разработки

require_once "routes.php";

Router::bootstrap();
