<?php

require_once __DIR__ . '/../config/db.php';


try {
   
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS books (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            category_id INTEGER NOT NULL,
            author TEXT,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )"
    
    );


} catch (PDOException $e) {
    echo "Ошибка 500: cвяжитесь с администратором сайта.";
}
