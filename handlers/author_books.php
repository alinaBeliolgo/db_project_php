<?php

require_once '../config/db.php';
require_once '../vendor/autoload.php';
require_once '../config/security.php';


$author = isset($_GET['author']) ? sanitize_text($_GET['author']) : '';

if (empty($author)) {
    echo "Не указан автор.";
    exit;
}

try {
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
} catch (Exception $e) {
    echo "Ошибка";
}