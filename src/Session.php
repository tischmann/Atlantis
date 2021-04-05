<?php

namespace Atlantis;

final class Session
{
    public static function start()
    {
        if (Request::cookie('PHPSESSID')) {
            session_id(Request::cookie('PHPSESSID'));
        }

        session_start();

        if (getenv('APP_DEBUG') ?: false) {
            Session::cookie('XDEBUG_SESSION', 'VSCODE');
        } else {
            Session::cookie('XDEBUG_SESSION', null);

            // Does IP Address match
            if ($_SERVER['REMOTE_ADDR'] != Session::get('REMOTE_ADDR')) {
                Session::destroy();
            }

            // Does user agent match
            if (sha1($_SERVER['HTTP_USER_AGENT']) != Session::get('HTTP_USER_AGENT')) {
                Session::destroy();
            }

            // Is the last access over an hour ago
            if (time() > (int)Session::get('LAST_ACCESS') + 3600) {
                Session::destroy();
            } else {
                Session::set('LAST_ACCESS', time());
            }
        }

        Session::set('SCRIPT_NONCE', bin2hex(random_bytes(16)));

        Session::cookie('PHPSESSID', session_id());
    }

    public static function cookie(string $key, $value)
    {
        setcookie(
            $key,
            $value,
            [
                'expires' => time() + 60 * 60 * 24 * 14,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );
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
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id();
    }
}
