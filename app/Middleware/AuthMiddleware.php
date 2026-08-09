<?php

declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    public static function guest(callable $callback): void
    {
        if (auth() !== null) {
            redirect(base_path('/dashboard'));
            return;
        }

        $callback();
    }

    public static function auth(callable $callback, array $roles = []): void
    {
        if (auth() === null) {
            flash('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
            redirect(base_path('/login'));
            return;
        }

        if (!empty($roles) && !in_array(auth()['role'], $roles, true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้าเพจนี้');
        }

        $callback();
    }

    public static function admin(callable $callback): void
    {
        self::auth($callback, ['admin']);
    }

    public static function manager(callable $callback): void
    {
        self::auth($callback, ['admin', 'manager']);
    }
}
