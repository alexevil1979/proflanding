<form method="get" class="filters">
    <label>Статус
        <select name="status" onchange="this.form.submit()">
            <option value="">Все</option>
            <?php foreach (['new','in_progress','done','spam'] as $st): ?>
            <option value="<?= $st ?>"<?= ($status ?? '') === $st ? ' selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
    </label>
</form>
<table class="table">
    <thead><tr><th>ID</th><th>Имя</th><th>Телефон</th><th>Услуга</th><th>Статус</th><th>Дата</th></tr></thead>
    <tbody>
    <?php foreach ($leads as $lead): ?>
        <tr>
            <td><a href="/admin/leads/<?= (int)$lead['id'] ?>">#<?= (int)$lead['id'] ?></a></td>
            <td><?= e($lead['name']) ?></td>
            <td><?= e($lead['phone']) ?></td>
            <td><?= e($lead['service_title'] ?? '—') ?></td>
            <td><?= e($lead['status']) ?></td>
            <td><?= e($lead['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
