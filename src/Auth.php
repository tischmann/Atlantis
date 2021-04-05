<?php

namespace Atlantis;

use Atlantis\Models\{User};
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class Auth
{
    private static string $privateKey = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;

    public static string $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;

    public static string $tokenAlgorithm = 'RS256';
    public static int $tokenExpire = 60 * 10;

    public static function auth()
    {
        $jwt = Request::bearer() ?? Request::cookie('jwt_token');
        $jwr = Request::cookie('jwr_token');
        $userId = Session::get('user_id');
        $userLogin = Session::get('user_login');

        if ($jwt && $userId && $userLogin && $jwr) {
            try {
                $decoded = self::decodeJWT($jwt);
            } catch (ExpiredException $e) {
                $jwt = self::refreshJWT($userId, $jwr);
                $decoded = self::decodeJWT($jwt);
            }

            if (
                $userId == $decoded->data->user_id &&
                $userLogin == $decoded->data->user_login
            ) {
                User::current()->__construct($userId);
                Session::cookie('jwt_token', $jwt);
                Session::cookie('jwr_token', $jwr);
            } else {
                Session::cookie('jwt_token', null);
                Session::cookie('jwr_token', null);
                Session::destroy();
            }
        }
    }

    public static function getJWT($data)
    {
        $issued = time();

        $token = [
            "iat" => $issued,
            "exp" => $issued + self::$tokenExpire,
            "iss" => $_SERVER['HTTP_HOST'],
            "data" => $data
        ];

        return JWT::encode($token, self::$privateKey, self::$tokenAlgorithm);
    }

    public static function getJWR()
    {
        return bin2hex(random_bytes(512));
    }

    public static function fetchJWR(int $userId): string|null
    {
        $result = User::where('id', $userId)
            ->pluck('refresh_token');

        foreach ($result as $jwr) {
            return $jwr;
        }

        return null;
    }

    public static function setJWR(string $token, int $userId): string
    {
        User::where('id', $userId)
            ->update(['refresh_token' => $token]);
        return $token;
    }

    public static function refreshJWT(int $userId, string $jwr)
    {
        if (self::fetchJWR($userId) !== $jwr) {
            self::deleteJWR($userId);
            Session::cookie('jwt_token', null);
            Session::cookie('jwr_token', null);
            Session::destroy();
            Response::response(new Error(
                title: lang('warning'),
                message: lang('refresh_token_expired'),
                type: 'danger'
            ));
        }

        $user = new User($userId);

        return self::issueJWT([
            'user_id' => $user->id,
            'user_login' => $user->login,
        ]);
    }

    public static function deleteJWR(int $userId): bool
    {
        return User::where('id', $userId)
            ->update(['refresh_token' => null]);
    }

    public static function issueJWT(array $data)
    {
        return self::getJWT($data);
    }

    public static function issueJWR(int $userId)
    {
        $jwr = self::fetchJWR($userId);

        if (!$jwr) {
            $jwr = self::setJWR(self::getJWR(), $userId);
        }

        return $jwr;
    }

    public static function decodeJWT(string $token)
    {
        return JWT::decode($token, self::$publicKey, [self::$tokenAlgorithm]);
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
        $fetchedUser = User::orWhere('login', $login)
            ->orWhere('email', $login)
            ->first();

        if (!$fetchedUser) {
            Response::response(new Error(
                title: lang('warning'),
                message: lang('bad_login'),
                type: 'warning'
            ));
        }

        if (!self::checkHash($password, $fetchedUser->password)) {
            Response::response(new Error(
                title: lang('warning'),
                message: lang('bad_password'),
                type: 'warning'
            ));
        }

        User::current()->__construct($fetchedUser->id);

        Session::regenerate();
        Session::set('user_id', User::current()->id);
        Session::set('user_login', User::current()->login);
        Session::cookie('jwt_token', self::issueJWT([
            'user_id' => User::current()->id,
            'user_login' => User::current()->login,
        ]));
        Session::cookie('jwr_token', self::issueJWR(User::current()->id));
        Session::set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
        Session::set('HTTP_USER_AGENT', sha1($_SERVER['HTTP_USER_AGENT']));
        Session::set('LAST_ACCESS', time());

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
        Session::cookie('jwt_token', null);
        Session::cookie('jwr_token', null);
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
                title: lang('warning'),
                message: lang('bad_login'),
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
                title: lang('warning'),
                message: lang('bad_email'),
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
                title: lang('warning'),
                message: lang('password_weak'),
                type: 'warning'
            ));
        }

        return true;
    }

    function isExists(): bool
    {
        if (User::where('login', $this->login)->exists()) {
            Response::response(new Error(
                title: lang('warning'),
                message: lang('user_exists'),
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
