<!-- Шаьблон для добавления книги -->

<h1>Добавить книгу</h1>

<?php if ($message !== ''): ?>
    <p style="color: <?= strpos($message, 'успешно') !== false ? 'green' : 'red' ?>;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form method="post">
    <label>Название:
        <input type="text" name="title" required value="<?= htmlspecialchars($title) ?>">
    </label><br>

    <label>Автор:
        <input type="text" name="author" required value="<?= htmlspecialchars($author) ?>">
    </label><br>

    <label>Описание:
        <textarea name="description"><?= htmlspecialchars($description) ?></textarea>
    </label><br>

    <label>Категория:
        <select name="category" required>
            <option value="">Select...</option>
            <?php
            // Получаем список категорий из базы данных
            $stmt = $pdo->query("SELECT id, name FROM categories");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?= $row['id'] ?>"
                    <?php echo $row['id'] == $category_id ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label><br>

    <label>Дата добавления:
        <input type="date" name="created_at" value="<?= htmlspecialchars($created_at) ?>">
    </label><br>

    <button type="submit">Сохранить</button>
</form>

<p><a href="../public/index.php">Назад</a></p>
