<?php


use core\Application;
use app\controllers\Site;
use app\controllers\Account;

// add this property to composer.json to enable autoloading of namespaced classes:
//
// "autoload": {
//    "psr-4": {"Namespace\\": "folder/"}
// }
//
// https://getcomposer.org/doc/01-basic-usage.md#autoloading
require_once __DIR__ . '/../vendor/autoload.php';

// load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD']
    ]
];

$app = new Application(dirname(__DIR__), $config);

// "::class" constant gets class name with its namespace
$app->route->path('/', [Site::class, 'home']);
// Apache server doesn't serve files at urls without question mark
// this url "/?signup" works and this "/signup" doesn't
// workarounds are creating an ".htaccess" file with rules overriding default behaviour
// or running PHP server via command line "php -S localhost:8000"
$app->route->path('/signup', [Account::class, 'signup']);
$app->route->path('/signup-success', [Account::class, 'signupSuccess']);
$app->route->path('/activate/{token:[\da-f]+}', [Account::class, 'activate']);
$app->route->path('/activate-success', [Account::class, 'activateSuccess']);
$app->route->path('/login', [Account::class, 'login']);
$app->route->path('/logout', [Account::class, 'logout']);
$app->route->path('/logout-success', [Account::class, 'logoutSuccess']);

$app->run();
