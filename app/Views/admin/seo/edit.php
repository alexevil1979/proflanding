<?php
use App\Core\Csrf;
$keys = ['home' => 'Главная', 'privacy' => 'Политика', 'offer' => 'Оферта'];
$selected = preg_split('/[\s,]+/', strtolower((string)$index_locales)) ?: ['ru', 'en'];
?>
<div class="panel" style="margin-bottom:1.25rem;max-width:820px">
    <h2 style="margin:0 0 .75rem;font-size:1.05rem">Чеклист к индексации</h2>
    <ul style="margin:0;padding-left:1.1rem">
        <?php foreach ($checklist as $item): ?>
        <li class="<?= !empty($item['ok']) ? 'ok' : 'err' ?>">
            <?= !empty($item['ok']) ? '✓' : '○' ?> <?= e($item['label']) ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <p class="muted" style="margin:.75rem 0 0">
        Sitemap: <a href="/sitemap.xml" target="_blank" rel="noopener">/sitemap.xml</a> ·
        Robots: <a href="/robots.txt" target="_blank" rel="noopener">/robots.txt</a> ·
        Аналитика: <a href="/admin/analytics">настроить счётчики</a>
    </p>
</div>

<form method="post" action="/admin/seo" class="form-grid">
    <?= Csrf::field() ?>

    <fieldset>
        <legend>Индексируемые языки</legend>
        <p class="muted">Остальные локали получат <code>noindex,follow</code> и не попадут в sitemap (чтобы не плодить тонкие дубли).</p>
        <div class="actions" style="flex-wrap:wrap">
            <?php foreach ($locales as $code => $meta): ?>
            <label class="check" style="margin:0">
                <input type="checkbox" name="index_locales[]" value="<?= e($code) ?>"<?= in_array($code, $selected, true) ? ' checked' : '' ?>>
                <?= e(strtoupper($code)) ?> (<?= e($meta['native']) ?>)
            </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <?php foreach ($keys as $key => $label): $p = $pages[$key] ?? [];
        $tLen = mb_strlen((string)($p['title'] ?? ''));
        $dLen = mb_strlen((string)($p['description'] ?? ''));
    ?>
        <fieldset>
            <legend><?= e($label) ?> (<?= e($key) ?>)</legend>
            <label>Title <span class="muted">(<?= (int)$tLen ?>/60)</span>
                <input name="pages[<?= e($key) ?>][title]" maxlength="70" value="<?= e($p['title'] ?? '') ?>">
            </label>
            <label>Description <span class="muted">(<?= (int)$dLen ?>/160)</span>
                <textarea name="pages[<?= e($key) ?>][description]" rows="2" maxlength="180"><?= e($p['description'] ?? '') ?></textarea>
            </label>
            <label>H1<input name="pages[<?= e($key) ?>][h1]" value="<?= e($p['h1'] ?? '') ?>"></label>
            <label>OG Title<input name="pages[<?= e($key) ?>][og_title]" value="<?= e($p['og_title'] ?? '') ?>"></label>
            <label>OG Description<textarea name="pages[<?= e($key) ?>][og_description]" rows="2"><?= e($p['og_description'] ?? '') ?></textarea></label>
            <label>OG Image URL<input name="pages[<?= e($key) ?>][og_image]" value="<?= e($p['og_image'] ?? '') ?>" placeholder="пусто = из Контента"></label>
            <label>Canonical (пусто = авто от public_url)
                <input name="pages[<?= e($key) ?>][canonical]" value="<?= e($p['canonical'] ?? '') ?>" placeholder="https://bizdevops.site/">
            </label>
            <label>Robots
                <select name="pages[<?= e($key) ?>][robots]">
                    <?php foreach (['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'] as $r): ?>
                    <option value="<?= e($r) ?>"<?= ($p['robots'] ?? 'index,follow') === $r ? ' selected' : '' ?>><?= e($r) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </fieldset>
    <?php endforeach; ?>
    <button class="btn" type="submit">Сохранить SEO</button>
</form>
