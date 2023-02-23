<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\{User};

use BadMethodCallException;

use Exception;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException(
            Locale::get('method_not_found') . ": {$name}",
            404
        );
    }

    public static function setTitle(string $title): void
    {
        putenv('APP_TITLE=' . getenv('APP_TITLE') . " - " . $title);
    }

    protected function checkAdmin(): void
    {
        if (!User::current()->isAdmin()) {
            throw new Exception(Locale::get('access_denied'), 404);
        }
    }
}
