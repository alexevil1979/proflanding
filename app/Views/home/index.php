<?php
/** @var array $seo */
/** @var array $services */
/** @var array $packages */
/** @var array $portfolio */
/** @var array $faq */
/** @var array $settings */
use App\Models\Package;
use App\Core\Csrf;

$h1 = $seo['h1'] ?? setting('hero_offer');
$name = setting('site_name', 'IT Specialist');
$role = setting('site_role');
$jsonLdPerson = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $name,
    'jobTitle' => $role,
    'url' => app_url(),
    'email' => setting('email'),
    'telephone' => setting('phone'),
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => setting('city')],
];
$offersClean = [];
foreach ($services as $s) {
    $o = [
        '@type' => 'Offer',
        'name' => $s['title'],
        'description' => $s['short_text'],
        'priceCurrency' => 'RUB',
        'url' => app_url('/#services'),
    ];
    if ($s['price_from'] !== null) {
        $o['price'] = (string)$s['price_from'];
    }
    $offersClean[] = $o;
}
$jsonLdService = [
    '@context' => 'https://schema.org',
    '@type' => 'ProfessionalService',
    'name' => $name . ' — ' . $role,
    'description' => setting('site_tagline'),
    'url' => app_url(),
    'areaServed' => setting('city'),
    'priceRange' => '₽₽',
];
$jsonLdCatalog = [
    '@context' => 'https://schema.org',
    '@type' => 'OfferCatalog',
    'name' => 'Услуги IT-специалиста',
    'itemListElement' => $offersClean,
];
$faqLd = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => []];
foreach ($faq as $item) {
    $faqLd['mainEntity'][] = [
        '@type' => 'Question',
        'name' => $item['q'] ?? '',
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a'] ?? ''],
    ];
}
?>
<script type="application/ld+json"><?= json_encode($jsonLdPerson, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script>
<script type="application/ld+json"><?= json_encode($jsonLdService, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script>
<script type="application/ld+json"><?= json_encode($jsonLdCatalog, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script>
<?php if ($faq): ?><script type="application/ld+json"><?= json_encode($faqLd, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) ?></script><?php endif; ?>

<section class="hero" aria-labelledby="hero-title">
    <div class="hero-bg" aria-hidden="true"></div>
    <div class="container hero-grid">
        <div class="hero-copy reveal">
            <p class="eyebrow"><?= e($name) ?> · <?= e($role) ?></p>
            <h1 id="hero-title"><?= e($h1) ?></h1>
            <p class="lead"><?= e(setting('hero_sub')) ?></p>
            <ul class="trust-bullets">
                <li>Прямая работа без агентства и накрутки сроков</li>
                <li>Понятная смета до старта, ответственность за результат</li>
                <li>PHP/Linux/DevOps/безопасность — в одних руках</li>
            </ul>
            <div class="hero-cta">
                <a class="btn btn-primary" href="#lead">Оставить заявку</a>
                <a class="btn btn-ghost" href="#services">Смотреть услуги</a>
            </div>
        </div>
        <div class="hero-visual reveal" aria-hidden="false">
            <div class="portrait-card">
                <svg class="portrait" viewBox="0 0 320 380" width="320" height="380" role="img" aria-label="Портрет IT-специалиста — заглушка">
                    <defs>
                        <linearGradient id="pg" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#0f766e"/>
                            <stop offset="100%" stop-color="#134e4a"/>
                        </linearGradient>
                    </defs>
                    <rect width="320" height="380" rx="24" fill="url(#pg)"/>
                    <circle cx="160" cy="140" r="58" fill="#ccfbf1" opacity=".9"/>
                    <rect x="70" y="220" width="180" height="120" rx="60" fill="#99f6e4" opacity=".85"/>
                    <text x="160" y="360" text-anchor="middle" fill="#ecfeff" font-size="14" font-family="Manrope,sans-serif">IT Specialist</text>
                </svg>
                <div class="portrait-meta">
                    <strong><?= e($name) ?></strong>
                    <span><?= e(setting('city')) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats" id="trust" aria-label="Показатели доверия">
    <div class="container stats-grid">
        <div class="stat reveal"><strong><?= e(setting('experience_years', '10')) ?>+</strong><span>лет опыта</span></div>
        <div class="stat reveal"><strong><?= e(setting('projects_count', '100+')) ?></strong><span>проектов и задач</span></div>
        <div class="stat reveal"><strong><?= e(setting('response_hours', '2')) ?> ч</strong><span>типовая реакция</span></div>
    </div>
</section>

<section class="section" id="services" aria-labelledby="services-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="services-title">Услуги</h2>
            <p>Активные направления: разработка, инфраструктура, безопасность, интеграции и поддержка.</p>
        </header>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <article class="service-card<?= !empty($service['is_featured']) ? ' is-featured' : '' ?> reveal">
                <div class="service-top">
                    <span class="service-icon" aria-hidden="true"><?= e(mb_substr((string)$service['icon'], 0, 1)) ?></span>
                    <?php if (!empty($service['is_featured'])): ?><span class="badge">В фокусе</span><?php endif; ?>
                </div>
                <h3><?= e($service['title']) ?></h3>
                <p><?= e($service['short_text']) ?></p>
                <div class="service-price">
                    <?php if ($service['price_from'] !== null): ?>
                        <strong>от <?= e(money((float)$service['price_from'])) ?></strong>
                    <?php else: ?>
                        <strong>по запросу</strong>
                    <?php endif; ?>
                    <span><?= e($service['price_note'] ?: period_label($service['period'])) ?></span>
                </div>
                <button type="button" class="btn btn-secondary btn-block js-order"
                        data-service="<?= (int)$service['id'] ?>"
                        data-package="">
                    <?= e($service['cta_label'] ?: 'Заказать') ?>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt" id="packages" aria-labelledby="packages-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="packages-title">Пакеты</h2>
            <p>Готовые форматы сотрудничества — от быстрого старта до внедрения под ключ.</p>
        </header>
        <div class="packages-grid">
            <?php foreach ($packages as $pkg): ?>
            <?php $features = Package::featuresList($pkg['features'] ?? null); ?>
            <article class="package-card<?= !empty($pkg['is_featured']) ? ' is-featured' : '' ?> reveal">
                <?php if (!empty($pkg['is_featured'])): ?><div class="package-ribbon">Оптимально</div><?php endif; ?>
                <h3><?= e($pkg['title']) ?></h3>
                <p><?= e($pkg['description'] ?? '') ?></p>
                <div class="package-price">
                    <?php if ($pkg['price'] !== null): ?><strong><?= e(money((float)$pkg['price'])) ?></strong><?php endif; ?>
                    <span><?= e($pkg['price_note'] ?? '') ?></span>
                </div>
                <ul>
                    <?php foreach ($features as $f): ?><li><?= e((string)$f) ?></li><?php endforeach; ?>
                </ul>
                <button type="button" class="btn <?= !empty($pkg['is_featured']) ? 'btn-primary' : 'btn-secondary' ?> btn-block js-order"
                        data-service="" data-package="<?= (int)$pkg['id'] ?>">
                    <?= e($pkg['cta_label'] ?: 'Выбрать') ?>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="process" aria-labelledby="process-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="process-title">Как работаем</h2>
            <p>Короткий прозрачный цикл без лишних совещаний.</p>
        </header>
        <ol class="steps">
            <li class="reveal"><span>01</span><h3>Заявка</h3><p>Коротко описываете задачу и желаемый результат.</p></li>
            <li class="reveal"><span>02</span><h3>Диагностика</h3><p>Уточняю объём, риски, сроки и фиксирую смету.</p></li>
            <li class="reveal"><span>03</span><h3>Реализация</h3><p>Делаю работы, держу в курсе статуса и сдаю результат.</p></li>
            <li class="reveal"><span>04</span><h3>Поддержка</h3><p>При необходимости подключаем абонентское сопровождение.</p></li>
        </ol>
    </div>
</section>

<section class="section section-alt" id="stack" aria-labelledby="stack-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="stack-title">Кейсы и стек</h2>
            <p>Практический стек для бизнеса: без моды ради моды.</p>
        </header>
        <?php if ($portfolio): ?>
        <div class="cases-grid">
            <?php foreach ($portfolio as $case): ?>
            <article class="case-card reveal">
                <h3><?= e($case['title']) ?></h3>
                <p><?= e($case['description'] ?? '') ?></p>
                <p class="muted"><?= e($case['stack'] ?? '') ?></p>
                <?php if (!empty($case['result_text'])): ?><p class="case-result"><?= e($case['result_text']) ?></p><?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <ul class="stack-list reveal">
            <li>PHP 8.2</li><li>MySQL</li><li>Apache</li><li>Linux</li><li>Git</li><li>Telegram bots</li><li>SEO</li><li>Nginx</li>
        </ul>
    </div>
</section>

<section class="section" id="faq" aria-labelledby="faq-title">
    <div class="container narrow">
        <header class="section-head reveal">
            <h2 id="faq-title">FAQ</h2>
            <p>Ответы на частые вопросы до старта работ.</p>
        </header>
        <div class="faq-list">
            <?php foreach ($faq as $i => $item): ?>
            <details class="faq-item reveal"<?= $i === 0 ? ' open' : '' ?>>
                <summary><?= e($item['q'] ?? '') ?></summary>
                <p><?= e($item['a'] ?? '') ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-cta" id="lead" aria-labelledby="lead-title">
    <div class="container lead-grid">
        <div class="reveal">
            <header class="section-head left">
                <h2 id="lead-title">Оставить заявку</h2>
                <p>Опишите задачу — отвечу с планом и ориентиром по срокам/бюджету.</p>
            </header>
            <div class="contact-chips">
                <?php if (setting('telegram')): ?><a href="<?= e(setting('telegram')) ?>" target="_blank" rel="noopener">Telegram</a><?php endif; ?>
                <?php if (setting('whatsapp')): ?><a href="<?= e(setting('whatsapp')) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
                <?php if (setting('phone')): ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a><?php endif; ?>
            </div>
        </div>
        <form class="lead-form reveal" id="leadForm" method="post" action="/lead" novalidate>
            <?= Csrf::field() ?>
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="page_url" value="<?= e(app_url('/')) ?>">
            <input type="hidden" name="utm_source" id="utm_source">
            <input type="hidden" name="utm_medium" id="utm_medium">
            <input type="hidden" name="utm_campaign" id="utm_campaign">
            <input type="hidden" name="utm_content" id="utm_content">
            <input type="hidden" name="utm_term" id="utm_term">

            <div class="form-row">
                <label for="name">Имя *</label>
                <input id="name" name="name" type="text" required maxlength="120" autocomplete="name">
            </div>
            <div class="form-row">
                <label for="phone">Телефон *</label>
                <input id="phone" name="phone" type="tel" required maxlength="40" autocomplete="tel">
            </div>
            <div class="form-row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" maxlength="160" autocomplete="email">
            </div>
            <div class="form-row two">
                <div>
                    <label for="service_id">Услуга</label>
                    <select id="service_id" name="service_id">
                        <option value="">Не выбрано</option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?= (int)$service['id'] ?>"><?= e($service['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="package_id">Пакет</label>
                    <select id="package_id" name="package_id">
                        <option value="">Не выбрано</option>
                        <?php foreach ($packages as $pkg): ?>
                        <option value="<?= (int)$pkg['id'] ?>"><?= e($pkg['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label for="messenger">Удобный мессенджер</label>
                <select id="messenger" name="messenger">
                    <option value="">Не важно</option>
                    <option value="telegram">Telegram</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="phone">Звонок</option>
                </select>
            </div>
            <div class="form-row">
                <label for="message">Сообщение</label>
                <textarea id="message" name="message" rows="4" maxlength="3000" placeholder="Коротко о задаче"></textarea>
            </div>
            <label class="check">
                <input type="checkbox" name="consent" value="1" required>
                <span>Согласен на <a href="/privacy" target="_blank" rel="noopener">обработку персональных данных</a> *</span>
            </label>
            <button class="btn btn-primary btn-block" type="submit" id="leadSubmit">Отправить заявку</button>
        </form>
    </div>
</section>
