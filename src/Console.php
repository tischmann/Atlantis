<?php

namespace Atlantis;

class Console
{
    public static function header(string $string, int $width = 80)
    {
        echo str_repeat(' ', floor($width / 2) - floor(strlen($string) / 2))
            . $string, PHP_EOL;
    }

    public static function line(string $string, string $end = 'OK', int $width = 80)
    {
        echo $string . str_repeat(' ', $width - strlen($string) - strlen($end))
            . $end, PHP_EOL;
    }

    public static function separator(int $width = 80, string $fill = '=')
    {
        echo str_repeat($fill, $width), PHP_EOL;
    }

    public static function progress($done, $total, $message = "", int $width = 80)
    {
        $perc = round(($done * 100) / $total);
        $line = $message . str_repeat(' ', $width - strlen($message) - strlen($perc) - 1);
        fwrite(STDERR, sprintf("%s%d%%\r", $line, $perc));
    }
}
