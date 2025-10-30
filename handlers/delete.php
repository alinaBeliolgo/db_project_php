<?php 

require_once '../config/db.php';
require_once '../vendor/autoload.php';
require_once '../config/security.php';

$redis = new Predis\Client();


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    echo "Доступ запрещён. Только администраторы могут удалять книги.";
    exit;
}

if (isset($_GET['id'])) {
    $id = intsafe($_GET['id']);
    if ($id === null) {
        http_response_code(400);
        echo "Некорректный идентификатор книги.";
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $ok = $stmt->execute([$id]);
    
    if ($ok) {
        $redis->del('books_list');
        header('Location: index.php?route=index');
        exit;
    } else {
        echo "Ошибка при удалении книги.";
    }
};