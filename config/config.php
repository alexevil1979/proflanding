<?php

declare(strict_types=1);

return [
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'url' => rtrim($_ENV['APP_URL'] ?? '', '/'),
    'key' => $_ENV['APP_KEY'] ?? '',
    'timezone' => 'Europe/Moscow',
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'name' => $_ENV['DB_NAME'] ?? 'proflanding',
        'user' => $_ENV['DB_USER'] ?? '',
        'pass' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
    ],
    'smtp' => [
        'host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
        'port' => (int)($_ENV['SMTP_PORT'] ?? 587),
        'secure' => $_ENV['SMTP_SECURE'] ?? 'tls',
        'user' => $_ENV['SMTP_USER'] ?? '',
        'pass' => $_ENV['SMTP_PASS'] ?? '',
        'from' => $_ENV['SMTP_FROM'] ?? '',
        'from_name' => $_ENV['SMTP_FROM_NAME'] ?? 'IT Specialist',
        'to' => $_ENV['SMTP_TO'] ?? '',
    ],
    'telegram' => [
        'token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? '',
        'chat_id' => $_ENV['TELEGRAM_CHAT_ID'] ?? '',
        'enabled' => (($_ENV['TELEGRAM_ENABLED'] ?? '1') === '1'),
    ],
    'mail_enabled' => (($_ENV['MAIL_ENABLED'] ?? '1') === '1'),
    'rate_limit' => [
        'max' => 5,
        'minutes' => 10,
    ],
    'login_lock' => [
        'max' => 5,
        'minutes' => 15,
    ],
];
