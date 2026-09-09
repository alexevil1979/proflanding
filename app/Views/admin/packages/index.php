<?php use App\Core\Csrf; ?>
<p><a class="btn" href="/admin/packages/create">+ Добавить пакет</a></p>
<table class="table">
    <thead><tr><th>Название</th><th>Цена</th><th>Активен</th><th>Featured</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($packages as $p): ?>
        <tr>
            <td><?= e($p['title']) ?></td>
            <td><?= $p['price'] !== null ? e(money((float)$p['price'])) : '—' ?></td>
            <td><?= !empty($p['is_active']) ? 'да' : 'нет' ?></td>
            <td><?= !empty($p['is_featured']) ? 'да' : 'нет' ?></td>
            <td class="actions">
                <a href="/admin/packages/<?= (int)$p['id'] ?>/edit">Изменить</a>
                <form method="post" action="/admin/packages/<?= (int)$p['id'] ?>/delete" onsubmit="return confirm('Удалить?')">
                    <?= Csrf::field() ?>
                    <button type="submit" class="link-btn">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
