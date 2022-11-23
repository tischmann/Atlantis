<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс HTTP ответа
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
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

        header("X-XSS-Protection: 1; mode=block");

        header("Access-Control-Expose-Headers: Content-Encoding");

        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");

        header("Content-Security-Policy: "
            . "base-uri 'self'; "
            . "default-src 'self'; "
            . "img-src 'self' data: blob: https:; "
            . "child-src 'self' https:;"
            . "script-src 'strict-dynamic' 'nonce-" . getenv('APP_NONCE') . "'; "
            . "style-src 'unsafe-inline' https:; "
            . "frame-src 'none';"
            . "font-src 'self' https:; ");

        header("Strict-Transport-Security: max-age=31536000; preload");

        header("Cross-Origin-Resource-Policy: same-origin");

        header("Cross-Origin-Opener-Policy: same-origin");

        header("X-Content-Type-Options: nosniff");

        header("X-Frame-Options: SAMEORIGIN");
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
    public static function send(mixed $response, int $code = 200)
    {
        static::headers($code);

        match (Request::accept()) {
            'html' => static::html($response),
            'json' => static::json($response),
            'text' => static::text($response),
            'xml' => static::xml($response),
            'default' => var_dump($response)
        };
    }

    /**
     * Отправка HTML ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function html(mixed $response): void
    {
        header('Content-Type: text/html; charset=UTF-8');

        if (is_string($response)) {
            echo $response;
        } else {
            var_dump($response);
        }
    }

    /**
     * Отправка JSON ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function json(mixed $response): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if (is_string($response)) {
            echo $response;
        } else {
            echo json_encode($response, 256 | 128 | 32);
        }
    }

    /**
     * Отправка XML ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function xml(mixed $response): void
    {
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
    }

    /**
     * Отправка текстового ответа
     *
     * @param string $response Данные для отправки 
     * @return void
     */
    public static function text(mixed $response): void
    {
        header('Content-Type: text/plain; charset=UTF-8');

        if (is_object($response) || is_array($response)) {
            json_encode($response, 256 | 128 | 32);
        } else {
            echo strval($response);
        }
    }
}
