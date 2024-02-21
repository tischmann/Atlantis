<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use App\Models\{User};

use Tischmann\Atlantis\Exceptions\{
    BeforeValidException,
    SignatureInvalidException,
    TokenExpiredException
};

/**
 * Класс для работы с авторизацией
 */
final class Auth
{
    public const JWT_ALGORITHM = 'RS256'; // Алгоритм шифрования

    public const JWT_EXPIRES = 600; // Время жизни токена в секундах

    protected static ?string $privateKey = null; // Закрытый ключ

    protected static ?string $publicKey = null; // Открытый ключ

    protected string $token = ''; // Токен

    public function __construct(public User $user = new User())
    {
    }

    /**
     * Получение объекта авторизации
     *
     * @param User $user Пользователь
     * 
     * @return self
     */
    public static function instance(User $user = new User()): self
    {
        return new self($user);
    }

    /**
     * Получение закрытого ключа
     *
     * @return string
     */
    protected static function getPrivateKey(): string
    {
        if (static::$privateKey) return static::$privateKey;

        if (!is_file(__DIR__ . "/../../../private.pem")) {
            die('Файл private.pem не найден');
        }

        static::$privateKey = file_get_contents(__DIR__ . "/../../../private.pem");

        if (!static::$privateKey) die('Файл private.pem поврежден');

        return static::$privateKey;
    }

    /**
     * Получение открытого ключа
     *
     * @return string
     */
    protected static function getPublicKey(): string
    {
        if (!static::$publicKey) {
            if (!is_file(__DIR__ . "/../../../public.pem")) {
                die('Файл public.pem не найден');
            }

            static::$publicKey = file_get_contents(__DIR__ . "/../../../public.pem");

            if (!static::$publicKey) die('Файл public.pem поврежден');
        }

        return static::$publicKey;
    }

    /**
     * Авторизация
     *
     * @return User Пользователь 
     */
    public function authorize(): User
    {
        if (!static::isLastAccessValid()) {
            session_kill();
            return $this->user;
        }

        if (!static::isClientUserAgentValid()) {
            session_kill();
            return $this->user;
        }

        if (!static::isClientAddressValid()) {
            session_kill();
            return $this->user;
        }

        $jwt = Request::authorization() ?: cookies_get('jwt');

        $jwr = cookies_get('jwr');

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

            cookies_set('jwt', $jwt, ['expires' => $expires]);
        } catch (SignatureInvalidException $e) {
            die('Подпись токена недействительна');
        } catch (BeforeValidException $e) {
            die('Токен еще не вступил в силу');
        } catch (TokenExpiredException $e) {
            die('Срок дейстаия токена истёк');
        }

        session_set('LAST_ACCESS', time());

        return $this->user;
    }

    /**
     * Авторизация
     *
     * @return string Токен обновления
     */
    public function signIn(): string
    {
        session_regenerate_id();

        $this->token = $this->createToken($this->createPayload());

        $refresh_token = static::createRefreshToken();

        $expires = time() + 2678400;

        cookies_set('jwr', $refresh_token, ['expires' => $expires]);

        cookies_set('jwt', $this->token, ['expires' => $expires]);

        session_set('LAST_ACCESS', time());

        session_set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

        session_set('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);

        return $refresh_token;
    }

    /**
     * Выход
     *
     * @return self
     */
    public function signOut(): self
    {
        cookies_del('jwt');

        cookies_del('jwr');

        $this->token = '';

        return $this;
    }

    /**
     * Преобразование токена в объект
     * 
     * @param string $token Токен
     * 
     * @return object Объект
     */
    protected function decodeToken(string $token): object
    {
        return JsonWebToken::decode($token, static::getPublicKey(), [self::JWT_ALGORITHM]);
    }

    /**
     * Обновление токена
     * 
     * @param string $token Токен
     * 
     * @param string $refresh_token Токен обновления
     * 
     * @return string Обновленный токен
     * 
     * @throws TokenExpiredException
     */
    protected function refreshToken(string $token, string $refresh_token): string
    {
        $payload = $this->getPayload($token)?->data ?? null;

        $payload ??= $this->createPayload();

        $user = User::find($payload?->id ?? 0);

        if ($user->refresh_token !== $refresh_token || !$refresh_token) {
            throw new TokenExpiredException();
        }

        return $this->createToken($payload);
    }

    /**
     * Создание токена
     * 
     * @param object $payload Объект токена
     * 
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

        return JsonWebToken::encode($payload, static::getPrivateKey(), static::JWT_ALGORITHM);
    }

    /**
     * Создание данных токена
     * 
     * @return object Данные токена
     */
    protected function createPayload(): object
    {
        return (object) [
            'id' => $this->user->id
        ];
    }

    /**
     * Получение данных из токена
     * 
     * @param string $token Токен
     * 
     * @return object Объект токена
     */
    protected function getPayload(string $token): object
    {
        $segments = explode('.', $token);

        if (count($segments) !== 3) {
            die('Некорректное количество сегментов');
        }

        $payload = JsonWebToken::jsonDecode(JsonWebToken::urlsafeB64Decode($segments[1]));

        if ($payload === null) {
            die('Ошибка декодирования данных токена');
        }

        return $payload;
    }

    /**
     * Создание токена обновления
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
        if (!session_has('REMOTE_ADDR')) return true;

        return session_get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Проверяет User-Agent клиента
     *
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    public static function isClientUserAgentValid()
    {
        if (!session_has('USER_AGENT')) return true;

        return session_get('USER_AGENT') === $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Проверяет время последнего доступа к системе
     * 
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    public static function isLastAccessValid(int $seconds = 1440): bool
    {
        if (!session_has('LAST_ACCESS')) return true;

        return time() < intval(session_get('LAST_ACCESS')) + $seconds;
    }
}
