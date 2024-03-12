<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс HTTP ответа
 */
final class Response
{
    private function __construct()
    {
    }

    /**
     * Отправка HTTP заголовков
     *
     * @param int $code Код ответа
     * @return void
     */
    public static function headers(int $code = 200): void
    {
        http_response_code($code);

        header("Access-Control-Allow-Origin: https://{$_SERVER['HTTP_HOST']}");

        header("Cache-Control: public, max-age=31536000, no-transform");

        header("Access-Control-Expose-Headers: Content-Encoding");

        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");

        header("Content-Security-Policy: "
            . "default-src 'none'; "
            . "base-uri 'none'; "
            . "connect-src 'self'; "
            . "manifest-src 'self'; "
            . "img-src 'self' data: blob: https:; "
            . "child-src 'self' https:;"
            . "script-src https: http: 'strict-dynamic' 'nonce-" . getenv('APP_NONCE') . "' 'unsafe-inline'; "
            . "object-src 'self'; "
            . "style-src 'unsafe-inline' https:; "
            . "frame-src 'self' https:;"
            . "font-src 'self' https: data:; ");

        header("Strict-Transport-Security: max-age=31536000; preload");

        header("Cross-Origin-Resource-Policy: same-origin");

        header("Cross-Origin-Opener-Policy: same-origin");

        header("X-Content-Type-Options: nosniff");
    }

    /**
     * Перенаправление на другой URL
     *
     * @param string $url URL для перенаправления 
     * @return void
     */
    public static function redirect(string $url = '/'): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Отправка ответа
     *
     * @param mixed $response Данные для отправки 
     * @param int $code Код ответа (по умолчанию 200)
     * @return void
     */
    public static function send(mixed $response = '', int $code = 200)
    {
        switch (Request::accept()) {
            case 'html':
                return static::html($response, $code);
            case 'json':
                return static::json($response, $code);
            case 'text':
                return static::text($response, $code);
            case 'xml':
                return static::xml($response, $code);
            default:
                static::headers($code);
                var_dump($response);
                exit;
        }
    }

    /**
     * Отправка HTML ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function html(mixed $response, int $code = 200): void
    {
        static::headers($code);

        header('Content-Type: text/html; charset=UTF-8');

        if (is_string($response)) {
            echo $response;
        } else {
            var_dump($response);
        }

        exit;
    }

    /**
     * Отправка JSON ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function json(mixed $response = [], int $code = 200): void
    {
        static::headers($code);

        header('Content-Type: application/json; charset=UTF-8');

        if (is_string($response)) {
            echo $response;
        } else {
            echo json_encode($response, 256 | 128 | 32);
        }

        exit;
    }

    /**
     * Отправка XML ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function xml(mixed $response, int $code = 200): void
    {
        static::headers($code);

        header('Content-Type: application/xml; charset=UTF-8');

        $xml = simplexml_load_string(
            <<<XML
            <?xml version='1.0'?> 
            <document>
            </document>
            XML
        );

        if (!is_array($response) && !is_object($response)) {
            $response = ['response' => strval($response)];
        }

        foreach ($response as $key => $value) {
            $xml->addChild($key, strval($value));
        }

        echo $xml->asXML();

        exit;
    }

    /**
     * Отправка текстового ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function text(mixed $response, int $code = 200): void
    {
        static::headers($code);

        header('Content-Type: text/plain; charset=UTF-8');

        if (is_object($response) || is_array($response)) {
            echo "<pre>" . json_encode($response, 256 | 128 | 32) . "</pre>";
        } else {
            echo strval($response);
        }

        exit;
    }
}
