<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Lead;

final class LeadsController
{
    public function index(): void
    {
        Auth::requireLogin();
        $status = Request::input('status');
        $status = is_string($status) && $status !== '' ? $status : null;
        View::render('admin/leads/index', [
            'title' => 'Заявки',
            'leads' => Lead::list($status, 200),
            'status' => $status,
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $lead = Lead::find((int)$id);
        if (!$lead) {
            http_response_code(404);
            echo 'Заявка не найдена';
            return;
        }
        View::render('admin/leads/show', [
            'title' => 'Заявка #' . $lead['id'],
            'lead' => $lead,
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function update(string $id): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $lead = Lead::find((int)$id);
        if (!$lead) {
            http_response_code(404);
            echo 'Заявка не найдена';
            return;
        }
        $status = (string)Request::input('status', 'new');
        if (!in_array($status, ['new', 'in_progress', 'done', 'spam'], true)) {
            $status = 'new';
        }
        $note = trim((string)Request::input('admin_note', ''));
        Lead::updateStatus((int)$id, $status, $note !== '' ? $note : null);
        flash('ok', 'Заявка обновлена');
        redirect('/admin/leads/' . (int)$id);
    }
}
