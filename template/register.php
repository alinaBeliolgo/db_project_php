<!--Шаблон страницы регистрации-->

<!DOCTYPE html>
<html>

<head>
    <title>Регистрация</title>
</head>

<body>
    <div class="container_login">
        <h1>Регистрация</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" action="index.php?route=register">
            <div class="form-group">
                <label>Имя пользователя: <input type="text" name="username" required></label><br>
                <label>Email: <input type="email" name="email" required></label><br>
                <label>Пароль: <input type="password" name="password" required></label><br>
                <button type="submit">Зарегистрироваться</button>
            </div>
        </form>
        <p>Уже есть аккаунт? <a href="index.php?route=login">Войдите</a></p>
    </div>
</body>

</html>