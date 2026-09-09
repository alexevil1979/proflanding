<?php
use App\Core\Csrf;
use App\Models\Package;
$p = $package ?? [];
$action = $package ? '/admin/packages/' . (int)$package['id'] . '/edit' : '/admin/packages/create';
$features = isset($p['features']) ? implode("\n", Package::featuresList($p['features'])) : '';
?>
<?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="form-grid">
    <?= Csrf::field() ?>
    <label>Название<input name="title" required value="<?= e($p['title'] ?? '') ?>"></label>
    <label>Описание<textarea name="description" rows="3"><?= e($p['description'] ?? '') ?></textarea></label>
    <label>Цена<input type="number" step="0.01" name="price" value="<?= e(isset($p['price']) ? (string)$p['price'] : '') ?>"></label>
    <label>Пояснение к цене<input name="price_note" value="<?= e($p['price_note'] ?? '') ?>"></label>
    <label>Фичи (по одной на строку)<textarea name="features" rows="8"><?= e($features) ?></textarea></label>
    <label>Порядок<input type="number" name="sort_order" value="<?= e((string)($p['sort_order'] ?? '0')) ?>"></label>
    <label>CTA<input name="cta_label" value="<?= e($p['cta_label'] ?? 'Выбрать пакет') ?>"></label>
    <label class="check"><input type="checkbox" name="is_active" value="1"<?= !isset($p['is_active']) || !empty($p['is_active']) ? ' checked' : '' ?>> Активен</label>
    <label class="check"><input type="checkbox" name="is_featured" value="1"<?= !empty($p['is_featured']) ? ' checked' : '' ?>> Акцент (средний)</label>
    <button class="btn" type="submit">Сохранить</button>
</form>
