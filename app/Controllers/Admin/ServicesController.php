<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Service;

final class ServicesController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('admin/services/index', [
            'title' => 'Услуги',
            'services' => Service::all(),
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function create(): void
    {
        Auth::requireLogin();
        View::render('admin/services/form', [
            'title' => 'Новая услуга',
            'service' => null,
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
            redirect('/admin/services/create');
        }
        if ($data['slug'] === '' || Service::slugExists($data['slug'])) {
            $data['slug'] = slugify($data['title']) . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
        }
        Service::create($data);
        flash('ok', 'Услуга создана');
        redirect('/admin/services');
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        $service = Service::find((int)$id);
        if (!$service) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        View::render('admin/services/form', [
            'title' => 'Редактирование услуги',
            'service' => $service,
            'error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $service = Service::find((int)$id);
        if (!$service) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        $data = $this->payload();
        if ($data['title'] === '') {
            flash('error', 'Укажите название');
            redirect('/admin/services/' . (int)$id . '/edit');
        }
        if ($data['slug'] === '') {
            $data['slug'] = slugify($data['title']);
        }
        if (Service::slugExists($data['slug'], (int)$id)) {
            flash('error', 'Такой slug уже существует');
            redirect('/admin/services/' . (int)$id . '/edit');
        }
        Service::update((int)$id, $data);
        flash('ok', 'Сохранено');
        redirect('/admin/services');
    }

    public function delete(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        Service::delete((int)$id);
        flash('ok', 'Удалено');
        redirect('/admin/services');
    }

    public function reorder(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $ids = Request::input('ids', []);
        if (!is_array($ids)) {
            View::json(['ok' => false], 422);
            return;
        }
        Service::reorder($ids);
        View::json(['ok' => true]);
    }

    private function payload(): array
    {
        $price = Request::input('price_from');
        return [
            'slug' => trim((string)Request::input('slug', '')),
            'title' => trim((string)Request::input('title', '')),
            'short_text' => trim((string)Request::input('short_text', '')),
            'full_text' => trim((string)Request::input('full_text', '')),
            'price_from' => $price === '' || $price === null ? null : (float)$price,
            'price_note' => trim((string)Request::input('price_note', '')) ?: null,
            'period' => in_array(Request::input('period'), ['one_time', 'monthly', 'custom'], true)
                ? (string)Request::input('period') : 'one_time',
            'icon' => trim((string)Request::input('icon', 'code')) ?: 'code',
            'sort_order' => (int)Request::input('sort_order', 0),
            'is_active' => Request::input('is_active') ? 1 : 0,
            'is_featured' => Request::input('is_featured') ? 1 : 0,
            'cta_label' => trim((string)Request::input('cta_label', 'Заказать')) ?: 'Заказать',
        ];
    }
}
