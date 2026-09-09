<?php

declare(strict_types=1);

return [
    'site_role' => 'Multi-profile IT specialist',
    'site_tagline' => 'Development, administration, DevOps and security — turnkey or subscription',
    'hero_offer' => 'IT solutions for business without unnecessary bureaucracy',
    'hero_sub' => 'I work directly, no middlemen or vague deadlines. Clear plan, transparent price, ownership of the result.',
    'city' => 'Moscow / remote',
    'services' => [
        'site-support' => [
            'title' => 'Website maintenance',
            'short_text' => 'WordPress, Bitrix and custom PHP sites: updates, fixes, speed, backups.',
        ],
        'landing-dev' => [
            'title' => 'Landing pages & web services',
            'short_text' => 'Clean PHP 8.2 + MySQL: landings, cabinets, forms, integrations without heavy frameworks.',
        ],
        'linux-admin' => [
            'title' => 'Linux / VPS administration',
            'short_text' => 'Apache, Nginx, PHP-FPM, MySQL, permissions, users, autostart and hardening.',
        ],
        'migrations-backups' => [
            'title' => 'Migrations, backups, monitoring',
            'short_text' => 'Site moves with minimal downtime, backup scripts, Telegram alerts.',
        ],
        'ssl-security' => [
            'title' => 'SSL, security, hardening',
            'short_text' => 'HTTPS, firewall, permissions, admin protection, CMS vulnerability audit.',
        ],
        'crm-integrations' => [
            'title' => 'CRM / payments / API integrations',
            'short_text' => 'Connect the site to CRM, payment gateways, webhooks and internal systems.',
        ],
        'telegram-bots' => [
            'title' => 'Telegram bots & notifications',
            'short_text' => 'Bots for leads, alerts, order statuses and internal workflows.',
        ],
        'seo-audit' => [
            'title' => 'SEO audit & technical SEO',
            'short_text' => 'Indexing, speed, markup, sitemap, robots, meta, Core Web Vitals.',
        ],
        'automation' => [
            'title' => 'Routine automation',
            'short_text' => 'Scripts, cron, reports, syncs — less manual work.',
        ],
        'consulting' => [
            'title' => 'Consulting & infra audit',
            'short_text' => 'Review of stack, risks, bottlenecks and improvement plan.',
        ],
        'subscription-support' => [
            'title' => 'Subscription IT support',
            'short_text' => 'Fixed hours package: incidents, small changes, advice.',
        ],
        'wordpress-bitrix' => [
            'title' => 'WordPress / Bitrix turnkey',
            'short_text' => 'Build, customization, security and speed-up for popular CMS.',
        ],
    ],
    'packages' => [
        'Старт' => [
            'title' => 'Start',
            'description' => 'For a small site or first infrastructure setup.',
            'features' => ['Current-state audit', '1–2 week action plan', 'Up to 10 hours of work', 'Basic backups & SSL', 'Short report'],
        ],
        'Бизнес' => [
            'title' => 'Business',
            'description' => 'Best fit for an operating business with regular tasks.',
            'features' => ['Everything in Start', 'Integrations & automation', 'Monitoring + Telegram alerts', '2-hour priority response', 'Access documentation'],
        ],
        'Под ключ' => [
            'title' => 'Turnkey',
            'description' => 'Full cycle: development/refactor + infrastructure + launch support.',
            'features' => ['Full delivery cycle', 'Server + app + SEO baseline', 'Security & hardening', 'Handover training', 'One month post-launch support'],
        ],
    ],
    'faq' => [
        ['q' => 'How fast do you reply?', 'a' => 'Usually within 1–2 hours on business days. Urgent subscription incidents follow the agreed SLA.'],
        ['q' => 'Remote or on-site?', 'a' => 'Mostly remote. On-site visits in Moscow are possible by agreement.'],
        ['q' => 'Can we start with a one-off task?', 'a' => 'Yes. Common path: audit → fix → optional subscription support.'],
        ['q' => 'Which stacks and CMS do you take?', 'a' => 'PHP/MySQL, Linux, Apache/Nginx, WordPress, Bitrix, Telegram bots, API integrations, basic security and SEO.'],
        ['q' => 'How is payment handled?', 'a' => 'One-off tasks by estimate. Packages and subscriptions — fixed price with clear scope.'],
        ['q' => 'Do you provide access and docs?', 'a' => 'Yes. I hand over access, a short change log and operating recommendations.'],
    ],
];
