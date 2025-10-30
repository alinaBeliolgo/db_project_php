<!--Шаблон страницы Входа-->

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
</head>

<body>
    <div class="container_login">
        <h1>Вход</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="index.php?route=login">
            <div class="form-group">
                <label for="username">Имя пользователя:</label>
                <input type="text" id="username" name="username" required maxlength="50" autocomplete="username">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
                <button type="submit">Войти</button>
            </div>
        </form>
        <p>У вас нет аккаунтa? <a href="index.php?route=register">Зарегистрироваться</a></p>
    </div>
</body>

</html>