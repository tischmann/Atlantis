<?php

declare(strict_types=1);

if (version_compare(phpversion(), '8.2', '<')) {
    die("Требуется PHP версии 8.2 или выше. Установленная версия: " . phpversion());
}

$extensions = [
    'session',
    'pdo_mysql',
    'intl',
    'mbstring',
    'json',
    'zlib',
    'curl',
    'memcache',
    'xml',
];

foreach ($extensions as $extension) {
    if (extension_loaded($extension)) continue;
    die("Требуется расширение PHP - {$extension}");
}
