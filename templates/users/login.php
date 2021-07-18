<?php include __DIR__ . '/../header.php'; ?>
<div class="login">
    <h1>Авторизация</h1><br>
    <?php if(!empty($error)): ?>
        <div style="background-color: red; padding: 5px; margin: 15px"><?= $error ?></div>
    <?php endif; ?>
    <form action="/users/login" method="post">
        <label>Email <input class="article__input" type="text" name="email" value="<?= $_POST['email'] ?? '' ?>"></label>
        <br><br>
        <label>Password <input class="article__input" type="password" name="password" value="<?= $_POST['password'] ?? '' ?>"></label>
        <br><br>
        <input class="log_btn" type="submit" value="Войти">
    </form>
</div>
<?php include __DIR__ . '/../footer.php'; ?>