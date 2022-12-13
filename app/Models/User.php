<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\{Users};

use Exception;

use Tischmann\Atlantis\{
    BeforeValidException,
    Cookie,
    JWT,
    Migration,
    Model,
    Request,
    Session,
    SignatureInvalidException,
    TokenExpiredException
};

class User extends Model
{
    public const ROLE_ADMIN = 'admin';

    public const ROLE_USER = 'user';

    public const ROLE_GUEST = 'guest';

    public const JWT_ALGORITHM = 'RS256';

    public const JWT_EXPIRES = 600;

    private string $privateKey;

    private string $publicKey;

    private string $token = '';

    private static User $user;

    public function __construct(
        public string $login = '',
        public string $password = '',
        public string $role = '',
        public string $refresh_token = '',
    ) {
        parent::__construct();

        $this->role = $this->role ?: self::ROLE_GUEST;

        $privatekey = file_get_contents(__DIR__ . "/../../private.pem");

        if (!$privatekey) throw new Exception('Private key not found');

        $this->privateKey = $privatekey;

        $publicKey = file_get_contents(__DIR__ . "/../../public.pem");

        if (!$publicKey) throw new Exception('Public key not found');

        $this->publicKey = $publicKey;
    }

    public static function table(): Migration
    {
        return new Users();
    }

    public static function current(): self
    {
        if (!isset(static::$user)) {
            static::$user ??= new static();
            static::$user->authorize();
        }

        return static::$user;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Авторизация пользователя
     *
     * @throws Exception Ошибка авторизации
     * @return self 
     */
    public function authorize(): self
    {
        if (!static::isLastAccessValid()) {
            Session::destroy();
            Session::start();
        }

        if (!static::isClientUserAgentValid()) {
            Session::destroy();
            Session::start();
        }

        if (!static::isClientAddressValid()) {
            Session::destroy();
            Session::start();
        }

        $jwt = Request::authorization() ?: Cookie::get('jwt');

        $jwr = Cookie::get('jwr');

        if (!$jwt || !$jwr) return $this->signOut();

        try {
            $decoded = $this->decodeJWT($jwt);
        } catch (TokenExpiredException $e) {
            $jwt = $this->refreshJWT($jwt, $jwr);

            $decoded = $this->decodeJWT($jwt);

            $this->token = $jwt;

            $this->refresh_token = $jwr;

            $expires = intval(Cookie::get('remember'));

            Cookie::set('jwt', $jwt, ['expires' => $expires]);
        } catch (SignatureInvalidException $e) {
            throw new Exception($e->getMessage());
        } catch (BeforeValidException $e) {
            throw new Exception($e->getMessage());
        }

        if (!$this->exists()) {
            $query = static::query()
                ->where('id', $decoded->data->id)
                ->where('login', $decoded->data->login)
                ->where('role', $decoded->data->role)
                ->limit(1);

            $this->__fill($query->first());
        }

        Session::set('LAST_ACCESS', time());

        return $this;
    }

    /**
     * Преобразование токена в объект
     * 
     * @param string $token Токен
     * @return object Объект токена
     */
    protected function decodeJWT(string $token): object
    {
        return JWT::decode($token, $this->publicKey, [self::JWT_ALGORITHM]);
    }

    /**
     * Обновление токена
     * 
     * @param string $token Токен
     * @param string $refresh_token Токен обновления
     * @return string Обновленный токен
     * @throws RefreshTokenExpiredException
     */
    protected function refreshJWT(string $token, string $refresh_token): string
    {
        $payload = $this->getJWTPayload($token)?->data ?? null;

        $payload ??= $this->getJWTData();

        $user = self::find($payload?->id ?? 0);

        if ($user->refresh_token !== $refresh_token || !$refresh_token) {
            $this->signOut();
            throw new TokenExpiredException();
        }

        return $this->getJWToken($payload);
    }

    /**
     * Получение JWT токена
     * 
     * @param object $payload Объект токена
     * @return string Токен
     */
    protected function getJWToken(object $payload): string
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + static::JWT_EXPIRES,
            "iss" => $_SERVER['HTTP_HOST'],
            "data" => $payload
        ];

        return JWT::encode($payload, $this->privateKey, static::JWT_ALGORITHM);
    }

    /**
     * Получение данных объекта JWT токена
     * 
     * @return object Данные объекта токена
     */
    protected function getJWTData(): object
    {
        return (object) [
            'id' => $this->id,
            'role' => $this->role,
            'login' => $this->login
        ];
    }

    /**
     * Получение объекта JWT токена из токена
     * 
     * @param string $token Токен
     * @return object Объект токена
     * @throws Exception
     */
    protected function getJWTPayload(string $token): object
    {
        $segments = explode('.', $token);

        if (count($segments) !== 3) {
            throw new Exception("Wrong number of segments");
        }

        $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($segments[1]));

        if ($payload === null) throw new Exception("Invalid payload");

        return $payload;
    }

    /**
     * Генерация токена обновления
     * 
     * @return string Токен обновления
     */
    public static function generateJWR(): string
    {
        return bin2hex(random_bytes(128));
    }

    /**
     * Вход пользователя в систему
     *
     * @param User $user Пользователь
     * 
     * @return self
     */
    public function signIn(): self
    {
        if ($this->exists()) {
            session_regenerate_id();

            $this->refresh_token = $this->refresh_token ?: static::generateJWR();

            $this->token = $this->getJWToken($this->getJWTData());

            $expires = intval(Cookie::get('remember'));

            Cookie::set('jwr', $this->refresh_token, ['expires' => $expires]);

            Cookie::set('jwt', $this->token, ['expires' => $expires]);

            Session::set('LAST_ACCESS', time());

            Session::set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

            Session::set('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);

            $this->save();
        }

        return $this;
    }

    /**
     * Выход пользователя из системы
     *
     * @return self
     */
    public function signOut(): self
    {
        if ($this->exists()) {
            Cookie::delete('jwt');

            Cookie::delete('jwr');

            Cookie::delete('remember');

            $this->token = '';

            $this->refresh_token = '';

            $this->save();
        }

        return $this;
    }

    /**
     * Проверяет IP-адрес клиента
     *
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    public static function isClientAddressValid(): bool
    {
        if (!Session::has('REMOTE_ADDR')) return true;

        return Session::get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Проверяет User-Agent клиента
     *
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    public static function isClientUserAgentValid()
    {
        if (!Session::has('USER_AGENT')) return true;

        return Session::get('USER_AGENT') === $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Проверяет время последнего доступа к системе
     * 
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    public static function isLastAccessValid(int $seconds = 1440): bool
    {
        if (!Session::has('LAST_ACCESS')) return true;

        return time() < intval(Session::get('LAST_ACCESS')) + $seconds;
    }
}
