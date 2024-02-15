<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use Exception;

/**
 * Класс для работы с JSON Web Token
 * 
 */
final class JWT
{
    private const ASN1_INTEGER = 0x02;

    private const ASN1_SEQUENCE = 0x10;

    private const ASN1_BIT_STRING = 0x03;

    private const SUPPORTED_ALGORITHMS = [
        'ES256' => ['openssl', 'SHA256'],
        'HS256' => ['hash_hmac', 'SHA256'],
        'HS384' => ['hash_hmac', 'SHA384'],
        'HS512' => ['hash_hmac', 'SHA512'],
        'RS256' => ['openssl', 'SHA256'],
        'RS384' => ['openssl', 'SHA384'],
        'RS512' => ['openssl', 'SHA512'],
    ];

    /**
     * Преобразует токен в объект
     * 
     * @param string $token Токен
     * @param string|array $publicKey Публичный ключ
     * @param array $allowedAlgorithms Разрешенные алгоритмы
     * @return object Объект токена 
     */
    public static function decode(
        string $token,
        string|array $publicKey,
        array $allowedAlgorithms = []
    ): object {
        $timestamp = time();

        if (!$publicKey) {
            throw new Exception(get_str('jwt_missing_public_key'));
        }

        $segments = explode('.', $token);

        $amount = count($segments);

        if ($amount !== 3) {
            throw new Exception(get_str('jwt_wrong_segment_amount') . ": {$amount}");
        }

        list($headb64, $bodyb64, $cryptob64) = $segments;

        $header = static::jsonDecode(static::urlsafeB64Decode($headb64));

        if ($header === null) {
            throw new Exception(get_str('jwt_bad_header'));
        }

        $payload = static::jsonDecode(static::urlsafeB64Decode($bodyb64));

        if ($payload === null) {
            throw new Exception(get_str('jwt_bad_payload'));
        }

        $signature = static::urlsafeB64Decode($cryptob64);

        if ($signature === false) throw new SignatureInvalidException();

        if (!key_exists($header->alg, static::SUPPORTED_ALGORITHMS)) {
            throw new Exception(get_str('jwt_algorithm_not_supported'));
        }

        if (!in_array($header->alg, $allowedAlgorithms)) {
            throw new Exception(get_str('jwt_algorithm_not_allowed'));
        }

        if ($header->alg === 'ES256') {
            $signature = static::signatureToDER($signature);
        }

        if ($publicKey === (array) $publicKey) {
            if (property_exists($header, 'kid')) {
                if (!key_exists($header->kid, $publicKey)) {
                    throw new Exception(get_str('jwt_key_id_invalid'));
                }

                $publicKey = $publicKey[$header->kid];
            } else {
                throw new Exception(get_str('jwt_key_id_missing'));
            }
        }

        $verified = static::verify(
            "$headb64.$bodyb64",
            $signature,
            $publicKey,
            $header->alg
        );

        if (!$verified) throw new SignatureInvalidException();

        $nbf = $payload?->nbf ?? 0;

        if ($nbf > $timestamp) {
            throw new BeforeValidException(get_str('jwt_token_not_yet_valid'));
        }

        $iat = $payload?->iat ?? 0;

        if ($iat > $timestamp) {
            throw new BeforeValidException(get_str('jwt_token_not_yet_valid'));
        }

        $exp = $payload?->exp ?? 0;

        if ($timestamp >= $exp) {
            throw new TokenExpiredException(get_str('jwt_token_expired'));
        }

        return $payload;
    }

    /**
     * Преобразует данные в токен
     *
     * @param object|array $payload Данные
     * @param string $privateKey Приватный ключ
     * @param string $algorithm Алгоритм
     * @param string|null $keyId Идентификатор ключа
     * @param array $head Заголовок
     * @return string Токен
     */
    public static function encode(
        object|array $payload,
        string $privateKey,
        string $algorithm = 'HS256',
        ?string $keyId = null,
        array $head = []
    ): string {
        $header = array('typ' => 'JWT', 'alg' => $algorithm);

        if ($keyId !== null) $header['kid'] = $keyId;

        if ($head && is_array($head)) {
            $header = array_merge($head, $header);
        }

        $segments = [
            static::urlsafeB64Encode(static::jsonEncode($header)),
            static::urlsafeB64Encode(static::jsonEncode($payload))
        ];

        $signature = static::sign(
            implode('.', $segments),
            $privateKey,
            $algorithm
        );

        $segments[] = static::urlsafeB64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * Подписывает данные
     *
     * @param string $data Данные
     * @param string $privateKey Приватный ключ
     * @param string $algorithm Алгоритм
     * @return string Подпись
     */
    public static function sign(
        string $data,
        string $privateKey,
        string $algorithm = 'HS256'
    ): string {
        if (!key_exists($algorithm, static::SUPPORTED_ALGORITHMS)) {
            throw new Exception(get_str('jwt_algorithm_not_supported'));
        }

        list($function, $algorithm) = static::SUPPORTED_ALGORITHMS[$algorithm];

        switch ($function) {
            case 'hash_hmac':
                return hash_hmac($algorithm, $data, $privateKey, true);
            case 'openssl':
                $signature = '';

                $success = openssl_sign(
                    $data,
                    $signature,
                    $privateKey,
                    $algorithm
                );

                if (!$success) {
                    throw new Exception(get_str('jwt_ssl_unable_to_sign'));
                } else {
                    if ($algorithm === 'ES256') {
                        $signature = static::signatureFromDER($signature, 256);
                    }

                    return $signature;
                }
        }
    }

    /**
     * Проверяет подпись данных
     *
     * @param string $data Данные
     * @param string $signature Подпись
     * @param string $publicKey Публичный ключ
     * @param string $algorithm Алгоритм
     * @return boolean true если подпись верна и false если нет
     */
    private static function verify(
        string $data,
        string $signature,
        string $publicKey,
        string $algorithm
    ): bool {
        if (!key_exists($algorithm, static::SUPPORTED_ALGORITHMS)) {
            throw new Exception(get_str('jwt_algorithm_not_supported'));
        }

        list($function, $algorithm) = static::SUPPORTED_ALGORITHMS[$algorithm];

        switch ($function) {
            case 'openssl':
                $success = openssl_verify(
                    $data,
                    $signature,
                    $publicKey,
                    $algorithm
                );

                if ($success === 1) return true;
                elseif ($success === 0) return false;

                throw new Exception(get_str('jwt_ssl_unable_to_sign') . ": " . openssl_error_string());
            case 'hash_hmac':
            default:
                $hash = hash_hmac($algorithm, $data, $publicKey, true);

                if (function_exists('hash_equals')) {
                    return hash_equals($signature, $hash);
                }

                $len = min(
                    static::safeStrlen($signature),
                    static::safeStrlen($hash)
                );

                $status = 0;

                for ($i = 0; $i < $len; $i++) {
                    $status |= (ord($signature[$i]) ^ ord($hash[$i]));
                }

                $status |= static::safeStrlen($signature)
                    ^ static::safeStrlen($hash);

                return $status === 0;
        }
    }

    /**
     * Преобразует JSON строку в объект
     * 
     * @param string $input JSON Строка
     * @return object Объект
     */
    public static function jsonDecode(string $input): object
    {
        $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);

        if ($errno = json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($obj === null && $input !== 'null') {
            throw new Exception(get_str('jwt_null_result'));
        }

        return $obj;
    }

    /**
     * Преобразует объект в JSON строку
     * 
     * @param object|array $input Объект
     * @return string JSON Строка
     */
    public static function jsonEncode(object|array $input): string
    {
        $json = json_encode($input);

        if ($errno = json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            throw new Exception(get_str('jwt_null_result'));
        }

        return $json;
    }

    /**
     * URL-безопасное преобразование в строку base64
     *
     * @param string $input Строка
     * @return string Строка base64
     */
    public static function urlsafeB64Decode(string $input): string
    {
        $remainder = strlen($input) % 4;

        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * URL-безопасное преобразование из строки base64
     *
     * @param string $input Строка base64
     * @return string Строка
     */
    public static function urlsafeB64Encode(string $input): string
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * Обработка ошибок преобразования JSON
     *
     * @param integer $errorCode Код ошибки
     * @return void
     */
    private static function handleJsonError(int $errorCode): never
    {
        $messages = [
            JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
            JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8 => 'Malformed UTF-8 characters'
        ];

        throw new Exception(get_str('json_error') . ": " . $messages[$errorCode] ?? $errorCode);
    }

    /**
     * Определение длины строки
     * 
     * @param string $input Строка
     * @return int Длина строки
     */
    private static function safeStrlen(string $input): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($input, '8bit');
        }

        return strlen($input);
    }

    /**
     * Преобразование подписи в DER-формат
     * 
     * @param string $signature Подпись
     * @return string Подпись в DER-формате
     */
    private static function signatureToDER(string $signature): string
    {

        list($r, $s) = str_split($signature, (int) (strlen($signature) / 2));

        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");

        if (ord($r[0]) > 0x7f) $r = "\x00" . $r;
        if (ord($s[0]) > 0x7f) $s = "\x00" . $s;

        return static::encodeDER(
            static::ASN1_SEQUENCE,
            static::encodeDER(static::ASN1_INTEGER, $r) .
                static::encodeDER(static::ASN1_INTEGER, $s)
        );
    }

    /**
     * Преобразование DER-формата в подпись
     * 
     * @param int $type Тип
     * @param string $value Подпись в DER-формате
     * @return string Подпись
     */
    private static function encodeDER(int $type, string $value): string
    {
        $tagHeader = 0;

        if ($type === static::ASN1_SEQUENCE) $tagHeader |= 0x20;

        $der = chr($tagHeader | $type);
        $der .= chr(strlen($value));

        return $der . $value;
    }

    /**
     * Преобразование строки DER-формата в подпись
     * 
     * @param string $derSignature Строка DER-формата
     * @param int $keySize Размер ключа
     * @return string Подпись
     */
    private static function signatureFromDER(
        string $derSignature,
        int $keySize
    ): string {
        list($offset, $_) = static::readDER($derSignature);
        list($offset, $r) = static::readDER($derSignature, $offset);
        list($offset, $s) = static::readDER($derSignature, $offset);

        $r = ltrim($r, "\x00");
        $s = ltrim($s, "\x00");

        $r = str_pad($r, $keySize / 8, "\x00", STR_PAD_LEFT);
        $s = str_pad($s, $keySize / 8, "\x00", STR_PAD_LEFT);

        return $r . $s;
    }

    /**
     * Чтение DER-формата
     * 
     * @param string $derSignature Строка DER-формата
     * @param int $offset Смещение
     * @return array Массив с данными
     */
    private static function readDER(
        string $derSignature,
        int $offset = 0
    ): array {
        $pos = $offset;
        $size = strlen($derSignature);
        $constructed = (ord($derSignature[$pos]) >> 5) & 0x01;
        $type = ord($derSignature[$pos++]) & 0x1f;
        $len = ord($derSignature[$pos++]);

        if ($len & 0x80) {
            $n = $len & 0x1f;
            $len = 0;

            while ($n-- && $pos < $size) {
                $len = ($len << 8) | ord($derSignature[$pos++]);
            }
        }

        if ($type == static::ASN1_BIT_STRING) {
            $pos++;
            $data = substr($derSignature, $pos, $len - 1);
            $pos += $len - 1;
        } elseif (!$constructed) {
            $data = substr($derSignature, $pos, $len);
            $pos += $len;
        } else {
            $data = null;
        }

        return [$pos, $data];
    }
}
