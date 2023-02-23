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
class Date
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

        return ($errors['warning_count'] ?? 0) + ($errors['error_count'] ?? 0) == 0;
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
        $locale = Locale::all()[$locale] ?? 'ru_RU';

        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT
        );

        $formatter->setPattern($pattern);

        return $formatter->format($date);
    }

    public static function getElapsed(DateTime $date): string
    {
        $now = new DateTime();

        $diff = $now->diff($date);

        $elapsed = '';

        if ($diff->y > 0) {
            $elapsed .= $diff->y . ' ';

            $elapsed .= match ($diff->y) {
                1 => Locale::get('year_ago'),
                2, 3, 4 => Locale::get('years_ago_2_4'),
                default => Locale::get('years_ago'),
            };
        } else if ($diff->m > 0) {
            $elapsed .= $diff->m . ' ';

            $elapsed .= match ($diff->m) {
                1 => Locale::get('month_ago'),
                2, 3, 4 => Locale::get('months_ago_2_4'),
                default => Locale::get('months_ago'),
            };
        } else if ($diff->d > 0) {
            $elapsed .= $diff->d . ' ';

            $elapsed .= match ($diff->d) {
                1 => Locale::get('day_ago'),
                2, 3, 4 => Locale::get('days_ago_2_4'),
                default => Locale::get('days_ago'),
            };
        } else if ($diff->h > 0) {
            $elapsed .= $diff->h . ' ';

            $elapsed .= match ($diff->h) {
                1 => Locale::get('hour_ago'),
                2, 3, 4 => Locale::get('hours_ago_2_4'),
                default => Locale::get('hours_ago'),
            };
        } else if ($diff->i > 0) {
            $elapsed .= $diff->i . ' ';

            $elapsed .= match ($diff->i) {
                1 => Locale::get('minute_ago'),
                2, 3, 4 => Locale::get('minutes_ago_2_4'),
                default => Locale::get('minutes_ago'),
            };
        } else if ($diff->s > 0) {
            $elapsed .= $diff->s . ' ';

            $elapsed .= match ($diff->s) {
                1 => Locale::get('second_ago'),
                2, 3, 4 => Locale::get('seconds_ago_2_4'),
                default => Locale::get('seconds_ago'),
            };
        }

        return $elapsed;
    }
}
