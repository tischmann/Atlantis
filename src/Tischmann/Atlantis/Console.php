<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

/**
 * Класс для работы с консолью
 * 
 * @author Yuriy Stolov <yuriystolov@gmail.com>
 */
final class Console
{
    /**
     * Блокируем конструктор
     */
    private function __construct()
    {
    }

    /**
     * Разбор аргументов командной строки
     * 
     * @param array $args Аргументы командной строки
     * @return array Команды и их аргументы
     */
    public static function parse(array $args): array
    {
        array_shift($args);

        preg_match_all(
            '/[\/-]?((\w+)(?:[=:]("[^"]+"|[^\s"]+))?)(?:\s+|$)/i',
            implode(' ', $args),
            $matches,
            PREG_SET_ORDER
        );

        $commands = [];

        foreach ($matches as $match) {
            $commands[$match[2]] = $match[3] ?? null;
        }

        return $commands;
    }

    /**
     * Возвращает ширину строки консоли
     * 
     * @return int Ширина строки консоли
     */
    public static function getLineWidth(): int
    {
        return intval(exec('tput cols'));
    }

    /**
     * Вывод информации в консоль
     * 
     * @param string $message Сообщение
     * @param string $postfix Постфикс
     */
    public static function print(
        string $message,
        string $postfix = "OK",
        bool $separate = false
    ): void {
        $width = static::getLineWidth();

        $length = $width - mb_strlen($postfix) - 3;

        $message = mb_strlen($message) > $length
            ? substr($message, 0, $length) . '...'
            : $message;

        $width = $width - mb_strlen($message) - mb_strlen($postfix);

        if ($separate) static::separator();

        echo $message . str_repeat(' ', $width) . $postfix, PHP_EOL;

        if ($separate) static::separator();
    }

    /**
     * Вывод разделителя в консоль
     */
    public static function separator()
    {
        echo str_repeat("=", static::getLineWidth()) . PHP_EOL;
    }

    /**
     * Вывод прогресса в консоль
     * 
     * @param int $current Текущее значение
     * @param int $total Общее значение
     * @param string $message Сообщение
     */
    public static function progress(
        int $current,
        int $total,
        string $message = "Обработка..."
    ) {
        $perc = $total ? round(($current * 100) / $total) : 0;

        $length = static::getLineWidth()
            - mb_strlen($message)
            - mb_strlen(strval($perc))
            - 1;

        $message = $message . str_repeat(' ', $length);

        fwrite(STDERR, sprintf("%s%d%%\r", $message, $perc));
    }
}
