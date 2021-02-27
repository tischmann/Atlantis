<?php

namespace Atlantis;

use Atlantis\Models\{User};

class Auth
{
    public static function auth()
    {
        $request = new Request();
        $hash = $request->cookie('HASH') ?? false;

        if (Session::get('HASH') === $hash) {
            App::$user->__construct(Session::get('USER_ID'));
        } else {
            Session::get('HASH');
            Session::get('USER_ID');
        }
    }

    public static function signIn(string $login, string $password): bool
    {
        $fetchedUser = User::where('login', $login)->first();

        if (!$fetchedUser) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_login'),
                type: 'warning'
            );
            return false;
        }

        if (!self::checkHash($password, $fetchedUser->password)) {
            App::$error = new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_password'),
                type: 'warning'
            );
            return false;
        }

        App::$user->__construct($fetchedUser->id);

        $hash = bin2hex(random_bytes(64));

        Session::regenerate();
        Session::set('USER_ID', App::$user->id);
        Session::cookie('HASH', $hash);
        Session::set('HASH', $hash);

        return true;
    }

    public static function signedIn(): bool
    {
        return !!App::$user->id;
    }

    public static function signOut(): bool
    {
        App::$user = new User();
        Session::destroy();
        return true;
    }

    public static function isAdmin(): bool
    {
        return App::$user->role == 1;
    }

    static function getHash(string $string): string
    {
        return password_hash($string, PASSWORD_DEFAULT);
    }

    static function checkHash(string $string, $hash): bool
    {
        return password_verify($string, $hash);
    }

    public static function canSelect(string $table): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        return false;
    }
}
