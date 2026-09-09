<?php use App\Core\Csrf; ?>
<?php if (!empty($flash_ok)): ?><div class="alert ok"><?= e($flash_ok) ?></div><?php endif; ?>
<p><a class="btn" href="/admin/portfolio/create">+ Добавить кейс</a></p>
<table class="table">
    <thead><tr><th>Название</th><th>Стек</th><th>Активен</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= e($item['title']) ?></td>
            <td><?= e($item['stack'] ?? '—') ?></td>
            <td><?= !empty($item['is_active']) ? 'да' : 'нет' ?></td>
            <td class="actions">
                <a href="/admin/portfolio/<?= (int)$item['id'] ?>/edit">Изменить</a>
                <form method="post" action="/admin/portfolio/<?= (int)$item['id'] ?>/delete" onsubmit="return confirm('Удалить?')">
                    <?= Csrf::field() ?>
                    <button type="submit" class="link-btn">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$items): ?><tr><td colspan="4">Нет кейсов — секция на лендинге скрыта.</td></tr><?php endif; ?>
    </tbody>
</table>
