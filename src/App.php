<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;

final class App
{
    protected static ?User $user = null;

    public static string $title = '';

    public static function getCurrentUser(): User
    {
        static::$user ??= User::current();
        return static::$user;
    }

    public static function getResourcesDir(): string
    {
        if (static::getCurrentUser()->isAdmin()) {
            return __DIR__ . '/../resources/js';
        }

        return __DIR__ . "/../public/js";
    }

    public static function getJsResource(string $name)
    {
        include static::getResourcesDir() . "/{$name}.js";
    }

    public static function getCssResource(string $name)
    {
        include static::getResourcesDir() . "/{$name}.css";
    }

    public static function getTitle(): string
    {
        return static::$title;
    }

    public static function setTitle(string $title): string
    {
        return (static::$title = $title);
    }
}
