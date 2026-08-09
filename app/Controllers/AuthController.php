<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\session;
use App\Middleware\AuthMiddleware;

class AuthController
{
    public static function showLogin(): void
    {
        AuthMiddleware::guest(function () {
            $data = [
                'title' => 'เข้าสู่ระบบ',
                'csrf' => csrf_field(),
            ];

            extract($data);

            require __DIR__ . '/../Views/auth/login.php';
        });
    }

    public static function login(): void
    {
        AuthMiddleware::guest(function () {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect(base_path('/login'));
                return;
            }

            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $csrfToken = (string) ($_POST['csrf_token'] ?? '');

            if (!verify_csrf($csrfToken)) {
                flash('error', 'Invalid CSRF token');
                redirect(base_path('/login'));
                return;
            }

            if (empty($email) || empty($password)) {
                flash('error', 'กรุณากรอกอีเมลและรหัสผ่าน');
                redirect(base_path('/login'));
                return;
            }

            if (!attempt($email, $password)) {
                flash('error', 'อีเมลหรือรหัสผ่านไม่ถูกต้อง');
                redirect(base_path('/login'));
                return;
            }

            flash('success', 'เข้าสู่ระบบสำเร็จ');
            redirect(base_path('/dashboard'));
        });
    }

    public static function logout(): void
    {
        logout();
        flash('success', 'ออกจากระบบสำเร็จ');
        redirect(base_path('/login'));
    }
}
