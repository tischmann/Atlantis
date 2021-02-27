<?php

namespace Atlantis;

use Atlantis\Models\{User};

final class App
{
    public static Kernel $kernel;
    public static Router $router;
    public static Language $lang;
    public static Error $error;
    public static Database $db;
    public static User $user;

    public static function run()
    {
        static::$lang = new Language(getenv('APP_LANG') ?: 'ru');
        static::$error = new Error();
        static::$db = new Database(
            name: getenv('DB_NAME') ?: '',
            user: getenv('DB_USER') ?: '',
            pass: getenv('DB_PASS') ?: '',
        );

        Session::start();

        static::$user = new User();

        Auth::auth();

        static::$router = new Router();
        static::$router->init();

        static::$kernel = new Kernel();
        static::$kernel->launch();
    }

    public static function hasErrors(): bool
    {
        return (bool) static::$error->getMessage();
    }
}
