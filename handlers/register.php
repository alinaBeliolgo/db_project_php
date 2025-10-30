<?php

require_once '../config/db.php';

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        $error = 'Пожалуйста, заполните все поля';
    } else {

        if (strlen($password) < 8) {
            $error = 'Пароль должен содержать не менее 8 символов';
        } else if (!preg_match('/[A-Z]/', $password)) {
            $error = 'Пароль должен содержать хотя бы одну заглавную букву';
        } else if (!preg_match('/[a-z]/', $password)) {
            $error = 'Пароль должен содержать хотя бы одну строчную букву';
        } else if (!preg_match('/[0-9]/', $password)) {
            $error = 'Пароль должен содержать хотя бы одну цифру';
        
        } else {

        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким именем или email уже существует';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user';
          
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$username, $email, $hashedPassword, $role]);

        
            if ($success) {
                $_SESSION['user'] = $username;
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $pdo->lastInsertId();

                header('Location: index.php?route=index');
                exit;
            } else {
                $error = 'Ошибка при регистрации. Попробуйте ещё раз.';
            }
        }
    
    }
    }
}
