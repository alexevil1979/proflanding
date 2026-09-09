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

        if (trim((string)Request::input('website', '')) !== '') {
            View::json(['ok' => true, 'message' => __('lead_ok')]);
            return;
        }

        $ip = Request::ip();
        $limit = Config::get('rate_limit', ['max' => 5, 'minutes' => 10]);
        if (Lead::countRecentByIp($ip, (int)$limit['minutes']) >= (int)$limit['max']) {
            View::json(['ok' => false, 'error' => __('rate_limited')], 429);
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

        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = __('required_name');
        }
        if ($data['phone'] === '') {
            $errors['phone'] = __('required_phone');
        } else {
            $digits = preg_replace('/\D/', '', $data['phone']) ?? '';
            if (strlen($digits) < 10) {
                $errors['phone'] = __('invalid_phone');
            }
        }
        if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = __('invalid_email');
        }
        if (!$data['consent']) {
            $errors['consent'] = __('required_consent');
        }

        if ($errors !== []) {
            View::json(['ok' => false, 'error' => (string)reset($errors), 'errors' => $errors], 422);
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
        }

        View::json(['ok' => true, 'message' => __('lead_ok')]);
    }

    private function utm(string $key): ?string
    {
        $v = trim((string)Request::input($key, ''));
        return $v !== '' ? substr($v, 0, 120) : null;
    }
}
