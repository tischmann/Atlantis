<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use DateTime;

use Exception;

use IntlDateFormatter;

/**
 * Класс утилит
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
class DateTimeUtilites
{
    /**
     * Возвращает год в виде числа
     * 
     * @return int Год
     */
    public static function getYear(DateTime $date): int
    {
        return intval($date->format("Y"));
    }

    /**
     * Возвращает месяц в виде числа
     * 
     * @return int Месяц
     */
    public static function getMonth(DateTime $date): int
    {
        return intval($date->format("m"));
    }

    /**
     * Возвращает день в виде числа
     *
     * @return int День
     */
    public static function getDay(DateTime $date): int
    {
        return intval($date->format("d"));
    }

    /**
     * Возвращает часы в виде числа
     * 
     * @return int Часы
     */
    public static function getHours(DateTime $date): int
    {
        return intval($date->format("H"));
    }

    /**
     * Возвращает минуты в виде числа
     * 
     * @return int Минуты
     */
    public static function getMinutes(DateTime $date): int
    {
        return intval($date->format("i"));
    }

    /**
     * Возвращает секунды в виде числа
     * 
     * @return int Секунды
     */
    public static function getSeconds(DateTime $date): int
    {
        return intval($date->format("s"));
    }

    /**
     * Проверяет корректность строкового представления даты и времени
     * 
     * @param string $dateString Строковое представление даты и времени
     * @return bool true - корректно, false - некорректно
     */
    public static function isValid(string $dateString): bool
    {
        if (!$dateString) return false;

        try {
            $date = new DateTime($dateString);
        } catch (Exception $e) {
            return false;
        }

        $errors = $date::getLastErrors();

        return $errors['warning_count'] + $errors['error_count'] == 0;
    }

    /**
     * Возвращает строковое представление даты и времени для выбранной локали и в выбранном формате
     * 
     * @param DateTime $date Дата и время
     * @param string $locale Локаль
     * @param string $pattern Формат даты и времени
     * @return string Строковое представление даты и времени
     */
    public static function localeFormat(
        DateTime $date,
        string $locale = 'ru',
        string $pattern = 'd MMMM kk:mm'
    ): string {
        $locale = match ($locale) {
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
            default => 'ru_RU'
        };

        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);

        $formatter->setPattern($pattern);

        return $formatter->format($date);
    }
}
