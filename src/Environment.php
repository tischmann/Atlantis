<?php

namespace Atlantis;

final class Environment
{
    public static function load(string $dir, string $file = '.env')
    {
        $path = rtrim($dir, DIRECTORY_SEPARATOR) . '/' . trim($file);

        if (file_exists($path)) {
            $lines = file($path, FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $line = trim($line);

                if (preg_match('/^([A-Z_0-9]+=.+)$/', $line)) {
                    putenv($line);
                }
            }
        }
    }
}
