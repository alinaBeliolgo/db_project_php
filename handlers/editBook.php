<?php

require_once '../config/db.php';
require_once '../migration/01_category.php';
require_once '../migration/02_books.php';
require_once '../vendor/autoload.php';
require_once '../config/security.php';

$redis = new Predis\Client();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    echo "Доступ запрещён. Только администраторы могут редактировать книги.";
    exit;
}

// Инициализация переменных
$message = '';
$title = $author = $description = '';
$created_at = date('Y-m-d');
$category_id = '';
$book_id = '';


if (isset($_GET['id'])) {
    $book_id = intsafe($_GET['id']);
    if ($book_id === null) {
        $message = 'Некорректный ID книги.';
    } else {
    
        $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->execute([$book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$book) {
            $message = 'Книга не найдена.';
        } else {
            $title = $book['title'];
            $author = $book['author'];
            $category_id = $book['category_id'];
            $description = $book['description'];
            $created_at = $book['created_at'];
        }
    }
} else {
    $message = 'Не указан ID книги для редактирования.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intsafe($_POST['book_id'] ?? null);
    $title = sanitize_text($_POST['title'] ?? '');
    $author = sanitize_text($_POST['author'] ?? '');
    $category_id = intsafe($_POST['category'] ?? null);
    $description = sanitize_text($_POST['description'] ?? '');
    $created_at = sanitize_date($_POST['created_at'] ?? null);

    // Проверка на заполненность всех полей
    if ($book_id === null || $title === '' || $author === '' || $category_id === null || $description === '' || empty($created_at)) {
        $message = 'Пожалуйста, заполните все поля.';

    // Проверка на допустимые символы в названии книги
    } elseif (!preg_match('/^[a-zA-ZА-Яа-я0-9\s]+$/u', $title)) {
        $message = 'Название книги содержит недопустимые символы.';

    // Проверка корректности дат
    } elseif (!$created_at) {
        $message = 'Некорректная дата.';

    // Проверка на дублирование книги (кроме текущей редактируемой)
    } else {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM books
            WHERE title = ? AND author = ? AND category_id = ? AND description = ? AND created_at = ?
            AND id != ?
        ");
        $stmt->execute([$title, $author, $category_id, $description, $created_at, $book_id]);

        if ($stmt->fetchColumn() > 0) {
            $message = 'Книга с такими данными уже существует.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    UPDATE books 
                    SET title = ?, author = ?, category_id = ?, description = ?, created_at = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $author, $category_id, $description, $created_at, $book_id]);
                
                if ($stmt->rowCount() > 0) {
                    $message = 'Книга успешно обновлена!';
                    $redis->del('books_list');
                } else {
                    $message = 'Не было изменений в данных книги.';
                }
            } catch (PDOException $e) {
                $message = 'Ошибка при обновлении книги: ' . $e->getMessage();
            }
        }
    }
}


