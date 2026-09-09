<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Csrf;
use App\Core\Config;
use App\Core\Request;
use App\Core\Validator;
use App\Core\View;
use App\Models\Lead;
use App\Models\Package;
use App\Models\Service;
use App\Services\Notify;

final class LeadController
{
    public function store(): void
    {
        Csrf::requireValid();

        // Honeypot
        if (trim((string)Request::input('website', '')) !== '') {
            View::json(['ok' => true, 'message' => 'Спасибо! Заявка отправлена.']);
            return;
        }

        $ip = Request::ip();
        $limit = Config::get('rate_limit', ['max' => 5, 'minutes' => 10]);
        if (Lead::countRecentByIp($ip, (int)$limit['minutes']) >= (int)$limit['max']) {
            View::json(['ok' => false, 'error' => 'Слишком много заявок. Попробуйте позже.'], 429);
            return;
        }

        $data = [
            'name' => trim((string)Request::input('name', '')),
            'phone' => trim((string)Request::input('phone', '')),
            'email' => trim((string)Request::input('email', '')),
            'messenger' => trim((string)Request::input('messenger', '')),
            'service_id' => Request::input('service_id') ?: null,
            'package_id' => Request::input('package_id') ?: null,
            'message' => trim((string)Request::input('message', '')),
            'consent' => Request::input('consent'),
        ];

        $v = new Validator($data);
        $v->required('name', 'Имя')
            ->maxLen('name', 120, 'Имя')
            ->required('phone', 'Телефон')
            ->phone('phone', 'телефон')
            ->email('email', 'email', true)
            ->maxLen('message', 3000, 'Сообщение')
            ->accepted('consent', 'Нужно согласие на обработку персональных данных');

        if ($v->fails()) {
            View::json(['ok' => false, 'error' => $v->firstError(), 'errors' => $v->errors()], 422);
            return;
        }

        $serviceId = $data['service_id'] !== null && $data['service_id'] !== '' ? (int)$data['service_id'] : null;
        $packageId = $data['package_id'] !== null && $data['package_id'] !== '' ? (int)$data['package_id'] : null;
        if ($serviceId && !Service::find($serviceId)) {
            $serviceId = null;
        }
        if ($packageId && !Package::find($packageId)) {
            $packageId = null;
        }

        $leadId = Lead::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] !== '' ? $data['email'] : null,
            'messenger' => $data['messenger'] !== '' ? $data['messenger'] : null,
            'service_id' => $serviceId,
            'package_id' => $packageId,
            'message' => $data['message'] !== '' ? $data['message'] : null,
            'page_url' => substr((string)Request::input('page_url', app_url()), 0, 500),
            'utm_source' => $this->utm('utm_source'),
            'utm_medium' => $this->utm('utm_medium'),
            'utm_campaign' => $this->utm('utm_campaign'),
            'utm_content' => $this->utm('utm_content'),
            'utm_term' => $this->utm('utm_term'),
            'ip' => $ip,
            'user_agent' => Request::userAgent(),
            'status' => 'new',
        ]);

        try {
            Notify::leadCreated($leadId);
        } catch (\Throwable $e) {
            // заявка уже сохранена
        }

        View::json(['ok' => true, 'message' => 'Спасибо! Заявка отправлена. Я свяжусь с вами в ближайшее время.']);
    }

    private function utm(string $key): ?string
    {
        $v = trim((string)Request::input($key, ''));
        return $v !== '' ? substr($v, 0, 120) : null;
    }
}
