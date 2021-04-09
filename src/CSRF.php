<?php

namespace Atlantis;

final class CSRF
{
    static function set(): string
    {
        return Session::set('CSRF', bin2hex(random_bytes(256)));
    }

    static function get()
    {
        return Session::get('CSRF');
    }

    static function del()
    {
        Session::del('CSRF');
    }

    static function isset(): bool
    {
        return (bool) self::get();
    }

    static function check(): bool
    {
        $request = new Request();

        $isset = self::isset();

        if (!$isset) {
            Response::response(new Error(
                message: Language::get('error_csrf_not_set')
            ));
        }

        $csrf = self::get();

        $input = $request->csrf_token ?? $request->headers()['X-CSRF-Token']
            ?? null;

        if ($input !== $csrf) {
            Response::response(new Error(
                message: Language::get('error_csrf_not_match')
            ));
        }

        return true;
    }
}
