<?php

declare(strict_types=1);

$svc = static fn(string $t, string $s): array => ['title' => $t, 'short_text' => $s];

return [
    'site_name' => 'Alexander M.',
    'site_role' => '多领域 IT 专家',
    'site_tagline' => '开发、运维、DevOps 与安全 — 交钥匙或订阅制',
    'hero_offer' => '为企业提供无冗余官僚流程的 IT 方案',
    'hero_sub' => '直接合作，无中介、无模糊工期。清晰计划、透明报价、对结果负责。',
    'services' => [
        'site-support' => $svc('网站维护', 'WordPress、Bitrix 与自研 PHP：更新、修复、加速、备份。'),
        'landing-dev' => $svc('落地页与 Web 服务', '纯 PHP 8.2 + MySQL：落地页、后台、表单与集成。'),
        'linux-admin' => $svc('Linux / VPS 运维', 'Apache、Nginx、PHP-FPM、MySQL 与加固。'),
        'migrations-backups' => $svc('迁移、备份、监控', '低停机迁移、备份脚本、Telegram 告警。'),
        'ssl-security' => $svc('SSL 与安全加固', 'HTTPS、防火墙、后台保护与漏洞审计。'),
        'crm-integrations' => $svc('CRM / 支付 / API 集成', '连接 CRM、支付与 Webhook。'),
        'telegram-bots' => $svc('Telegram 机器人与通知', '线索、告警与订单状态机器人。'),
        'seo-audit' => $svc('SEO 与技术审计', '收录、速度、结构化数据与 Core Web Vitals。'),
        'automation' => $svc('流程自动化', '脚本、cron、报表与同步。'),
        'consulting' => $svc('咨询与基建审计', '评估技术栈、风险与改进计划。'),
        'subscription-support' => $svc('订阅制 IT 支持', '固定工时：故障、小改、建议。'),
        'wordpress-bitrix' => $svc('WordPress / Bitrix 交钥匙', '搭建、定制、安全与加速。'),
    ],
    'packages' => [
        'Старт' => ['title' => '起步', 'description' => '适合小型站点或首次基建。', 'features' => ['现状审计', '1–2 周计划', '最多 10 小时', '基础备份与 SSL', '简要报告']],
        'Бизнес' => ['title' => '商业', 'description' => '适合有持续需求的运营业务。', 'features' => ['含起步全部', '集成与自动化', '监控 + Telegram', '2 小时优先响应', '权限文档']],
        'Под ключ' => ['title' => '交钥匙', 'description' => '开发/重构 + 基建 + 上线支持全流程。', 'features' => ['全流程交付', '服务器+应用+SEO基线', '安全加固', '交接培训', '上线后一个月支持']],
    ],
    'faq' => [
        ['q' => '多久回复？', 'a' => '工作日通常 1–2 小时内。'],
        ['q' => '远程还是现场？', 'a' => '以远程为主，莫斯科可协商上门。'],
        ['q' => '可以从单次任务开始吗？', 'a' => '可以。常见路径：审计 → 修复 → 可选订阅支持。'],
        ['q' => '承接哪些技术栈？', 'a' => 'PHP/MySQL、Linux、Apache/Nginx、WordPress、Bitrix、Telegram 机器人、API、基础安全与 SEO。'],
        ['q' => '如何付款？', 'a' => '单次按报价，套餐与订阅按固定价格与明确范围。'],
        ['q' => '是否提供权限与文档？', 'a' => '会。交付权限、变更摘要与运维建议。'],
    ],
];
