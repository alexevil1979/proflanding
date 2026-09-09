<?php
/** @var array $seo */
/** @var array $services */
/** @var array $packages */
/** @var array $portfolio */
/** @var array $faq */
/** @var array $settings */
/** @var float|null $usdRate */
use App\Core\Csrf;

$h1 = $seo['h1'] ?? setting('hero_offer');
$name = brand_name();
$role = setting('site_role');
$jsonLdPerson = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $name,
    'jobTitle' => $role,
    'url' => app_url(ltrim(lang_url('/'), '/')),
    'email' => setting('email'),
    'telephone' => setting('phone'),
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => setting('city')],
];
$offersClean = [];
foreach ($services as $s) {
    $o = [
        '@type' => 'Offer',
        'name' => service_field($s, 'title'),
        'description' => service_field($s, 'short_text'),
        'priceCurrency' => 'RUB',
        'url' => app_url(ltrim(lang_url('/#services'), '/')),
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
    'url' => app_url(ltrim(lang_url('/'), '/')),
    'areaServed' => setting('city'),
    'priceRange' => '₽ / $',
];
$jsonLdCatalog = [
    '@context' => 'https://schema.org',
    '@type' => 'OfferCatalog',
    'name' => __('services_title'),
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
            <p class="eyebrow"><bdi dir="ltr"><?= e($name) ?></bdi> · <?= e($role) ?></p>
            <h1 id="hero-title"><?= e($h1) ?></h1>
            <p class="lead"><?= e(setting('hero_sub')) ?></p>
            <ul class="trust-bullets">
                <li><?= e(__('trust_1')) ?></li>
                <li><?= e(__('trust_2')) ?></li>
                <li><?= e(__('trust_3')) ?></li>
            </ul>
            <div class="hero-cta">
                <a class="btn btn-primary" href="#lead"><?= e(__('cta_lead')) ?></a>
                <a class="btn btn-ghost" href="#services"><?= e(__('cta_services')) ?></a>
            </div>
        </div>
        <div class="hero-visual reveal">
            <div class="portrait-card">
                <svg class="portrait" viewBox="0 0 320 380" width="320" height="380" role="img" aria-label="<?= e(__('portrait_alt')) ?>">
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
                    <strong><bdi dir="ltr"><?= e($name) ?></bdi></strong>
                    <span><?= e(setting('city')) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="stats" id="trust">
    <div class="container stats-grid">
        <div class="stat reveal"><strong><?= e(setting('experience_years', '10')) ?>+</strong><span><?= e(__('stat_years')) ?></span></div>
        <div class="stat reveal"><strong><?= e(setting('projects_count', '100+')) ?></strong><span><?= e(__('stat_projects')) ?></span></div>
        <div class="stat reveal"><strong><?= e(setting('response_hours', '2')) ?> <?= e(__('stat_hours_suffix')) ?></strong><span><?= e(__('stat_response')) ?></span></div>
    </div>
</section>

<section class="section" id="services" aria-labelledby="services-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="services-title"><?= e(__('services_title')) ?></h2>
            <p><?= e(__('services_sub')) ?></p>
        </header>
        <div class="services-grid">
            <?php foreach ($services as $service): ?>
            <article class="service-card<?= !empty($service['is_featured']) ? ' is-featured' : '' ?> reveal">
                <div class="service-top">
                    <span class="service-icon" aria-hidden="true"><?= e(mb_substr((string)$service['icon'], 0, 1)) ?></span>
                    <?php if (!empty($service['is_featured'])): ?><span class="badge"><?= e(__('featured')) ?></span><?php endif; ?>
                </div>
                <h3><?= e(service_field($service, 'title')) ?></h3>
                <p><?= e(service_field($service, 'short_text')) ?></p>
                <div class="service-price">
                    <?php if ($service['price_from'] !== null): ?>
                        <strong><?= e(__('price_from')) ?> <?= e(money_dual((float)$service['price_from'])) ?></strong>
                    <?php else: ?>
                        <strong><?= e(__('price_on_request')) ?></strong>
                    <?php endif; ?>
                    <span><?= e($service['price_note'] ?: period_label($service['period'])) ?></span>
                </div>
                <button type="button" class="btn btn-secondary btn-block js-order"
                        data-service="<?= (int)$service['id'] ?>" data-package="">
                    <?= e($service['cta_label'] ?: __('order')) ?>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
        <?php if (!empty($usdRate)): ?>
        <p class="rate-note muted reveal"><?= e(__('rate_note')) ?>: 1 USD ≈ <?= e(number_format((float)$usdRate, 2, '.', ' ')) ?> ₽</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-alt" id="packages" aria-labelledby="packages-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="packages-title"><?= e(__('packages_title')) ?></h2>
            <p><?= e(__('packages_sub')) ?></p>
        </header>
        <div class="packages-grid">
            <?php foreach ($packages as $pkg): ?>
            <?php $features = package_features($pkg); ?>
            <article class="package-card<?= !empty($pkg['is_featured']) ? ' is-featured' : '' ?> reveal">
                <?php if (!empty($pkg['is_featured'])): ?><div class="package-ribbon"><?= e(__('optimal')) ?></div><?php endif; ?>
                <h3><?= e(package_field($pkg, 'title')) ?></h3>
                <p><?= e(package_field($pkg, 'description')) ?></p>
                <div class="package-price">
                    <?php if ($pkg['price'] !== null): ?><strong><?= e(money_dual((float)$pkg['price'])) ?></strong><?php endif; ?>
                    <span><?= e($pkg['price_note'] ?? '') ?></span>
                </div>
                <ul>
                    <?php foreach ($features as $f): ?><li><?= e((string)$f) ?></li><?php endforeach; ?>
                </ul>
                <button type="button" class="btn <?= !empty($pkg['is_featured']) ? 'btn-primary' : 'btn-secondary' ?> btn-block js-order"
                        data-service="" data-package="<?= (int)$pkg['id'] ?>">
                    <?= e($pkg['cta_label'] ?: __('choose')) ?>
                </button>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="process" aria-labelledby="process-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="process-title"><?= e(__('process_title')) ?></h2>
            <p><?= e(__('process_sub')) ?></p>
        </header>
        <ol class="steps">
            <li class="reveal"><span>01</span><h3><?= e(__('step1_t')) ?></h3><p><?= e(__('step1_d')) ?></p></li>
            <li class="reveal"><span>02</span><h3><?= e(__('step2_t')) ?></h3><p><?= e(__('step2_d')) ?></p></li>
            <li class="reveal"><span>03</span><h3><?= e(__('step3_t')) ?></h3><p><?= e(__('step3_d')) ?></p></li>
            <li class="reveal"><span>04</span><h3><?= e(__('step4_t')) ?></h3><p><?= e(__('step4_d')) ?></p></li>
        </ol>
    </div>
</section>

<section class="section section-alt" id="stack" aria-labelledby="stack-title">
    <div class="container">
        <header class="section-head reveal">
            <h2 id="stack-title"><?= e(__('stack_title')) ?></h2>
            <p><?= e(__('stack_sub')) ?></p>
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
            <h2 id="faq-title"><?= e(__('faq_title')) ?></h2>
            <p><?= e(__('faq_sub')) ?></p>
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
                <h2 id="lead-title"><?= e(__('lead_title')) ?></h2>
                <p><?= e(__('lead_sub')) ?></p>
            </header>
            <div class="contact-chips">
                <?php if (setting('telegram')): ?><a href="<?= e(setting('telegram')) ?>" target="_blank" rel="noopener">Telegram</a><?php endif; ?>
                <?php if (setting('whatsapp')): ?><a href="<?= e(setting('whatsapp')) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
                <?php if (setting('phone')): ?><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a><?php endif; ?>
            </div>
        </div>
        <form class="lead-form reveal" id="leadForm" method="post" action="<?= e(lang_url('/lead')) ?>" novalidate>
            <?= Csrf::field() ?>
            <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <input type="hidden" name="page_url" value="<?= e(\App\Core\Lang::absoluteUrl('/')) ?>">
            <input type="hidden" name="utm_source" id="utm_source">
            <input type="hidden" name="utm_medium" id="utm_medium">
            <input type="hidden" name="utm_campaign" id="utm_campaign">
            <input type="hidden" name="utm_content" id="utm_content">
            <input type="hidden" name="utm_term" id="utm_term">

            <div class="form-row">
                <label for="name"><?= e(__('field_name')) ?> *</label>
                <input id="name" name="name" type="text" required maxlength="120" autocomplete="name">
            </div>
            <div class="form-row">
                <label for="phone"><?= e(__('field_phone')) ?> *</label>
                <input id="phone" name="phone" type="tel" required maxlength="40" autocomplete="tel">
            </div>
            <div class="form-row">
                <label for="email"><?= e(__('field_email')) ?></label>
                <input id="email" name="email" type="email" maxlength="160" autocomplete="email">
            </div>
            <div class="form-row two">
                <div>
                    <label for="service_id"><?= e(__('field_service')) ?></label>
                    <select id="service_id" name="service_id">
                        <option value=""><?= e(__('not_selected')) ?></option>
                        <?php foreach ($services as $service): ?>
                        <option value="<?= (int)$service['id'] ?>"><?= e(service_field($service, 'title')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="package_id"><?= e(__('field_package')) ?></label>
                    <select id="package_id" name="package_id">
                        <option value=""><?= e(__('not_selected')) ?></option>
                        <?php foreach ($packages as $pkg): ?>
                        <option value="<?= (int)$pkg['id'] ?>"><?= e(package_field($pkg, 'title')) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <label for="messenger"><?= e(__('field_messenger')) ?></label>
                <select id="messenger" name="messenger">
                    <option value=""><?= e(__('messenger_any')) ?></option>
                    <option value="telegram"><?= e(__('messenger_telegram')) ?></option>
                    <option value="whatsapp"><?= e(__('messenger_whatsapp')) ?></option>
                    <option value="phone"><?= e(__('messenger_phone')) ?></option>
                </select>
            </div>
            <div class="form-row">
                <label for="message"><?= e(__('field_message')) ?></label>
                <textarea id="message" name="message" rows="4" maxlength="3000" placeholder="<?= e(__('field_message_ph')) ?>"></textarea>
            </div>
            <label class="check">
                <input type="checkbox" name="consent" value="1" required>
                <span><?= e(__('consent')) ?>: <a href="<?= e(lang_url('/privacy')) ?>" target="_blank" rel="noopener"><?= e(__('consent_link')) ?></a> *</span>
            </label>
            <button class="btn btn-primary btn-block" type="submit" id="leadSubmit"><?= e(__('submit')) ?></button>
        </form>
    </div>
</section>
