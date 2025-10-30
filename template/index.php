<?php

require_once '../config/db.php';
require_once '../vendor/autoload.php';


$books = [];
$useRedis = true;

try {
    $route = isset($_GET['route']) ? $_GET['route'] : '';

    echo "<form method='get' action='index.php'>
            <input type='hidden' name='route' value='search'>
            <label>Введите имя автора:
                <input type='text' name='author' required>
            </label>
            <button type='submit'>Найти книги</button>
          </form>";

    if ($route === 'search') {
        $author = isset($_GET['author']) ? trim($_GET['author']) : '';

        if (empty($author)) {
            echo "<p>Не указан автор.</p>";
            exit;
        }

        if ($useRedis) {
            $redis = new Predis\Client();
            $cacheKey = "author_books_" . md5($author);

            $books = $redis->get($cacheKey);

            if ($books) {
                $books = json_decode($books, true);
            } else {
                $stmt = $pdo->prepare("
                    SELECT books.title, books.description, categories.name AS category_name
                    FROM books
                    JOIN categories ON books.category_id = categories.id
                    WHERE books.author = ?
                ");
                $stmt->execute([$author]);
                $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $redis->set($cacheKey, json_encode($books));
                $redis->expire($cacheKey, 3600);
            }
        } else {
            $stmt = $pdo->prepare("
                SELECT books.title, books.description, categories.name AS category_name
                FROM books
                JOIN categories ON books.category_id = categories.id
                WHERE books.author = ?
            ");
            $stmt->execute([$author]);
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($books)) {
            echo "<p>Книги автора '" . htmlspecialchars($author, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' не найдены.</p>";
        } else {
            echo "<h2>Книги автора: " . htmlspecialchars($author) . "</h2>";
            echo "<ul>";
            foreach ($books as $book) {
                echo "<li>
                        <strong>" . htmlspecialchars($book['title']) . "</strong> 
                        (Категория: " . htmlspecialchars($book['category_name']) . ")<br>
                        Описание: " . htmlspecialchars($book['description']) . "
                      </li>";
            }
            echo "</ul>";
        }
        exit;
    }

    // Основной список книг не должен отображаться при поиске
    if ($route !== 'search') {
        $stmt = $pdo->query("
            SELECT books.id, books.title, books.author, books.created_at, categories.name AS category_name
            FROM books
            JOIN categories ON books.category_id = categories.id
        ");

        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>
                        <strong>" . htmlspecialchars($row['title']) . "</strong> — " . htmlspecialchars($row['author']) .
                    " (Категория: " . htmlspecialchars($row['category_name']) . ", Дата: " . htmlspecialchars($row['created_at']) . ")
                        <a class=\"info\" href=\"index.php?route=list&id=" . $row['id'] . "\">Подробнее</a> |
                        <a class=\"edit\" href=\"index.php?route=edit&id=" . $row['id'] . "\">Редактировать</a> |
                        <a class=\"delete\" href=\"index.php?route=delete&id=" . $row['id'] . "\">Удалить</a>
                      </li>";
            }
            echo "</ul>";
        } else {
            echo "<ul>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>
                        <strong>" . htmlspecialchars($row['title']) . "</strong> — " . htmlspecialchars($row['author']) .
                    " (Категория: " . htmlspecialchars($row['category_name']) . ", Дата: " . htmlspecialchars($row['created_at']) . ")
                        <a class=\"info\" href='index.php?route=list&id=" . $row['id'] . "'>Подробнее</a>
                      </li>";
            }
            echo "</ul>";
        }
    }

} catch (Exception $e) {
    echo "Ошибка: ";
}
