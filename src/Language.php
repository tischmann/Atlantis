<?php

namespace Atlantis;

final class Language
{
    public string $code; // ISO 639-1 Language Code
    public array $strings = [];

    public function __construct(string $code = 'ru')
    {
        $this->change($code);
    }

    public function change(string $code)
    {
        $this->code = $code;
        $this->strings = [];

        foreach (glob("../lang/{$this->code}/*.php") as $path) {
            $strings = include $path;

            if (!is_array($strings)) continue;

            $this->strings = array_merge($strings, $this->strings);
        }

        return $this;
    }

    public function get(string $key): string
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
