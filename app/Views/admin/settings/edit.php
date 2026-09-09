<?php use App\Core\Csrf; $s = $settings; ?>
<form method="post" action="/admin/settings" class="form-grid">
    <?= Csrf::field() ?>
    <label>Имя / бренд<input name="site_name" value="<?= e($s['site_name'] ?? '') ?>"></label>
    <label>Должность (RU)<input name="site_role" value="<?= e($s['site_role'] ?? '') ?>"></label>
    <label>Слоган (RU)<textarea name="site_tagline" rows="2"><?= e($s['site_tagline'] ?? '') ?></textarea></label>
    <label>Оффер / H1 (RU)<textarea name="hero_offer" rows="2"><?= e($s['hero_offer'] ?? '') ?></textarea></label>
    <label>Подзаголовок героя (RU)<textarea name="hero_sub" rows="3"><?= e($s['hero_sub'] ?? '') ?></textarea></label>
    <label>Телефон<input name="phone" value="<?= e($s['phone'] ?? '') ?>"></label>
    <label>Email<input name="email" value="<?= e($s['email'] ?? '') ?>"></label>
    <label>Telegram URL<input name="telegram" value="<?= e($s['telegram'] ?? '') ?>"></label>
    <label>WhatsApp URL<input name="whatsapp" value="<?= e($s['whatsapp'] ?? '') ?>"></label>
    <label>Город (RU)<input name="city" value="<?= e($s['city'] ?? '') ?>"></label>
    <label>Лет опыта<input name="experience_years" value="<?= e($s['experience_years'] ?? '') ?>"></label>
    <label>Проектов<input name="projects_count" value="<?= e($s['projects_count'] ?? '') ?>"></label>
    <label>Часов реакции<input name="response_hours" value="<?= e($s['response_hours'] ?? '') ?>"></label>
    <label>Яндекс.Метрика (код)<textarea name="yandex_metrika" rows="4"><?= e($s['yandex_metrika'] ?? '') ?></textarea></label>
    <label>Google Analytics<textarea name="google_analytics" rows="4"><?= e($s['google_analytics'] ?? '') ?></textarea></label>
    <label>FAQ JSON (RU)<textarea name="faq_json" rows="10"><?= e($s['faq_json'] ?? '') ?></textarea></label>

    <fieldset>
        <legend>Курс USD (ЦБ РФ)</legend>
        <p class="muted">Цены в БД в рублях. На сайте: ₽ + ~$ по курсу. Языки UI: ru/en/fa/zh/tr/ar (как hiddifysales.com).</p>
        <label>USD rate (RUB per $)<input name="usd_rate" type="number" step="0.0001" value="<?= e($s['usd_rate'] ?? '') ?>"></label>
        <p class="muted">Обновлён: <?= e($s['usd_rate_updated_at'] ?? '—') ?></p>
    </fieldset>

    <button class="btn" type="submit">Сохранить</button>
</form>
<form method="post" action="/admin/settings/refresh-usd" style="margin-top:1rem">
    <?= Csrf::field() ?>
    <button class="btn" type="submit">Обновить курс с ЦБ РФ</button>
</form>
