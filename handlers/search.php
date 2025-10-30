<?php
require_once '../config/db.php';
require_once '../vendor/autoload.php';
require_once '../config/security.php';

$author = isset($_GET['author']) ? sanitize_text($_GET['author']) : '';
$books = [];

if (!empty($author)) {
    try {
        $stmt = $pdo->prepare("
            SELECT books.id, books.title, books.author, books.description, books.created_at, categories.name AS category_name
            FROM books
            JOIN categories ON books.category_id = categories.id
            WHERE books.author LIKE ?
        ");
    $stmt->execute(['%' . $author . '%']); 
        return $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Ошибка при выполнении поиска";
        exit;
    }
}

