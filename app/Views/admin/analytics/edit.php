<?php
use App\Core\Csrf;
$s = $settings;
$ymId = $s['yandex_metrika_id'] ?? '';
$gaId = $s['google_analytics_id'] ?? '';
$gtmId = $s['google_tag_manager_id'] ?? '';
?>
<p class="muted">Счётчики выводятся только на публичных страницах. Админка всегда <code>noindex</code>.</p>

<form method="post" action="/admin/analytics" class="form-grid">
    <?= Csrf::field() ?>

    <fieldset>
        <legend>Яндекс</legend>
        <label>ID счётчика Метрики (только цифры)
            <input name="yandex_metrika_id" value="<?= e($ymId) ?>" placeholder="12345678" inputmode="numeric">
        </label>
        <p class="muted">Если указан ID — скрипт Метрики подключается автоматически (webvisor, clickmap). Полный код ниже имеет приоритет, если заполнен.</p>
        <label>Полный код Метрики (HTML, опционально)
            <textarea name="yandex_metrika" rows="6" placeholder="<!-- Yandex.Metrika -->"><?= e($s['yandex_metrika'] ?? '') ?></textarea>
        </label>
        <label>Яндекс.Вебмастер — meta verification
            <input name="yandex_verification" value="<?= e($s['yandex_verification'] ?? '') ?>" placeholder="значение content= из meta">
        </label>
        <label>Идентификатор цели «заявка» в Метрике
            <input name="yandex_goal_lead" value="<?= e($s['yandex_goal_lead'] ?? 'lead') ?>" placeholder="lead">
        </label>
    </fieldset>

    <fieldset>
        <legend>Google</legend>
        <label>Google Analytics 4 — Measurement ID
            <input name="google_analytics_id" value="<?= e($gaId) ?>" placeholder="G-XXXXXXXX">
        </label>
        <label>Google Tag Manager ID
            <input name="google_tag_manager_id" value="<?= e($gtmId) ?>" placeholder="GTM-XXXXXXX">
        </label>
        <p class="muted">Если задан GTM, обычно GA подключают внутри контейнера — не дублируйте GA4 ID без нужды.</p>
        <label>Полный код GA / gtag (HTML, опционально)
            <textarea name="google_analytics" rows="6" placeholder="<!-- Google tag -->"><?= e($s['google_analytics'] ?? '') ?></textarea>
        </label>
        <label>Google Search Console — meta verification
            <input name="google_site_verification" value="<?= e($s['google_site_verification'] ?? '') ?>" placeholder="значение content= из meta">
        </label>
        <label>Имя события GA4 при заявке
            <input name="ga_event_lead" value="<?= e($s['ga_event_lead'] ?? 'generate_lead') ?>" placeholder="generate_lead">
        </label>
    </fieldset>

    <fieldset>
        <legend>Дополнительные вставки</legend>
        <label>Произвольный код в &lt;head&gt;
            <textarea name="head_custom" rows="5" placeholder="meta, link, script"><?= e($s['head_custom'] ?? '') ?></textarea>
        </label>
        <label>Произвольный код перед &lt;/body&gt;
            <textarea name="body_custom" rows="5"><?= e($s['body_custom'] ?? '') ?></textarea>
        </label>
        <p class="muted">PHP-теги вырезаются. Вставляйте только доверенный код из кабинетов Яндекс/Google.</p>
    </fieldset>

    <fieldset>
        <legend>Статус (что уйдёт на сайт)</legend>
        <ul class="muted" style="margin:0;padding-left:1.2rem">
            <li>Метрика: <?= $ymId !== '' || !empty($s['yandex_metrika']) ? '<span class="ok">включена</span>' : 'не задана' ?></li>
            <li>GA4: <?= $gaId !== '' || !empty($s['google_analytics']) ? '<span class="ok">включён</span>' : 'не задан' ?></li>
            <li>GTM: <?= $gtmId !== '' ? '<span class="ok">включён</span>' : 'не задан' ?></li>
            <li>Верификация Яндекс: <?= !empty($s['yandex_verification']) ? '<span class="ok">есть</span>' : 'нет' ?></li>
            <li>Верификация Google: <?= !empty($s['google_site_verification']) ? '<span class="ok">есть</span>' : 'нет' ?></li>
        </ul>
    </fieldset>

    <button class="btn" type="submit">Сохранить аналитику</button>
</form>

<div class="panel" style="margin-top:1.25rem;max-width:820px">
    <h2 style="margin-top:0;font-size:1.05rem">Как подключить</h2>
    <ol class="muted" style="padding-left:1.2rem">
        <li><a href="https://metrika.yandex.ru/" target="_blank" rel="noopener">Яндекс.Метрика</a> → создать счётчик → сюда ID или полный код.</li>
        <li><a href="https://webmaster.yandex.ru/" target="_blank" rel="noopener">Вебмастер</a> → добавить https://bizdevops.site → meta-подтверждение.</li>
        <li><a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a> → ресурс → meta-тег HTML.</li>
        <li><a href="https://analytics.google.com/" target="_blank" rel="noopener">GA4</a> → Admin → Data streams → Measurement ID (G-…).</li>
        <li>Sitemap: <code>https://bizdevops.site/sitemap.xml</code> — добавить в оба кабинета.</li>
    </ol>
</div>
