<?php use App\Core\Csrf; $s = $settings; ?>
<form method="post" action="/admin/notifications" class="form-grid">
    <?= Csrf::field() ?>
    <label class="check"><input type="checkbox" name="telegram_enabled" value="1"<?= ($s['telegram_enabled'] ?? '1') === '1' ? ' checked' : '' ?>> Telegram включён</label>
    <label class="check"><input type="checkbox" name="mail_enabled" value="1"<?= ($s['mail_enabled'] ?? '1') === '1' ? ' checked' : '' ?>> Email включён</label>
    <label>Тема письма (шаблон)<input name="notify_tpl_email_subject" value="<?= e($s['notify_tpl_email_subject'] ?? 'Новая заявка с лендинга #{id}') ?>"></label>
    <button class="btn" type="submit">Сохранить</button>
</form>

<div class="cards-row" style="margin-top:1.5rem">
    <form method="post" action="/admin/notifications/test-telegram">
        <?= Csrf::field() ?>
        <button class="btn" type="submit">Тест Telegram</button>
    </form>
    <form method="post" action="/admin/notifications/test-email">
        <?= Csrf::field() ?>
        <button class="btn" type="submit">Тест SMTP</button>
    </form>
</div>
<p class="muted">Токены и SMTP берутся из `.env`. Здесь только флаги и шаблон темы.</p>

<h2>Лог уведомлений</h2>
<table class="table">
    <thead><tr><th>Канал</th><th>Статус</th><th>Lead</th><th>Ответ</th><th>Время</th></tr></thead>
    <tbody>
    <?php foreach ($logs as $n): ?>
        <tr>
            <td><?= e($n['channel']) ?></td>
            <td class="<?= $n['status'] === 'ok' ? 'ok' : 'err' ?>"><?= e($n['status']) ?></td>
            <td><?= $n['lead_id'] ? '#' . (int)$n['lead_id'] : '—' ?></td>
            <td class="truncate"><?= e(mb_substr((string)$n['response'], 0, 120)) ?></td>
            <td><?= e($n['created_at']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
