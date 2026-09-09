<?php use App\Core\Csrf; ?>
<form method="post" action="/admin/password" class="form-grid" style="max-width:420px">
    <?= Csrf::field() ?>
    <label>Текущий пароль<input type="password" name="current_password" required></label>
    <label>Новый пароль<input type="password" name="new_password" required minlength="8"></label>
    <label>Повтор нового<input type="password" name="confirm_password" required minlength="8"></label>
    <button class="btn" type="submit">Сменить пароль</button>
</form>
