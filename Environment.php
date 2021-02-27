<?php

namespace Atlantis;

final class Environment
{
    public string $path;

    public function __construct(string $dir, string $file = '.env')
    {
        $this->path = rtrim($dir, DIRECTORY_SEPARATOR) . '/' . trim($file);
    }

    public function load()
    {
        if (file_exists($this->path)) {
            $lines = file($this->path, FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $line = trim($line);

                if (preg_match('/^([A-Z_0-9]+=.+)$/', $line)) {
                    putenv($line);
                }
            }
        }
    }
}
