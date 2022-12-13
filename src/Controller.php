<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;
use BadMethodCallException;

class Controller
{
    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException("Method {$name} not found", 404);
    }

    protected function checkAdmin(): void
    {
        if (User::current()->role !== User::ROLE_ADMIN) {
            throw new AccessDeniedException();
        }
    }

    protected function getAlert(bool $wipe = true): ?Alert
    {
        if (Session::has('alert')) {
            $alert = Session::get('alert');
            if ($wipe) Session::delete('alert');
            return $alert;
        }

        return new Alert(status: -1, message: 'Everything is fine');
    }
}
