<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\User;

use Exception;

final class Auth
{
    public const JWT_ALGORITHM = 'RS256';

    public const JWT_EXPIRES = 600;

    protected static ?string $privateKey = null;

    protected static ?string $publicKey = null;

    protected string $token = '';

    public function __construct(public User $user = new User())
    {
    }

    public static function instance(User $user = new User()): self
    {
        return new self($user);
    }

    /**
     * Получение закрытого ключа
     *
     * @return string
     */
    public static function getPrivateKey(): string
    {
        if (!static::$privateKey) {
            if (!is_file(__DIR__ . "/../private.pem")) {
                throw new Exception('Private key not found');
            }

            static::$privateKey = file_get_contents(__DIR__ . "/../private.pem");

            if (!static::$privateKey) throw new Exception('Bad private key');
        }

        return static::$privateKey;
    }

    /**
     * Получение открытого ключа
     *
     * @return string
     */
    public static function getPublicKey(): string
    {
        if (!static::$publicKey) {
            if (!is_file(__DIR__ . "/../public.pem")) {
                throw new Exception('Public key not found');
            }

            static::$publicKey = file_get_contents(__DIR__ . "/../public.pem");

            if (!static::$publicKey) throw new Exception('Bad public key');
        }

        return static::$publicKey;
    }

    /**
     * Авторизация пользователя
     *
     * @throws Exception Ошибка авторизации
     * @return self 
     */
    public function authorize(): User
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

        if (!$jwt || !$jwr) return $this->user;

        try {
            $decoded = $this->decodeToken($jwt);

            $this->user = User::find($decoded->data->id, 'id');
        } catch (TokenExpiredException $e) {
            $jwt = $this->refreshToken($jwt, $jwr);

            $decoded = $this->decodeToken($jwt);

            $this->user = User::find($decoded->data->id, 'id');

            $this->token = $jwt;

            $this->user->refresh_token = $jwr;

            $this->user->save();

            $expires = time() + 2678400;

            Cookie::set('jwt', $jwt, ['expires' => $expires]);
        } catch (SignatureInvalidException $e) {
            View::send(
                view: '403',
                args: ['exception' => $e],
                layout: 'default',
                exit: true
            );
        } catch (BeforeValidException $e) {
            View::send(
                view: '403',
                args: ['exception' => $e],
                layout: 'default',
                exit: true
            );
        } catch (TokenExpiredException $e) {
            View::send(
                view: '403',
                args: ['exception' => $e],
                layout: 'default',
                exit: true
            );
        }

        Session::set('LAST_ACCESS', time());

        return $this->user;
    }

    public function signIn(): string
    {
        session_regenerate_id();

        $this->token = $this->createToken($this->createPayload());

        $refresh_token = static::createRefreshToken();

        $expires = time() + 2678400;

        Cookie::set('jwr', $refresh_token, ['expires' => $expires]);

        Cookie::set('jwt', $this->token, ['expires' => $expires]);

        Session::set('LAST_ACCESS', time());

        Session::set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

        Session::set('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);

        return $refresh_token;
    }

    public function signOut(): self
    {
        Cookie::delete('jwt');

        Cookie::delete('jwr');

        $this->token = '';

        return $this;
    }

    /**
     * Преобразование токена в объект
     * 
     * @param string $token Токен
     * @return object Объект токена
     */
    protected function decodeToken(string $token): object
    {
        return JWT::decode($token, static::getPublicKey(), [self::JWT_ALGORITHM]);
    }

    /**
     * Обновление токена
     * 
     * @param string $token Токен
     * @param string $refresh_token Токен обновления
     * @return string Обновленный токен
     * @throws RefreshTokenExpiredException
     */
    protected function refreshToken(string $token, string $refresh_token): string
    {
        $payload = $this->getPayload($token)?->data ?? null;

        $payload ??= $this->createPayload();

        $user = User::find($payload?->id ?? 0);

        if ($user->refresh_token !== $refresh_token || !$refresh_token) {
            throw new TokenExpiredException(Locale::get('jwr_token_expired'));
        }

        return $this->createToken($payload);
    }

    /**
     * Получение JWT токена
     * 
     * @param object $payload Объект токена
     * @return string Токен
     */
    protected function createToken(object $payload): string
    {
        $payload = [
            "iat" => time(),
            "exp" => time() + static::JWT_EXPIRES,
            "iss" => $_SERVER['HTTP_HOST'],
            "data" => $payload
        ];

        return JWT::encode($payload, static::getPrivateKey(), static::JWT_ALGORITHM);
    }

    /**
     * Получение данных объекта JWT токена
     * 
     * @return object Данные объекта токена
     */
    protected function createPayload(): object
    {
        return (object) [
            'id' => $this->user->id
        ];
    }

    /**
     * Получение объекта JWT токена из токена
     * 
     * @param string $token Токен
     * @return object Объект токена
     * @throws Exception
     */
    protected function getPayload(string $token): object
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
    public static function createRefreshToken(): string
    {
        return bin2hex(random_bytes(128));
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
