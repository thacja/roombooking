<?php

declare(strict_types=1);

namespace App\Config;

use SessionHandlerInterface;

return [
    'session' => [
        'name' => 'ROOMBOOKING_SESSION',
        'cookie_lifetime' => 0,
        'cookie_path' => '/',
        'cookie_domain' => '',
        'cookie_secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
        'gc_maxlifetime' => 7200,
    ],
];
