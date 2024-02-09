<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;

final class App
{
    private static ?string $resourceSuffix = null;

    public static function getResourceSuffix(): string
    {
        static::$resourceSuffix ??= User::current()->isAdmin() ? '' : ".min";

        return static::$resourceSuffix;
    }
}
