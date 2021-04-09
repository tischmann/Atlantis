<?php

namespace Atlantis;

final class Autoloader
{
    protected static $prefixes = [];

    public static function register()
    {
        spl_autoload_register(['static', 'loadClass']);
    }

    public static function addNamespace(string $prefix, string $dir, bool $prepend = false)
    {
        $prefix = trim($prefix, '\\') . '\\';

        $dir = rtrim($dir, DIRECTORY_SEPARATOR) . '/';

        if (isset(static::$prefixes[$prefix]) === false) {
            static::$prefixes[$prefix] = [];
        }

        if ($prepend) {
            array_unshift(static::$prefixes[$prefix], $dir);
        } else {
            array_push(static::$prefixes[$prefix], $dir);
        }
    }

    protected static function loadClass(string $class): bool
    {
        $prefix = $class;

        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relativeClass = substr($class, $pos + 1);
            $loaded = self::loadMappedFile($prefix, $relativeClass);

            if ($loaded) {
                return true;
            }

            $prefix = rtrim($prefix, '\\');
        }

        return false;
    }

    protected static function loadMappedFile(string $prefix, string $relativeClass): bool
    {
        if (isset(static::$prefixes[$prefix]) === false) {
            return false;
        }

        foreach (static::$prefixes[$prefix] as $dir) {
            $path = $dir . str_replace('\\', '/', $relativeClass) . '.php';
            return self::requireFile($path);
        }

        return false;
    }

    protected static function requireFile($file): bool
    {
        if (file_exists($file)) {
            require $file;
            return true;
        }

        return false;
    }
}
