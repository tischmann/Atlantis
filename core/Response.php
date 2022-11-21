<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class Response
{
    private function __construct()
    {
    }

    public static function headers(): void
    {
        header("Access-Control-Allow-Origin: https://{$_SERVER['HTTP_HOST']}");

        header("Cache-Control: max-age=180");

        header("X-XSS-Protection: 1; mode=block");

        header("Access-Control-Expose-Headers: Content-Encoding");

        header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");

        header("Content-Security-Policy: "
            . "base-uri 'self'; "
            . "default-src 'self'; "
            . "img-src 'self' data: blob: https:; "
            . "child-src 'self' https:;"
            . "script-src 'self' 'strict-dynamic' 'unsafe-inline' https: 'nonce-"
            . getenv('APP_NONCE') . "'; "
            . "style-src 'unsafe-inline' https:; "
            . "frame-src 'none';"
            . "font-src 'self' https:; ");

        header("Strict-Transport-Security: max-age=180; preload");

        header("Cross-Origin-Resource-Policy: same-origin");

        header("Cross-Origin-Opener-Policy: same-origin");

        header("X-Content-Type-Options: nosniff");

        header("X-Frame-Options: SAMEORIGIN");
    }

    public static function redirect(string $path)
    {
        header('Location: ' . $path);
        exit;
    }

    public static function echo(mixed $response)
    {
        static::headers();

        if ($response instanceof Error) {
            http_response_code(500);
        } else {
            http_response_code(200);
        }

        match (Request::accept()) {
            'html' => static::html($response),
            'json' => static::json($response),
            'text' => static::text($response),
            'default' => var_dump($response)
        };
    }

    private static function html(mixed $response): void
    {
        header('Content-Type: text/html; charset=UTF-8');

        if ($response instanceof Error) {
            echo $response->html();
        } elseif (is_string($response)) {
            echo $response;
        } else {
            var_dump($response);
        }
    }

    private static function json(mixed $response): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        if ($response instanceof Error) {
            echo $response->json();
        } elseif (is_string($response)) {
            echo $response;
        } else {
            echo json_encode($response, 256 | 128 | 32);
        }
    }

    private static function text(mixed $response): void
    {
        header('Content-Type: text/plain; charset=UTF-8');

        if ($response instanceof Error) {
            echo $response->text();
        } else if (is_object($response) || is_array($response)) {
            json_encode($response, 256 | 128 | 32);
        } else {
            echo strval($response);
        }
    }
}
