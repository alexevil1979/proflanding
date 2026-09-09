<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Currency;
use App\Core\Request;
use App\Core\View;
use App\Models\Setting;

final class SettingsController
{
    public function edit(): void
    {
        Auth::requireLogin();
        View::render('admin/settings/edit', [
            'title' => 'Контент сайта',
            'settings' => Setting::all(),
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $keys = [
            'public_url', 'site_name', 'site_name_latin', 'site_role', 'site_tagline', 'hero_offer', 'hero_sub',
            'phone', 'email', 'telegram', 'whatsapp', 'city',
            'experience_years', 'projects_count', 'response_hours',
            'work_format', 'response_sla', 'not_doing',
            'yandex_metrika', 'google_analytics', 'faq_json', 'og_image',
        ];
        $pairs = [];
        foreach ($keys as $key) {
            $val = trim((string)Request::input($key, ''));
            if ($key === 'experience_years') {
                $val = preg_replace('/\++$/', '+', $val) ?? $val;
                if ($val !== '' && !str_ends_with($val, '+') && ctype_digit($val)) {
                    $val .= '+';
                }
            }
            if ($key === 'public_url' && $val !== '') {
                $val = rtrim($val, '/');
            }
            $pairs[$key] = $val;
        }

        $uploaded = $this->storeUpload('avatar');
        if ($uploaded) {
            $pairs['avatar_path'] = $uploaded;
            if (($pairs['og_image'] ?? '') === '') {
                $pairs['og_image'] = $uploaded;
            }
        }

        $usd = trim((string)Request::input('usd_rate', ''));
        if ($usd !== '' && is_numeric($usd)) {
            try {
                Currency::setManual((float)$usd);
            } catch (\Throwable $e) {
                flash('error', $e->getMessage());
                redirect('/admin/settings');
            }
        }
        Setting::setMany($pairs);
        flash('ok', 'Настройки сохранены');
        redirect('/admin/settings');
    }

    public function refreshUsd(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        try {
            $r = Currency::refreshFromCbr();
            flash('ok', 'Курс USD обновлён: ' . $r['rate']);
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }
        redirect('/admin/settings');
    }

    private function storeUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['tmp_name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
            return null;
        }
        $code = (int)($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($code !== UPLOAD_ERR_OK) {
            flash('error', 'Ошибка загрузки файла (код ' . $code . '). Проверьте upload_max_filesize в PHP.');
            redirect('/admin/settings');
        }
        if (($_FILES[$field]['size'] ?? 0) > 3 * 1024 * 1024) {
            flash('error', 'Файл больше 3 МБ');
            redirect('/admin/settings');
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string)$finfo->file($_FILES[$field]['tmp_name']);
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($map[$mime])) {
            flash('error', 'Допустимы только JPG/PNG/WebP (сейчас: ' . $mime . ')');
            redirect('/admin/settings');
        }
        $name = bin2hex(random_bytes(12)) . '.' . $map[$mime];
        $dir = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            flash('error', 'Нет каталога uploads и не удалось создать: ' . $dir);
            redirect('/admin/settings');
        }
        if (!is_writable($dir)) {
            flash('error', 'Каталог uploads недоступен для записи. Выполните: chown -R www-data:www-data public/uploads && chmod 775 public/uploads');
            redirect('/admin/settings');
        }
        $dest = $dir . DIRECTORY_SEPARATOR . $name;
        if (!@move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
            flash('error', 'Не удалось сохранить файл в ' . $dir . ' (права PHP-FPM)');
            redirect('/admin/settings');
        }
        @chmod($dest, 0644);
        return '/uploads/' . $name;
    }
}
