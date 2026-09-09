<?php use App\Core\Csrf; $s = $settings; ?>
<?php if (!empty($flash_ok)): ?><div class="alert ok"><?= e($flash_ok) ?></div><?php endif; ?>
<?php if (!empty($flash_error)): ?><div class="alert err"><?= e($flash_error) ?></div><?php endif; ?>
<form method="post" action="/admin/settings" class="form-grid" enctype="multipart/form-data">
    <?= Csrf::field() ?>
    <label>Публичный URL (каноникал)<input name="public_url" value="<?= e($s['public_url'] ?? 'https://bizdevops.site') ?>" placeholder="https://bizdevops.site"></label>
    <label>Имя / бренд (RU)<input name="site_name" value="<?= e($s['site_name'] ?? '') ?>"></label>
    <label>Имя латиницей (EN/FA/ZH/TR/AR)<input name="site_name_latin" value="<?= e($s['site_name_latin'] ?? 'Alexander M.') ?>" placeholder="Alexander M."></label>
    <p class="muted">На других языках имя не переводится — латиница + bdi (без поломки RTL).</p>
    <label>Должность (RU)<input name="site_role" value="<?= e($s['site_role'] ?? '') ?>"></label>
    <label>Слоган (RU)<textarea name="site_tagline" rows="2"><?= e($s['site_tagline'] ?? '') ?></textarea></label>
    <label>Оффер / H1 (RU)<textarea name="hero_offer" rows="2"><?= e($s['hero_offer'] ?? '') ?></textarea></label>
    <label>Подзаголовок героя (RU)<textarea name="hero_sub" rows="3"><?= e($s['hero_sub'] ?? '') ?></textarea></label>
    <label>Фото специалиста (jpg/png/webp ≤3MB)<input type="file" name="avatar" accept="image/jpeg,image/png,image/webp"></label>
    <?php if (!empty($s['avatar_path'])): ?><p class="muted">Сейчас: <?= e($s['avatar_path']) ?></p><?php endif; ?>
    <label>OG image URL<input name="og_image" value="<?= e($s['og_image'] ?? '') ?>" placeholder="/uploads/... или https://"></label>
    <label>Телефон<input name="phone" value="<?= e($s['phone'] ?? '') ?>"></label>
    <label>Email<input name="email" value="<?= e($s['email'] ?? '') ?>"></label>
    <label>Telegram URL<input name="telegram" value="<?= e($s['telegram'] ?? '') ?>"></label>
    <label>WhatsApp URL<input name="whatsapp" value="<?= e($s['whatsapp'] ?? '') ?>"></label>
    <label>Город / локация<input name="city" value="<?= e($s['city'] ?? '') ?>"></label>
    <label>Лет опыта (например 25+)<input name="experience_years" value="<?= e($s['experience_years'] ?? '') ?>"></label>
    <label>Проектов<input name="projects_count" value="<?= e($s['projects_count'] ?? '') ?>"></label>
    <label>Часов реакции<input name="response_hours" value="<?= e($s['response_hours'] ?? '') ?>"></label>
    <label>Формат работы<textarea name="work_format" rows="2"><?= e($s['work_format'] ?? '') ?></textarea></label>
    <label>SLA ответа<textarea name="response_sla" rows="2"><?= e($s['response_sla'] ?? '') ?></textarea></label>
    <label>Что не беру<textarea name="not_doing" rows="3"><?= e($s['not_doing'] ?? '') ?></textarea></label>
    <label>Яндекс.Метрика (код)<textarea name="yandex_metrika" rows="4"><?= e($s['yandex_metrika'] ?? '') ?></textarea></label>
    <label>Google Analytics<textarea name="google_analytics" rows="4"><?= e($s['google_analytics'] ?? '') ?></textarea></label>
    <label>FAQ JSON (RU)<textarea name="faq_json" rows="10"><?= e($s['faq_json'] ?? '') ?></textarea></label>

    <fieldset>
        <legend>Курс USD (ЦБ РФ)</legend>
        <p class="muted">Цены в БД в рублях. USD — мелкая подпись на карточках.</p>
        <label>USD rate (RUB per $)<input name="usd_rate" type="number" step="0.0001" value="<?= e($s['usd_rate'] ?? '') ?>"></label>
        <p class="muted">Обновлён: <?= e($s['usd_rate_updated_at'] ?? '—') ?></p>
    </fieldset>

    <button class="btn" type="submit">Сохранить</button>
</form>
<form method="post" action="/admin/settings/refresh-usd" style="margin-top:1rem">
    <?= Csrf::field() ?>
    <button class="btn" type="submit">Обновить курс с ЦБ РФ</button>
</form>
