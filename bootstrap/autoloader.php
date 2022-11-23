<?php

declare(strict_types=1);

use Tischmann\Atlantis\Autoloader;

require_once __DIR__ . "/../core/Autoloader.php";

spl_autoload_register("Tischmann\Atlantis\Autoloader::load");

// Подключаем классы фреймворка
Autoloader::add('Tischmann\Atlantis', 'core');

// Подключаем базовые классы приложения
Autoloader::add('App', 'app');

// Подключаем классы базы данных
Autoloader::add('Tischmann\Atlantis\Migrations', 'database');

// Подключение автозагрузчика зависимостей Composer
if (file_exists(__DIR__ . '/../vendor/autoloader.php')) {
    require_once __DIR__ . '/../vendor/autoloader.php';
}
