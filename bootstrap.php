<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$sessionConfig = require __DIR__ . '/app/Config/session.php';

ini_set('session.use_strict_mode', $sessionConfig['session']['use_strict_mode'] ? '1' : '0');
ini_set('session.gc_maxlifetime', (string) $sessionConfig['session']['gc_maxlifetime']);

session_set_cookie_params([
    'lifetime' => $sessionConfig['session']['cookie_lifetime'],
    'path' => $sessionConfig['session']['cookie_path'],
    'domain' => $sessionConfig['session']['cookie_domain'],
    'secure' => $sessionConfig['session']['cookie_secure'],
    'httponly' => $sessionConfig['session']['cookie_httponly'],
    'samesite' => $sessionConfig['session']['cookie_samesite'],
]);

session_name($sessionConfig['session']['name']);
session_start();

if (!empty($_SESSION['regenerated'])) {
    unset($_SESSION['regenerated']);
} elseif (empty($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}

require_once __DIR__ . '/app/Helpers/functions.php';
require_once __DIR__ . '/app/Helpers/auth.php';
