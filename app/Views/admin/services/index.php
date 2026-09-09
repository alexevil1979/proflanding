<?php use App\Core\Csrf; ?>
<p><a class="btn" href="/admin/services/create">+ Добавить услугу</a></p>
<table class="table" id="servicesTable">
    <thead><tr><th>Порядок</th><th>Название</th><th>Цена от</th><th>Активна</th><th>Featured</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($services as $s): ?>
        <tr data-id="<?= (int)$s['id'] ?>">
            <td><?= (int)$s['sort_order'] ?></td>
            <td><?= e($s['title']) ?><div class="muted">/<?= e($s['slug']) ?></div></td>
            <td><?= $s['price_from'] !== null ? e(money((float)$s['price_from'])) : '—' ?></td>
            <td><?= !empty($s['is_active']) ? 'да' : 'нет' ?></td>
            <td><?= !empty($s['is_featured']) ? 'да' : 'нет' ?></td>
            <td class="actions">
                <a href="/admin/services/<?= (int)$s['id'] ?>/edit">Изменить</a>
                <form method="post" action="/admin/services/<?= (int)$s['id'] ?>/delete" onsubmit="return confirm('Удалить?')">
                    <?= Csrf::field() ?>
                    <button type="submit" class="link-btn">Удалить</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<p class="muted">Сортировка: меняйте поле «Порядок» в карточке услуги (шаг 10).</p>
