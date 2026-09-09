<?php

declare(strict_types=1);

$svc = static fn(string $t, string $s): array => ['title' => $t, 'short_text' => $s];

return [
    'site_name' => 'Alexander M.',
    'site_role' => 'Çok yönlü IT uzmanı',
    'site_tagline' => 'Geliştirme, yönetim, DevOps ve güvenlik — anahtar teslim veya abonelik',
    'hero_offer' => 'Gereksiz bürokrasi olmadan işletmeler için IT çözümleri',
    'hero_sub' => 'Doğrudan çalışırım; aracı ve belirsiz süre yok. Net plan, şeffaf fiyat, sonuç sorumluluğu.',
    'services' => [
        'site-support' => $svc('Site bakımı', 'WordPress, Bitrix ve özel PHP: güncelleme, düzeltme, hız, yedek.'),
        'landing-dev' => $svc('Landing ve web servisleri', 'Temiz PHP 8.2 + MySQL: landing, panel, form, entegrasyon.'),
        'linux-admin' => $svc('Linux / VPS yönetimi', 'Apache, Nginx, PHP-FPM, MySQL ve hardening.'),
        'migrations-backups' => $svc('Migrasyon, yedek, izleme', 'Site taşıma, yedek scriptleri, Telegram uyarıları.'),
        'ssl-security' => $svc('SSL ve güvenlik', 'HTTPS, güvenlik duvarı, admin koruması, zafiyet denetimi.'),
        'crm-integrations' => $svc('CRM / ödeme / API entegrasyonları', 'Siteyi CRM, ödeme ve webhook’lara bağlama.'),
        'telegram-bots' => $svc('Telegram botları ve bildirimler', 'Lead, uyarı ve sipariş durumu botları.'),
        'seo-audit' => $svc('SEO denetimi', 'İndeksleme, hız, işaretleme, sitemap, Core Web Vitals.'),
        'automation' => $svc('Rutin otomasyon', 'Script, cron, rapor ve senkronizasyon.'),
        'consulting' => $svc('Danışmanlık ve altyapı denetimi', 'Yığın, risk ve iyileştirme planı incelemesi.'),
        'subscription-support' => $svc('Abonelik IT desteği', 'Sabit saat paketi: olaylar, küçük işler, tavsiye.'),
        'wordpress-bitrix' => $svc('WordPress / Bitrix anahtar teslim', 'Kurulum, özelleştirme, güvenlik ve hız.'),
    ],
    'packages' => [
        'Старт' => ['title' => 'Başlangıç', 'description' => 'Küçük site veya ilk altyapı kurulumu için.', 'features' => ['Mevcut durum denetimi', '1–2 haftalık plan', '10 saate kadar iş', 'Temel yedek & SSL', 'Kısa rapor']],
        'Бизнес' => ['title' => 'İş', 'description' => 'Düzenli işleri olan işletmeler için ideal.', 'features' => ['Başlangıçtaki her şey', 'Entegrasyon & otomasyon', 'İzleme + Telegram', '2 saat öncelikli yanıt', 'Erişim dokümantasyonu']],
        'Под ключ' => ['title' => 'Anahtar teslim', 'description' => 'Geliştirme/refaktör + altyapı + lansman desteği.', 'features' => ['Tam teslimat döngüsü', 'Sunucu + uygulama + SEO temeli', 'Güvenlik & hardening', 'Devretme eğitimi', 'Lansman sonrası 1 ay destek']],
    ],
    'faq' => [
        ['q' => 'Ne kadar hızlı yanıt veriyorsunuz?', 'a' => 'İş günlerinde genelde 1–2 saat içinde.'],
        ['q' => 'Uzaktan mı, yerinde mi?', 'a' => 'Çoğunlukla uzaktan. Moskova’da anlaşmaya göre ziyaret mümkün.'],
        ['q' => 'Tek seferlik işle başlanabilir mi?', 'a' => 'Evet. Sık yol: denetim → düzeltme → isteğe bağlı abonelik.'],
        ['q' => 'Hangi yığınları alıyorsunuz?', 'a' => 'PHP/MySQL, Linux, Apache/Nginx, WordPress, Bitrix, Telegram botları, API, temel güvenlik ve SEO.'],
        ['q' => 'Ödeme nasıl?', 'a' => 'Tek seferlik işler teklifle. Paket ve abonelik sabit fiyatla.'],
        ['q' => 'Erişim ve doküman veriyor musunuz?', 'a' => 'Evet. Erişimler, kısa değişiklik özeti ve işletim önerileri.'],
    ],
];
