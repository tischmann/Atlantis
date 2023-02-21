<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router, Cookie, Session};

require_once "require.php";

require_once "config.php";

require_once __DIR__ . '/../vendor/autoload.php';

set_exception_handler('Tischmann\Atlantis\Alert::exceptionHandler');

set_error_handler('Tischmann\Atlantis\Alert::errorHandler');

Session::start(name: 'PHPSESSID', id: Cookie::get('PHPSESSID'));

Cookie::set('PHPSESSID', session_id());

Cookie::set('XDEBUG_SESSION', 'VSCODE');

require_once "routes.php";

Router::bootstrap();
