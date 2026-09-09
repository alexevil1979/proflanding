<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Package;

final class PackagesController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('admin/packages/index', [
            'title' => 'Пакеты',
            'packages' => Package::all(),
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function create(): void
    {
        Auth::requireLogin();
        View::render('admin/packages/form', [
            'title' => 'Новый пакет',
            'package' => null,
            'error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function store(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $data = $this->payload();
        if ($data['title'] === '') {
            flash('error', 'Укажите название');
            redirect('/admin/packages/create');
        }
        Package::create($data);
        flash('ok', 'Пакет создан');
        redirect('/admin/packages');
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        $package = Package::find((int)$id);
        if (!$package) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        View::render('admin/packages/form', [
            'title' => 'Редактирование пакета',
            'package' => $package,
            'error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        if (!Package::find((int)$id)) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        $data = $this->payload();
        if ($data['title'] === '') {
            flash('error', 'Укажите название');
            redirect('/admin/packages/' . (int)$id . '/edit');
        }
        Package::update((int)$id, $data);
        flash('ok', 'Сохранено');
        redirect('/admin/packages');
    }

    public function delete(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        Package::delete((int)$id);
        flash('ok', 'Удалено');
        redirect('/admin/packages');
    }

    private function payload(): array
    {
        $featuresRaw = trim((string)Request::input('features', ''));
        $features = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $featuresRaw) ?: [])));
        $price = Request::input('price');
        return [
            'title' => trim((string)Request::input('title', '')),
            'description' => trim((string)Request::input('description', '')),
            'price' => $price === '' || $price === null ? null : (float)$price,
            'price_note' => trim((string)Request::input('price_note', '')) ?: null,
            'features' => json_encode($features, JSON_UNESCAPED_UNICODE),
            'is_featured' => Request::input('is_featured') ? 1 : 0,
            'is_active' => Request::input('is_active') ? 1 : 0,
            'sort_order' => (int)Request::input('sort_order', 0),
            'cta_label' => trim((string)Request::input('cta_label', 'Выбрать пакет')) ?: 'Выбрать пакет',
        ];
    }
}
