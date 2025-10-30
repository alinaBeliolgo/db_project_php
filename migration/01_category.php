<?php

require_once __DIR__ . '/../config/db.php';

try {
   
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    );


   $defaultCategories = ['Фантастика', 'Роман', 'Детектив', 'Научная литература', 'История'];

   foreach ($defaultCategories as $name) {
    
       $stmt = $pdo->prepare("INSERT OR IGNORE INTO categories (name) VALUES (:name)");
       $stmt->execute([':name' => $name]);
   }

} catch (PDOException $e) {
    echo "Ошибка 500: cвяжитесь с администратором сайта.";
}
