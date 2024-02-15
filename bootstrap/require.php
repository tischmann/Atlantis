<?php

declare(strict_types=1);

if (version_compare(phpversion(), '8.3', '<')) {
    die("Требуется PHP версии 8.3 или выше. Текущая версия: " . phpversion());
}

$extensions = [
    'session',
    'pdo_mysql',
    'intl',
    'mbstring',
    'json',
    'zlib',
    'curl',
    'xml',
];

foreach ($extensions as $extension) {
    if (extension_loaded($extension)) continue;
    die("Требуется расширение PHP - {$extension}");
}
