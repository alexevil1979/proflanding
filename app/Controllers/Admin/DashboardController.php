<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\View;
use App\Models\Lead;
use App\Models\NotificationLog;
use App\Models\Service;

final class DashboardController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('admin/dashboard/index', [
            'title' => 'Дашборд',
            'newLeads' => Lead::countByStatus('new'),
            'activeServices' => Service::countActive(),
            'latestLeads' => Lead::list(null, 8),
            'notifications' => NotificationLog::recent(10),
        ], 'admin/layouts/main');
    }
}
