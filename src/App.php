<?php

namespace Atlantis;

class App
{
    public static Router $router;
    public static Language $lang;
    public static Database $db;

    public static function run()
    {
        die(static::$router->resolve()->action());
    }
}
