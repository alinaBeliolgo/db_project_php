<?php
require_once '../config/security.php';
$input = $_GET['demo'] ?? '';
$input = (string)$input;
?>
<h1>Демонстрация экранирования (безопасно)</h1>
<p>Введите произвольный текст, в т.ч. теги или попытки скриптов — на странице он будет показан как текст, а не выполнен.</p>
<form method="get" action="index.php">
  <input type="hidden" name="route" value="xss-demo">
  <textarea name="demo" rows="4" cols="60"><?= e($input) ?></textarea><br>
  <button type="submit">Показать</button>
</form>

<?php if ($input !== ''): ?>
  <h2>Результат вывода</h2>
  <pre style="white-space: pre-wrap; border: 1px solid #ccc; padding: 8px; background: #fafafa;"><?= e($input) ?></pre>
<?php endif; ?>

<p><a href="index.php?route=index">Назад</a></p>
