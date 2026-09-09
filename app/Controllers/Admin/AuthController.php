<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\LoginAttempt;

final class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            redirect('/admin');
        }
        View::render('admin/auth/login', [
            'error' => flash('error'),
            'title' => 'Вход в админку',
        ], 'admin/layouts/auth');
    }

    public function login(): void
    {
        Csrf::requireValid();
        $login = trim((string)Request::input('login', ''));
        $password = (string)Request::input('password', '');
        $ip = Request::ip();

        $lockCfg = \App\Core\Config::get('login_lock', ['max' => 5, 'minutes' => 15]);
        if (LoginAttempt::isBlocked($ip, (int)$lockCfg['max'], (int)$lockCfg['minutes'])) {
            flash('error', 'Слишком много неудачных попыток. Подождите 15 минут.');
            redirect('/admin/login');
        }

        if (Auth::attempt($login, $password, $ip)) {
            redirect('/admin');
        }

        flash('error', 'Неверный логин или пароль');
        redirect('/admin/login');
    }

    public function logout(): void
    {
        Csrf::requireValid();
        Auth::logout();
        redirect('/admin/login');
    }
}
