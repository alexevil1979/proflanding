<?php
use App\Core\Csrf;
$s = $settings;
$enabled = ($s['promo_banner_enabled'] ?? '0') === '1';
$style = (int)($s['promo_banner_style'] ?? 1);
$dismissible = ($s['promo_banner_dismissible'] ?? '1') === '1';
$ctaUrl = $s['promo_banner_cta_url'] ?? '#lead';
$until = $s['promo_banner_until'] ?? '';
if ($until === '') {
    $until = date('Y-m-t');
}
?>
<form method="post" action="/admin/banner" class="form-grid">
    <?= Csrf::field() ?>

    <fieldset>
        <legend>Показ</legend>
        <label class="check">
            <input type="checkbox" name="promo_banner_enabled" value="1"<?= $enabled ? ' checked' : '' ?>>
            Баннер включён на сайте
        </label>
        <label class="check">
            <input type="checkbox" name="promo_banner_dismissible" value="1"<?= $dismissible ? ' checked' : '' ?>>
            Можно закрыть (запоминается в браузере)
        </label>
        <label>Ссылка кнопки
            <input name="promo_banner_cta_url" value="<?= e($ctaUrl) ?>" placeholder="#lead">
        </label>
        <label>Акция до (дата)
            <input type="date" name="promo_banner_until" value="<?= e($until) ?>">
        </label>
        <p class="muted">После этой даты баннер скрывается автоматически (даже если включён).</p>
    </fieldset>

    <fieldset>
        <legend>Оформление (5 типов)</legend>
        <div class="banner-style-pick">
            <?php foreach ($styles as $num => $label): ?>
            <label class="banner-style-card banner-preview--<?= (int)$num ?><?= $style === (int)$num ? ' is-active' : '' ?>">
                <input type="radio" name="promo_banner_style" value="<?= (int)$num ?>"<?= $style === (int)$num ? ' checked' : '' ?>>
                <span class="banner-style-swatch" aria-hidden="true"></span>
                <strong><?= (int)$num ?>. <?= e($label) ?></strong>
            </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>Тексты по языкам</legend>
        <p class="muted">Если для языка пусто — берётся русский, затем английский.</p>
        <?php foreach ($locales as $code => $meta):
            $row = $texts[$code] ?? ['title' => '', 'sub' => '', 'cta' => ''];
        ?>
        <div class="panel" style="margin-bottom:.75rem">
            <strong><?= e(strtoupper($code)) ?> — <?= e($meta['native']) ?></strong>
            <label>Заголовок
                <input name="texts[<?= e($code) ?>][title]" value="<?= e($row['title'] ?? '') ?>" maxlength="120">
            </label>
            <label>Подпись
                <input name="texts[<?= e($code) ?>][sub]" value="<?= e($row['sub'] ?? '') ?>" maxlength="200">
            </label>
            <label>Текст кнопки
                <input name="texts[<?= e($code) ?>][cta]" value="<?= e($row['cta'] ?? '') ?>" maxlength="60">
            </label>
        </div>
        <?php endforeach; ?>
    </fieldset>

    <button class="btn" type="submit">Сохранить баннер</button>
</form>
