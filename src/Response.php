<?php

namespace Atlantis;

class Response
{
    public $response = null;

    public function __construct($response = null)
    {
        $this->response = $response;
    }

    public function headers()
    {
        header("Cache-Control: max-age=31536000");
        header("X-XSS-Protection: 1; mode=block");
        header("Content-Security-Policy: default-src 'self'; "
            . "img-src 'self' data:; child-src 'none'; "
            . "script-src 'self' 'nonce-" . Session::get('SCRIPT_NONCE') . "'; "
            . "style-src 'self';");
        header("Strict-Transport-Security: max-age=31536000; preload");
        header("Cross-Origin-Resource-Policy: same-origin");
        header("Cross-Origin-Opener-Policy: same-origin");
        header("Cross-Origin-Opener-Policy: same-origin");
    }

    public function json($response = null)
    {
        header("Content-Type: application/json; charset=UTF-8");
        $this->headers();

        if ($response instanceof Error) {
            if ($response->status) {
                http_response_code($response->status);
            }
        }

        die(json_encode($response ?? $this->response, 256 | 32));
    }

    public function html($response = null)
    {
        header("Content-Type: text/html; charset=UTF-8");
        $this->headers();

        if ($response instanceof Error) {
            if ($response->status) {
                http_response_code($response->status);
            }
        }

        die($response ?? $this->response);
    }

    public static function response($response)
    {
        $instance = new self();

        $json = preg_match('/application\/json/i', $_SERVER['HTTP_ACCEPT']);

        if ($json) {
            return $instance->json($response);
        }

        return $instance->html($response);
    }
}
