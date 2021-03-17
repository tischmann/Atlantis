<?php

namespace Atlantis;

use Atlantis\Models\{User};

class Auth
{
    public static function auth()
    {
        $id = Session::get('USER_ID');

        if ($id) {
            User::current()->__construct($id);
        }
    }

    public static function generatePassword(
        int $length = 8,
        bool $dashes = false,
        string $setsAvailable = 'lud'
    ): string {
        $sets = array();

        if (strpos($setsAvailable, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }

        if (strpos($setsAvailable, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }

        if (strpos($setsAvailable, 'd') !== false) {
            $sets[] = '23456789';
        }

        if (strpos($setsAvailable, 's') !== false) {
            $sets[] = '!@#$%&*?';
        }

        $all = '';
        $password = '';

        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);

        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        if (!$dashes) return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';

        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }

        $dash_str .= $password;

        return $dash_str;
    }

    public static function signIn(
        string $login,
        string $password
    ): bool {
        $fetchedUser = User::where('login', $login)->first();

        if (!$fetchedUser) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_login'),
                type: 'warning'
            ));
        }

        if (!self::checkHash($password, $fetchedUser->password)) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_password'),
                type: 'warning'
            ));
        }

        User::current()->__construct($fetchedUser->id);

        Session::regenerate();
        Session::set('USER_ID', User::current()->id);

        return true;
    }

    public static function signedIn(): bool
    {
        return !!User::current()->id;
    }

    public static function signOut(): bool
    {
        User::current()->reset();
        Session::destroy();
        return true;
    }

    public static function isAdmin(): bool
    {
        return User::current()->role == 1;
    }

    function isLoginValid(string $login)
    {
        if (!preg_match('/[a-zA-Z0-9]+/i', $login)) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_login'),
                type: 'warning'
            ));
        }

        return true;
    }

    function isEmailValid(string $email)
    {
        if (!$email) {
            return true;
        }

        if (!preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9-]+.+.[A-Z]{2,4}$/i', $email)) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('bad_email'),
                type: 'warning'
            ));
        }

        return true;
    }

    function isPasswordValid(string $password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $minLength = 8;

        if (!$uppercase || !$lowercase || !$number || strlen($password) < $minLength) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('password_weak'),
                type: 'warning'
            ));
        }

        return true;
    }

    function isExists(): bool
    {
        if (User::where('login', $this->login)->exists()) {
            Response::response(new Error(
                title: App::$lang->get('warning'),
                message: App::$lang->get('user_exists'),
                type: 'warning'
            ));
        }

        return false;
    }

    static function getHash(string $string): string
    {
        return password_hash($string, PASSWORD_DEFAULT);
    }

    static function checkHash(string $string, $hash): bool
    {
        return password_verify($string, $hash);
    }

    public static function canSelect(string $table, string $column = null): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        return false;
    }

    public static function canUpdate(string $table, string $column = null): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        return false;
    }

    public static function canDelete(string $table, string $column = null): bool
    {
        if (self::isAdmin()) {
            return true;
        }

        return false;
    }
}
