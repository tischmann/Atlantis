<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

use DateTime;

use Exception;

use IntlDateFormatter;

class Date extends DateTime
{
    /**
     * Проверяет корректность строкового представления даты и времени
     * 
     * @param string $dateString Строковое представление даты и времени
     * @return bool true - корректно, false - некорректно
     */
    public static function validate(string $dateString): bool
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
     * @param string $locale Локаль
     * @param string $pattern Формат даты и времени
     * @return string Строковое представление даты и времени
     */
    public function localeFormat(
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

        return $formatter->format($this);
    }

    /**
     * Возвращает строковое представление даты и времени в формате прошедшего времени
     * 
     * @param string|null $locale Локаль (по умолчанию - ru)
     *
     * @return string
     */
    public function getElapsedTime(?string $locale = null): string
    {
        $locale ??= getenv('APP_LOCALE') ?: 'ru';

        $now = new static('now');

        $diff = $now->diff($this);

        $elapsed = '';

        if ($diff->y > 0) {
            $elapsed .= $diff->y . ' ';

            $elapsed .= match (abs($diff->y % 10)) {
                1 => Locale::get('year_ago', $locale),
                2, 3, 4 => Locale::get('years_ago_2_4', $locale),
                default => Locale::get('years_ago', $locale),
            };
        } else if ($diff->m > 0) {
            $elapsed .= $diff->m . ' ';

            $elapsed .= match (abs($diff->m % 10)) {
                1 => Locale::get('month_ago', $locale),
                2, 3, 4 => Locale::get('months_ago_2_4', $locale),
                default => Locale::get('months_ago', $locale),
            };
        } else if ($diff->d > 0) {
            $elapsed .= $diff->d . ' ';

            $elapsed .= match (abs($diff->d % 10)) {
                1 => Locale::get('day_ago', $locale),
                2, 3, 4 => Locale::get('days_ago_2_4', $locale),
                default => Locale::get('days_ago', $locale),
            };
        } else if ($diff->h > 0) {
            $elapsed .= $diff->h . ' ';

            $elapsed .= match (abs($diff->h % 10)) {
                1 => Locale::get('hour_ago', $locale),
                2, 3, 4 => Locale::get('hours_ago_2_4', $locale),
                default => Locale::get('hours_ago', $locale),
            };
        } else if ($diff->i > 0) {
            $elapsed .= $diff->i . ' ';

            $elapsed .= match (abs($diff->i % 10)) {
                1 => Locale::get('minute_ago', $locale),
                2, 3, 4 => Locale::get('minutes_ago_2_4', $locale),
                default => Locale::get('minutes_ago', $locale),
            };
        } else if ($diff->s > 0) {
            $elapsed .= $diff->s . ' ';

            $elapsed .= match (abs($diff->s % 10)) {
                1 => Locale::get('second_ago', $locale),
                2, 3, 4 => Locale::get('seconds_ago_2_4', $locale),
                default => Locale::get('seconds_ago', $locale),
            };
        }

        return $elapsed;
    }
}
