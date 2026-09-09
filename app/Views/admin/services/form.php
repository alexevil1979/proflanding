<?php
use App\Core\Csrf;
$s = $service ?? [];
$action = $service ? '/admin/services/' . (int)$service['id'] . '/edit' : '/admin/services/create';
?>
<?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="form-grid">
    <?= Csrf::field() ?>
    <label>Название<input name="title" required value="<?= e($s['title'] ?? '') ?>"></label>
    <label>Slug<input name="slug" value="<?= e($s['slug'] ?? '') ?>" placeholder="auto"></label>
    <label>Краткий текст<textarea name="short_text" rows="3"><?= e($s['short_text'] ?? '') ?></textarea></label>
    <label>Полный текст<textarea name="full_text" rows="5"><?= e($s['full_text'] ?? '') ?></textarea></label>
    <label>Цена от<input type="number" step="0.01" name="price_from" value="<?= e(isset($s['price_from']) ? (string)$s['price_from'] : '') ?>"></label>
    <label>Пояснение к цене<input name="price_note" value="<?= e($s['price_note'] ?? '') ?>"></label>
    <label>Период
        <select name="period">
            <?php foreach (['one_time'=>'Разово','monthly'=>'Ежемесячно','custom'=>'Custom'] as $k=>$lab): ?>
            <option value="<?= $k ?>"<?= ($s['period'] ?? '') === $k ? ' selected' : '' ?>><?= $lab ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Иконка (ключ)<input name="icon" value="<?= e($s['icon'] ?? 'code') ?>"></label>
    <label>Порядок<input type="number" name="sort_order" value="<?= e((string)($s['sort_order'] ?? '0')) ?>"></label>
    <label>CTA<input name="cta_label" value="<?= e($s['cta_label'] ?? 'Заказать') ?>"></label>
    <label class="check"><input type="checkbox" name="is_active" value="1"<?= !isset($s['is_active']) || !empty($s['is_active']) ? ' checked' : '' ?>> Активна</label>
    <label class="check"><input type="checkbox" name="is_featured" value="1"<?= !empty($s['is_featured']) ? ' checked' : '' ?>> Featured</label>
    <button class="btn" type="submit">Сохранить</button>
</form>
