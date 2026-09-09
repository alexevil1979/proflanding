<?php
use App\Core\Csrf;
$keys = ['home' => 'Главная', 'privacy' => 'Privacy', 'offer' => 'Оферта'];
?>
<form method="post" action="/admin/seo" class="form-grid">
    <?= Csrf::field() ?>
    <?php foreach ($keys as $key => $label): $p = $pages[$key] ?? []; ?>
        <fieldset>
            <legend><?= e($label) ?> (<?= e($key) ?>)</legend>
            <label>Title<input name="pages[<?= e($key) ?>][title]" value="<?= e($p['title'] ?? '') ?>"></label>
            <label>Description<textarea name="pages[<?= e($key) ?>][description]" rows="2"><?= e($p['description'] ?? '') ?></textarea></label>
            <label>H1<input name="pages[<?= e($key) ?>][h1]" value="<?= e($p['h1'] ?? '') ?>"></label>
            <label>OG Title<input name="pages[<?= e($key) ?>][og_title]" value="<?= e($p['og_title'] ?? '') ?>"></label>
            <label>OG Description<textarea name="pages[<?= e($key) ?>][og_description]" rows="2"><?= e($p['og_description'] ?? '') ?></textarea></label>
            <label>OG Image URL<input name="pages[<?= e($key) ?>][og_image]" value="<?= e($p['og_image'] ?? '') ?>"></label>
            <label>Canonical<input name="pages[<?= e($key) ?>][canonical]" value="<?= e($p['canonical'] ?? '') ?>"></label>
            <label>Robots<input name="pages[<?= e($key) ?>][robots]" value="<?= e($p['robots'] ?? 'index,follow') ?>"></label>
        </fieldset>
    <?php endforeach; ?>
    <button class="btn" type="submit">Сохранить SEO</button>
</form>
