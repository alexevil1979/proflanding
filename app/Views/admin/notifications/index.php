<?php
use App\Core\Csrf;
/** @var array $settings */
/** @var array $resolved */
/** @var bool $has_smtp_pass */
/** @var bool $has_tg_token */
$s = $settings;
$smtp = $resolved['smtp'] ?? [];
$tg = $resolved['telegram'] ?? [];
?>
<form method="post" action="/admin/notifications" class="form-grid">
    <?= Csrf::field() ?>

    <fieldset>
        <legend>Каналы</legend>
        <label class="check"><input type="checkbox" name="telegram_enabled" value="1"<?= ($s['telegram_enabled'] ?? '1') === '1' ? ' checked' : '' ?>> Telegram включён</label>
        <label class="check"><input type="checkbox" name="mail_enabled" value="1"<?= ($s['mail_enabled'] ?? '1') === '1' ? ' checked' : '' ?>> Email включён</label>
        <label>Тема письма (шаблон)<input name="notify_tpl_email_subject" value="<?= e($s['notify_tpl_email_subject'] ?? 'Новая заявка с лендинга #{id}') ?>"></label>
    </fieldset>

    <fieldset>
        <legend>Telegram</legend>
        <label>Bot token
            <input type="password" name="telegram_bot_token" value="" autocomplete="new-password"
                   placeholder="<?= !empty($has_tg_token) ? '•••••••• (оставьте пустым, чтобы не менять)' : '123456:AA...' ?>">
        </label>
        <label>Chat ID
            <input name="telegram_chat_id" value="<?= e($tg['chat_id'] ?? '') ?>" placeholder="-100... или личный id">
        </label>
        <p class="muted">Приоритет: значения из админки. Если пусто — берётся `.env`.</p>
    </fieldset>

    <fieldset>
        <legend>SMTP (Gmail)</legend>
        <label>SMTP host<input name="smtp_host" value="<?= e($smtp['host'] ?? 'smtp.gmail.com') ?>"></label>
        <label>Port<input type="number" name="smtp_port" value="<?= e((string)($smtp['port'] ?? '587')) ?>"></label>
        <label>Secure
            <select name="smtp_secure">
                <?php foreach (['tls' => 'TLS (587)', 'ssl' => 'SSL (465)'] as $k => $lab): ?>
                <option value="<?= $k ?>"<?= ($smtp['secure'] ?? 'tls') === $k ? ' selected' : '' ?>><?= e($lab) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>SMTP user (Gmail)<input name="smtp_user" value="<?= e($smtp['user'] ?? '') ?>" autocomplete="off"></label>
        <label>App Password
            <input type="password" name="smtp_pass" value="" autocomplete="new-password"
                   placeholder="<?= !empty($has_smtp_pass) ? '•••••••• (оставьте пустым, чтобы не менять)' : 'Google App Password' ?>">
        </label>
        <label>From email<input name="smtp_from" value="<?= e($smtp['from'] ?? '') ?>"></label>
        <label>From name<input name="smtp_from_name" value="<?= e($smtp['from_name'] ?? '') ?>"></label>
        <label>Куда слать заявки (To)<input name="smtp_to" value="<?= e($smtp['to'] ?? '') ?>"></label>
    </fieldset>

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
