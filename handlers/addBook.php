<?php

require_once '../config/db.php';

require_once '../migration/01_category.php';

require_once '../migration/02_books.php';
require_once '../config/security.php';

require_once '../vendor/autoload.php';


$redis = new Predis\Client();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    echo "Доступ запрещён. Только администраторы могут добавлять книги.";
    exit;
}

// Инициализация переменных
$message = '';
$title = $author = $description = '';
$created_at = date('Y-m-d');
$category_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Санитизация входных данных (серверная защита)
    $title = sanitize_text($_POST['title'] ?? '');
    $author = sanitize_text($_POST['author'] ?? '');
    $category_id = intsafe($_POST['category'] ?? null);
    $description = sanitize_text($_POST['description'] ?? '');
    $created_at = sanitize_date($_POST['created_at'] ?? null);

    if ($title === '' || $author === '' || $category_id === null || $description === '' || empty($created_at)) {
        $message = 'Пожалуйста, заполните все поля.';

    } elseif (!preg_match('/^[a-zA-ZА-Яа-я0-9\s]+$/u', $title)) {
        $message = 'Название книги содержит недопустимые символы.';

    } elseif (!$created_at) {
        $message = 'Некорректная дата.';

    } else {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM books
            WHERE title = ? AND author = ? AND category_id = ? AND description = ? AND created_at = ?
        ");
        $stmt->execute([$title, $author, $category_id, $description, $created_at]);

        if ($stmt->fetchColumn() > 0) {
            $message = 'Книга с такими данными уже существует.';
        } else {
                
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO books (title, author, category_id, description, created_at)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $author, $category_id, $description, $created_at]);
                $message = 'Книга успешно добавлена!';

                $redis->del('books_list');

                $title = $author = $description = '';
                $category_id = '';
                $created_at = date('Y-m-d');
            } catch (PDOException $e) {
                $message = 'Ошибка при добавлении книги.';
            }
        }
    }
}
