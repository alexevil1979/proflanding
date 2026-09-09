<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Portfolio;

final class PortfolioController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('admin/portfolio/index', [
            'title' => 'Кейсы',
            'items' => Portfolio::all(),
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function create(): void
    {
        Auth::requireLogin();
        View::render('admin/portfolio/form', [
            'title' => 'Новый кейс',
            'item' => null,
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
            redirect('/admin/portfolio/create');
        }
        Portfolio::create($data);
        flash('ok', 'Кейс создан');
        redirect('/admin/portfolio');
    }

    public function edit(string $id): void
    {
        Auth::requireLogin();
        $item = Portfolio::find((int)$id);
        if (!$item) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        View::render('admin/portfolio/form', [
            'title' => 'Редактирование кейса',
            'item' => $item,
            'error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        if (!Portfolio::find((int)$id)) {
            http_response_code(404);
            echo 'Не найдено';
            return;
        }
        $data = $this->payload();
        if ($data['title'] === '') {
            flash('error', 'Укажите название');
            redirect('/admin/portfolio/' . (int)$id . '/edit');
        }
        Portfolio::update((int)$id, $data);
        flash('ok', 'Сохранено');
        redirect('/admin/portfolio');
    }

    public function delete(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        Portfolio::delete((int)$id);
        flash('ok', 'Удалено');
        redirect('/admin/portfolio');
    }

    private function payload(): array
    {
        return [
            'title' => trim((string)Request::input('title', '')),
            'description' => trim((string)Request::input('description', '')),
            'stack' => trim((string)Request::input('stack', '')) ?: null,
            'result_text' => trim((string)Request::input('result_text', '')) ?: null,
            'url' => trim((string)Request::input('url', '')) ?: null,
            'image' => trim((string)Request::input('image', '')) ?: null,
            'is_active' => Request::input('is_active') ? 1 : 0,
            'sort_order' => (int)Request::input('sort_order', 0),
        ];
    }
}
