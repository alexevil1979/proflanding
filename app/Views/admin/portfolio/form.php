<?php
use App\Core\Csrf;
$item = (isset($item) && is_array($item)) ? $item : null;
$isEdit = $item !== null && !empty($item['id']);
$action = $isEdit ? '/admin/portfolio/' . (int)$item['id'] . '/edit' : '/admin/portfolio/create';
?>
<?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
<form method="post" action="<?= e($action) ?>" class="form-grid">
    <?= Csrf::field() ?>
    <label>Название<input name="title" required value="<?= e($item['title'] ?? '') ?>"></label>
    <label>Краткое описание<textarea name="description" rows="3"><?= e($item['description'] ?? '') ?></textarea></label>
    <label>Стек<input name="stack" value="<?= e($item['stack'] ?? '') ?>"></label>
    <label>Результат<input name="result_text" value="<?= e($item['result_text'] ?? '') ?>"></label>
    <label>URL кейса<input name="url" value="<?= e($item['url'] ?? '') ?>"></label>
    <label>Картинка (URL или /uploads/...)<input name="image" value="<?= e($item['image'] ?? '') ?>"></label>
    <label>Порядок<input type="number" name="sort_order" value="<?= e((string)($item['sort_order'] ?? '0')) ?>"></label>
    <label class="check"><input type="checkbox" name="is_active" value="1"<?= !$isEdit || !empty($item['is_active']) ? ' checked' : '' ?>> Активен</label>
    <button class="btn" type="submit">Сохранить</button>
</form>
