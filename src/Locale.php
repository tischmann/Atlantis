<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы с локализацией
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Locale
{
    private static array $locales = []; // Массив с локализациями (ключи - локали, значения - массивы с переводами)

    private function __construct()
    {
    }

    /**
     * Возвращает строку из файла локализации по ключу
     *
     * @param string $key Ключ
     * @param string $locale Локаль
     * @return string Строка из файла локализации
     */
    public static function get(string $key, string $locale = ''): string
    {
        $locale = $locale ?: getenv('APP_LOCALE');

        self::$locales[$locale] ??= static::load($locale);

        return self::$locales[$locale][$key] ?? $key;
    }

    public static function getLocale(string $locale): array
    {
        self::$locales[$locale] ??= static::load($locale);

        return self::$locales[$locale];
    }

    /**
     * Возвращает массив доступных локалей
     *
     * @return array Массив доступных локалей
     */
    public static function available(): array
    {
        return array_map(
            function ($path) {
                return strtolower(basename($path, ".php"));
            },
            glob(getenv('APP_ROOT') . "/lang/*.php")
        );
    }

    /**
     * Проверяет, существует ли локаль
     * 
     * @param string $locale Локаль
     * @return bool true - если локаль существует, иначе false
     */
    public static function exists(string $locale): bool
    {
        return in_array(strtolower($locale), static::available());
    }

    /**
     * Загружает файл локализации
     *
     * @param string $locale Локаль
     * @return array Массив строк из файла локализации
     */
    public static function load(string $locale = ''): array
    {
        $locale = $locale ?: getenv('APP_LOCALE');

        $strings = [];

        $path = getenv('APP_ROOT') . "/lang/{$locale}.php";

        if (file_exists($path)) {
            $array = require_once $path;
            if (is_array($array)) $strings = array_merge($array, $strings);
        }

        return $strings;
    }

    /**
     * Возвращает массив с локализациями
     *
     * @return array Массив с локализациями
     */
    public static function all(): array
    {
        return [
            'af' => 'af-ZA',
            'am' => 'am-ET',
            'ar' => 'ar-AE',
            'as' => 'as-IN',
            'az' => 'az-Latn-AZ',
            'ba' => 'ba-RU',
            'be' => 'be-BY',
            'bg' => 'bg-BG',
            'bn' => 'bn-BD',
            'bo' => 'bo-CN',
            'br' => 'br-FR',
            'bs' => 'bs-Latn-BA',
            'ca' => 'ca-ES',
            'co' => 'co-FR',
            'cs' => 'cs-CZ',
            'cy' => 'cy-GB',
            'da' => 'da-DK',
            'de' => 'de-DE',
            'dv' => 'dv-MV',
            'el' => 'el-GR',
            'en' => 'en-US',
            'es' => 'es-ES',
            'et' => 'et-EE',
            'eu' => 'eu-ES',
            'fa' => 'fa-IR',
            'fi' => 'fi-FI',
            'fo' => 'fo-FO',
            'fr' => 'fr-FR',
            'fy' => 'fy-NL',
            'ga' => 'ga-IE',
            'gd' => 'gd-GB',
            'gl' => 'gl-ES',
            'gu' => 'gu-IN',
            'ha' => 'ha-Latn-NG',
            'he' => 'he-IL',
            'hi' => 'hi-IN',
            'hr' => 'hr-HR',
            'hu' => 'hu-HU',
            'hy' => 'hy-AM',
            'id' => 'id-ID',
            'ig' => 'ig-NG',
            'ii' => 'ii-CN',
            'is' => 'is-IS',
            'it' => 'it-IT',
            'iu' => 'iu-Latn-CA',
            'ja' => 'ja-JP',
            'ka' => 'ka-GE',
            'kk' => 'kk-KZ',
            'kl' => 'kl-GL',
            'km' => 'km-KH',
            'kn' => 'kn-IN',
            'ko' => 'ko-KR',
            'ky' => 'ky-KG',
            'lb' => 'lb-LU',
            'lo' => 'lo-LA',
            'lt' => 'lt-LT',
            'lv' => 'lv-LV',
            'mi' => 'mi-NZ',
            'mk' => 'mk-MK',
            'ml' => 'ml-IN',
            'mn' => 'mn-MN',
            'mr' => 'mr-IN',
            'ms' => 'ms-BN',
            'mt' => 'mt-MT',
            'nb' => 'nb-NO',
            'ne' => 'ne-NP',
            'nl' => 'nl-NL',
            'nn' => 'nn-NO',
            'oc' => 'oc-FR',
            'or' => 'or-IN',
            'pa' => 'pa-IN',
            'pl' => 'pl-PL',
            'ps' => 'ps-AF',
            'pt' => 'pt-PT',
            'rm' => 'rm-CH',
            'ro' => 'ro-RO',
            'ru' => 'ru-RU',
            'rw' => 'rw-RW',
            'sa' => 'sa-IN',
            'se' => 'se-SE',
            'si' => 'si-LK',
            'sk' => 'sk-SK',
            'sl' => 'sl-SI',
            'sq' => 'sq-AL',
            'sr' => 'sr-Latn-CS',
            'sv' => 'sv-FI',
            'sw' => 'sw-KE',
            'ta' => 'ta-IN',
            'te' => 'te-IN',
            'tg' => 'tg-Cyrl-TJ',
            'th' => 'th-TH',
            'tk' => 'tk-TM',
            'tn' => 'tn-ZA',
            'tr' => 'tr-TR',
            'tt' => 'tt-RU',
            'ug' => 'ug-CN',
            'uk' => 'uk-UA',
            'ur' => 'ur-PK',
            'uz' => 'uz-Latn-UZ',
            'vi' => 'vi-VN',
            'wo' => 'wo-SN',
            'xh' => 'xh-ZA',
            'yo' => 'yo-NG',
            'zh' => 'zh-CN',
            'zu' => 'zu-ZA',
        ];
    }
}
