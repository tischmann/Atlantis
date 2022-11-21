<?php

declare(strict_types=1);

namespace Tischmann\Atlantis\Exceptions;

use Tischmann\Atlantis\{Exception, Locale, View};

final class CSRFAttackException extends Exception
{
    public function __construct()
    {
        putenv("APP_TITLE=" . Locale::get('error_csrf_attack'));

        $view = View::make(
            'errors/csrf',
            ['message' => Locale::get('error_csrf_attack')]
        );

        die($view->render());
    }
}
