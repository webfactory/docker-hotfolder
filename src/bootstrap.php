<?php

// Basiert auf https://github.com/symfony/recipes/blob/1d26ca40a1b5ba8be7c67a25227c836f84ce2465/symfony/framework-bundle/4.2/config/bootstrap.php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';
if (!class_exists(Dotenv::class)) {
    throw new RuntimeException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
} else {
    // load all the .env files
    (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = (string) ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'development';
$_SERVER['APP_DEBUG'] = (string) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ('production' !== $_SERVER['APP_ENV'] ? 'true' : 'false'));
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], \FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
