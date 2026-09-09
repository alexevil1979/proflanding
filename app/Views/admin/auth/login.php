<?php use App\Core\Csrf; ?>
<div class="login-card">
    <h1>Вход в админку</h1>
    <?php if (!empty($error)): ?><div class="alert err"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="/admin/login">
        <?= Csrf::field() ?>
        <label>Логин<input type="text" name="login" required autocomplete="username"></label>
        <label>Пароль<input type="password" name="password" required autocomplete="current-password"></label>
        <button type="submit" class="btn">Войти</button>
    </form>
</div>
