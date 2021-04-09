<?php

namespace Atlantis;

final class Language
{
    public string $code; // ISO 639-1 Language Code
    public array $strings = [];

    public function __construct(string $code = 'ru')
    {
        $this->code = $code;
    }

    public static function get(string $key): string
    {
        if (!Session::get('LANG')) {
            Session::set('LANG', []);
            $code = Request::cookie('language') ?? getenv('APP_LANG') ?: 'ru';

            foreach (glob("../lang/{$code}/*.php") as $path) {
                $array = include $path;

                if (is_array($array)) {
                    Session::set('LANG', array_merge($array, Session::get('LANG')));
                }
            }
        }

        return Session::get('LANG')[$key] ?? $key;
    }

    public function load()
    {
        $this->strings = [];

        foreach (glob("../lang/{$this->code}/*.php") as $path) {
            $array = include $path;

            if (is_array($array)) {
                $this->strings = array_merge($array, $this->strings);
            }
        }

        return $this;
    }

    public function change(string $code)
    {
        $this->code = $code;
        return $this->load();
    }

    public function string(string $key): string
    {
        return $this->strings[$key] ?? $key;
    }

    public static function available(): array
    {
        return array_map(function ($path) {
            return strtolower(basename($path));
        }, glob("../lang/*", GLOB_ONLYDIR));
    }
}
