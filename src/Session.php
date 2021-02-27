<?php

namespace Atlantis;

final class Session
{
    public static function start()
    {
        $request = new Request();

        if ($request::cookie('PHPSESSID')) {
            session_id($request::cookie('PHPSESSID'));
        }

        session_start();

        Session::cookie('PHPSESSID', session_id());

        if (getenv('APP_DEBUG') ?: false) {
            Session::cookie('XDEBUG_SESSION', 'VSCODE');
        } else {
            setcookie('XDEBUG_SESSION', null);
        }
    }

    public static function cookie(string $key, $value)
    {
        setcookie($key, $value, time() + 60 * 60 * 24 * 14, '/', '', 0, 1);
    }

    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
        return static::get($key);
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function del(string $key)
    {
        unset($_SESSION[$key]);
    }

    public static function regenerate()
    {
        session_regenerate_id();
        Session::cookie('PHPSESSID', session_id());
    }

    public static function destroy()
    {
        Session::cookie('PHPSESSID', null);
        session_destroy();
    }
}
