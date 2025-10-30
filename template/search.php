<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php';
require '../handlers/search.php';

$author = isset($_GET['author']) ? trim($_GET['author']) : '';
?>

<h1>Поиск книг по автору</h1>
<form method="get" action="index.php">
    <input type="hidden" name="route" value="search">
    <label>Введите имя автора:
        <input type="text" name="author" value="<?= htmlspecialchars($author) ?>" required>
    </label>
    <button type="submit">Искать</button>
</form>

<?php if (!empty($author)): ?>
    <?php if (empty($books)): ?>
        <p>Книги автора "<?= htmlspecialchars($author) ?>" не найдены.</p>
    <?php else: ?>
        <h2>Результаты поиска:</h2>
        <ul>
            <?php foreach ($books as $book): ?>
                <li>
                    <strong><?= htmlspecialchars($book['title']) ?></strong> — 
                    <?= htmlspecialchars($book['author']) ?> 
                    (Категория: <?= htmlspecialchars($book['category_name']) ?>, 
                    Дата: <?= htmlspecialchars($book['created_at']) ?>)<br>
                    <p><?= htmlspecialchars($book['description']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php endif; ?>

<a href="index.php?route=index">Назад</a>