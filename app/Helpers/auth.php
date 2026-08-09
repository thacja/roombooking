<?php

declare(strict_types=1);

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('check')) {
    function check(): bool
    {
        return isset($_SESSION['user']);
    }
}

if (!function_exists('user')) {
    function user(string $key = null): mixed
    {
        $user = auth();
        if ($user === null) {
            return null;
        }

        return $key ? ($user[$key] ?? null) : $user;
    }
}

if (!function_exists('id')) {
    function id(): ?int
    {
        return (int) (user('id') ?? 0);
    }
}

if (!function_exists('role')) {
    function role(): ?string
    {
        return user('role');
    }
}

if (!function_exists('hasRole')) {
    function hasRole(string ...$roles): bool
    {
        $currentRole = role();
        if ($currentRole === null) {
            return false;
        }

        return in_array($currentRole, $roles, true);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return hasRole('admin');
    }
}

if (!function_exists('isManager')) {
    function isManager(): bool
    {
        return hasRole('admin', 'manager');
    }
}

if (!function_exists('guest')) {
    function guest(): bool
    {
        return !check();
    }
}

if (!function_exists('attempt')) {
    function attempt(string $email, string $password): bool
    {
        $db = \App\Database\Database::getInstance();
        $user = $db->fetch(
            'SELECT id, email, password_hash, full_name, role, department, is_active 
             FROM users 
             WHERE email = :email 
             LIMIT 1',
            ['email' => $email]
        );

        if ($user === false || (int) $user['is_active'] !== 1) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        unset($user['password_hash']);

        $_SESSION['user'] = $user;
        $_SESSION['regenerated'] = true;

        return true;
    }
}

if (!function_exists('login')) {
    function login(int $userId, bool $remember = false): void
    {
        $db = \App\Database\Database::getInstance();
        $user = $db->fetch(
            'SELECT id, email, password_hash, full_name, role, department, is_active 
             FROM users 
             WHERE id = :id 
             LIMIT 1',
            ['id' => $userId]
        );

        if ($user === false || (int) $user['is_active'] !== 1) {
            return;
        }

        unset($user['password_hash']);

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        $_SESSION['user'] = $user;
        $_SESSION['regenerated'] = true;
    }
}

if (!function_exists('logout')) {
    function logout(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }

            session_destroy();
        }
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
