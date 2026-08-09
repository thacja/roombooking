<?php

declare(strict_types=1);

namespace App\Config;

use App\Database\Database;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$driver = $_ENV['DB_DRIVER'] ?? 'sqlite';

if ($driver === 'sqlite') {
    $dsn = 'sqlite:' . ($_ENV['DB_DATABASE'] ?? __DIR__ . '/../../storage/database/database.sqlite');
} else {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $_ENV['DB_HOST'] ?? '127.0.0.1',
        $_ENV['DB_PORT'] ?? '3306',
        $_ENV['DB_DATABASE'] ?? 'roombooking'
    );
}

return [
    'database' => [
        'driver' => $driver,
        'dsn' => $dsn,
        'username' => $_ENV['DB_USERNAME'] ?? null,
        'password' => $_ENV['DB_PASSWORD'] ?: null,
    ],

    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Room Booking System',
        'url' => $_ENV['APP_URL'] ?? 'http://localhost:8000',
        'env' => $_ENV['APP_ENV'] ?? 'local',
    ],
];
