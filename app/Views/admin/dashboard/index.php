<div class="cards-row">
    <div class="kpi"><div class="kpi-n"><?= (int)$newLeads ?></div><div class="kpi-l">Новые заявки</div></div>
    <div class="kpi"><div class="kpi-n"><?= (int)$activeServices ?></div><div class="kpi-l">Активные услуги</div></div>
</div>

<h2>Последние заявки</h2>
<table class="table">
    <thead><tr><th>ID</th><th>Имя</th><th>Телефон</th><th>Статус</th><th>Дата</th></tr></thead>
    <tbody>
    <?php foreach ($latestLeads as $lead): ?>
        <tr>
            <td><a href="/admin/leads/<?= (int)$lead['id'] ?>">#<?= (int)$lead['id'] ?></a></td>
            <td><?= e($lead['name']) ?></td>
            <td><?= e($lead['phone']) ?></td>
            <td><?= e($lead['status']) ?></td>
            <td><?= e($lead['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2>Последние уведомления</h2>
<table class="table">
    <thead><tr><th>Канал</th><th>Статус</th><th>Lead</th><th>Ответ</th><th>Время</th></tr></thead>
    <tbody>
    <?php foreach ($notifications as $n): ?>
        <tr>
            <td><?= e($n['channel']) ?></td>
            <td class="<?= $n['status'] === 'ok' ? 'ok' : 'err' ?>"><?= e($n['status']) ?></td>
            <td><?= $n['lead_id'] ? '#' . (int)$n['lead_id'] : '—' ?></td>
            <td class="truncate"><?= e(mb_substr((string)$n['response'], 0, 80)) ?></td>
            <td><?= e($n['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
