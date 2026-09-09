<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\User;

final class PasswordController
{
    public function edit(): void
    {
        Auth::requireLogin();
        View::render('admin/password/edit', [
            'title' => 'Смена пароля',
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $user = Auth::user();
        if (!$user) {
            redirect('/admin/login');
        }
        $current = (string)Request::input('current_password', '');
        $new = (string)Request::input('new_password', '');
        $confirm = (string)Request::input('confirm_password', '');

        if (!password_verify($current, $user['password_hash'])) {
            flash('error', 'Текущий пароль неверен');
            redirect('/admin/password');
        }
        if (strlen($new) < 8) {
            flash('error', 'Новый пароль — минимум 8 символов');
            redirect('/admin/password');
        }
        if ($new !== $confirm) {
            flash('error', 'Пароли не совпадают');
            redirect('/admin/password');
        }
        User::updatePassword((int)$user['id'], password_hash($new, PASSWORD_DEFAULT));
        flash('ok', 'Пароль обновлён');
        redirect('/admin/password');
    }
}
