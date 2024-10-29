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
 * 
 * Авторизация реализована на основе JWT (JSON Web Token)
 */
final class Auth
{
    private const JWT_ALGORITHM = 'RS256'; // Алгоритм шифрования

    private const JWT_EXPIRES = 600; // Время жизни токена в секундах

    private static ?string $privateKey = null; // Закрытый ключ

    private static ?string $publicKey = null; // Открытый ключ

    private static ?User $user = null; // Пользователь

    private static string $token = ''; // Токен

    // Запрещаем создание экземпляра класса
    private function __construct() {}

    /**
     * Получение пользователя
     *
     * @return User Пользователь
     */
    private static function getUser(): User
    {
        static::$user ??= new User();
        return static::$user;
    }

    /**
     * Получение закрытого ключа
     *
     * @return string
     */
    private static function getPrivateKey(): string
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
    private static function getPublicKey(): string
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
    public static function authorize(): User
    {
        if (
            !static::isLastAccessValid()
            || !static::isClientUserAgentValid()
            || !static::isClientAddressValid()
        ) {
            session_kill();
            return static::getUser();
        }

        $jwt = Request::authorization() ?: cookies_get('jwt');

        $jwr = cookies_get('jwr');

        if (!$jwt || !$jwr) return static::getUser();

        try {
            $decoded = static::decodeToken($jwt);

            static::$user = User::find($decoded->data->id, 'id');
        } catch (TokenExpiredException $e) {
            $jwt = static::refreshToken($jwt, $jwr);

            $decoded = static::decodeToken($jwt);

            static::$user = User::find($decoded->data->id, 'id');

            static::$token = $jwt;

            static::$user->refresh_token = $jwr;

            static::$user->save();

            $expires = time() + 2678400;

            cookies_set('jwt', $jwt, ['expires' => $expires]);
        } catch (SignatureInvalidException $e) {
            die('Подпись токена недействительна');
        } catch (BeforeValidException $e) {
            die('Токен еще не вступил в силу');
        }

        session_set('LAST_ACCESS', time());

        return static::getUser();
    }

    /**
     * Авторизация
     *
     * @return string Токен обновления
     */
    public static function signIn(User $user): string
    {
        static::$user = $user;

        session_regenerate_id();

        static::$token = static::createToken(static::createPayload());

        $refresh_token = static::createRefreshToken();

        $expires = time() + 2678400;

        cookies_set('jwr', $refresh_token, ['expires' => $expires]);

        cookies_set('jwt', static::$token, ['expires' => $expires]);

        session_set('LAST_ACCESS', time());

        session_set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);

        session_set('USER_AGENT', $_SERVER['HTTP_USER_AGENT']);

        return $refresh_token;
    }

    /**
     * Выход
     *
     * @return void
     */
    public static function signOut(): void
    {
        cookies_del('jwt');

        cookies_del('jwr');

        static::$token = '';
    }

    /**
     * Преобразование токена в объект
     * 
     * @param string $token Токен
     * 
     * @return object Объект
     */
    private static function decodeToken(string $token): object
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
    private static function refreshToken(string $token, string $refresh_token): string
    {
        $payload = static::getPayload($token)?->data ?? null;

        $payload ??= static::createPayload();

        $user = User::find($payload?->id ?? 0);

        if ($user->refresh_token !== $refresh_token || !$refresh_token) {
            cookies_del('jwt');
            cookies_del('jwr');
            static::$token = '';
            header('Location: /');
            exit;
        }

        return static::createToken($payload);
    }

    /**
     * Создание токена
     * 
     * @param object $payload Объект токена
     * 
     * @return string Токен
     */
    private static function createToken(object $payload): string
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
    private static function createPayload(): object
    {
        return (object) [
            'id' => static::getUser()->id
        ];
    }

    /**
     * Получение данных из токена
     * 
     * @param string $token Токен
     * 
     * @return object Объект токена
     */
    private static function getPayload(string $token): object
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
    private static function createRefreshToken(): string
    {
        return bin2hex(random_bytes(128));
    }

    /**
     * Проверяет IP-адрес клиента 
     *
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    private static function isClientAddressValid(): bool
    {
        if (!session_has('REMOTE_ADDR')) return true;

        return session_get('REMOTE_ADDR') === $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Проверяет User-Agent клиента
     *
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    private static function isClientUserAgentValid()
    {
        if (!session_has('USER_AGENT')) return true;

        return session_get('USER_AGENT') === $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * Проверяет время последнего доступа к системе
     * 
     * @return boolean true - проверка прошла успешно, false - проверка не прошла
     */
    private static function isLastAccessValid(int $seconds = 1440): bool
    {
        if (!session_has('LAST_ACCESS')) return true;

        return time() < intval(session_get('LAST_ACCESS')) + $seconds;
    }
}
