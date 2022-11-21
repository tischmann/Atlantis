<?php

declare(strict_types=1);

use Tischmann\Atlantis\Autoloader;

require_once __DIR__ . "/../core/Autoloader.php";

spl_autoload_register("Tischmann\Atlantis\Autoloader::load");

// Подключаем базовые классы приложения
Autoloader::add('App', 'app');

Autoloader::add('Tischmann\Atlantis\Migrations', 'database');

// Подключаем классы фреймворка
Autoloader::add('Tischmann\Atlantis', 'core');

// Подключаем дополнительные классы

// Подключение автозагрузчика зависимостей Composer
if (file_exists('../vendor/autoloader.php')) {
    require_once '../vendor/autoloader.php';
}
