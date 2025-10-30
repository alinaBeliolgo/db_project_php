<?php
session_start();

require_once '../config/db.php';
require_once '../config/security.php';

// Безопасные заголовки (до любого вывода)
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer-when-downgrade");
// CSP: запрет inline-скриптов и внешних доменов; разрешаем только self
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'self'; frame-ancestors 'none'; script-src 'self'; style-src 'self';");

$route = $_GET['route'] ?? 'index';

$template = '../template/layout.php';

$publicRoutes = ['login', 'register', 'reset-password'];


if (!empty($_SESSION['user']) && in_array($route, $publicRoutes)) {
    header('Location: index.php?route=index');
    exit;
}


if (empty($_SESSION['user']) && !in_array($route, $publicRoutes)) {
    header('Location: index.php?route=login');
    exit;
}


switch ($route) {
    case 'index':
        $content = '../template/index.php';
        break;

    case 'add':
        // Добавление книги (только для администратора)
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('Доступ запрещён');
        }
        require_once '../handlers/addBook.php';
        $content = '../template/add.php';
        break;

    case 'edit':
        // Редактирование книги (только для администратора)
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('Доступ запрещён');
        }
        require_once '../handlers/editBook.php';
        $content = '../template/edit.php';
        break;

    case 'list':
        $content = '../template/list.php';
        break;

    case 'search':
        // Поиск книг
        require_once '../handlers/search.php';
        $content = '../template/search.php';
        break;

    case 'delete':
        // Удаление книги (только для администратора)
        if ($_SESSION['role'] !== 'admin') {
            http_response_code(403);
            exit('Доступ запрещён');
        }
        $content = '../handlers/delete.php';
        break;

    case 'login':
        // Страница входа
        require_once '../handlers/login.php';
        $content = '../template/login.php';
        break;


    case 'register':
        // Страница регистрации
        require_once '../handlers/register.php';
        $content = '../template/register.php';
        break;

    case 'logout':
        // Выход из системы
        session_destroy();
        header('Location: index.php?route=login');
        exit;

    case 'xss-demo':
        // Образовательная безопасная демо-страница (без выполнения кода)
        $content = '../template/xss_demo.php';
        break;

    default:
        http_response_code(404);
        $content = '../template/404.php';
        break;
}

require_once $template;
