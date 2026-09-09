<?php use App\Core\Csrf; $lead = $lead; ?>
<div class="detail-grid">
    <div>
        <p><b>Имя:</b> <?= e($lead['name']) ?></p>
        <p><b>Телефон:</b> <?= e($lead['phone']) ?></p>
        <p><b>Email:</b> <?= e($lead['email'] ?? '—') ?></p>
        <p><b>Мессенджер:</b> <?= e($lead['messenger'] ?? '—') ?></p>
        <p><b>Услуга:</b> <?= e($lead['service_title'] ?? '—') ?></p>
        <p><b>Пакет:</b> <?= e($lead['package_title'] ?? '—') ?></p>
        <p><b>Сообщение:</b><br><?= nl2br(e($lead['message'] ?? '—')) ?></p>
        <p><b>Страница:</b> <?= e($lead['page_url'] ?? '—') ?></p>
        <p><b>UTM:</b> <?= e(trim(implode(' / ', array_filter([$lead['utm_source'] ?? null, $lead['utm_medium'] ?? null, $lead['utm_campaign'] ?? null]))) ?: '—') ?></p>
        <p><b>IP:</b> <?= e($lead['ip'] ?? '') ?></p>
        <p><b>UA:</b> <?= e($lead['user_agent'] ?? '') ?></p>
        <p><b>Создано:</b> <?= e($lead['created_at']) ?></p>
    </div>
    <form method="post" action="/admin/leads/<?= (int)$lead['id'] ?>" class="panel">
        <?= Csrf::field() ?>
        <label>Статус
            <select name="status">
                <?php foreach (['new','in_progress','done','spam'] as $st): ?>
                <option value="<?= $st ?>"<?= $lead['status'] === $st ? ' selected' : '' ?>><?= $st ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Заметка
            <textarea name="admin_note" rows="6"><?= e($lead['admin_note'] ?? '') ?></textarea>
        </label>
        <button type="submit" class="btn">Сохранить</button>
    </form>
</div>
<p><a href="/admin/leads">← К списку</a></p>
