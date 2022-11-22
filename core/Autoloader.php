<?php

declare(strict_types=1);

namespace Tischmann\Atlantis;

final class Autoloader
{
    private const NAMESPACE_SEPARATOR = "\\";

    private static array $classes = []; // Подключённые пространства имен

    private function __construct()
    {
    }

    /**
     * Добавление класса в список автозагрузки
     *
     * @param string $namespace Пространство имен
     * @param string $path Путь к директории с классами
     * @return void
     */
    public static function add(string $namespace, string $path): void
    {
        $namespace = trim($namespace, static::NAMESPACE_SEPARATOR)
            . static::NAMESPACE_SEPARATOR;

        static::$classes[$namespace] ??= [];

        $path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        static::$classes[$namespace][] =  $path;
    }

    /**
     * Функция автозагрузки классов
     *
     * @param string $className Имя класса
     * @return void
     */
    public static function load(string $className): void
    {
        $root = dirname(__FILE__, 2);

        $className = trim($className, static::NAMESPACE_SEPARATOR);

        $namespace = $className;

        $found = false;

        $path = '';

        while (false !== $pos = strrpos($namespace, static::NAMESPACE_SEPARATOR)) {
            $namespace = substr($className, 0, $pos + 1);

            $filename = substr($className, $pos + 1);

            $classes = static::$classes[$namespace] ?? [];

            foreach ($classes as $dir) {
                $filename = str_replace(
                    static::NAMESPACE_SEPARATOR,
                    DIRECTORY_SEPARATOR,
                    $filename
                );

                $path = "{$root}/{$dir}{$filename}.php";

                if (file_exists($path)) {
                    $found = true;
                    break 2;
                }
            }

            $namespace = rtrim($namespace, static::NAMESPACE_SEPARATOR);
        }

        if ($found) require_once $path;
        else throw new \Exception("Класс {$className} не найден");
    }
}
