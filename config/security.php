<?php
// Общие функции безопасности для проекта

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sanitize_text')) {
    // Убирает HTML-теги, нормализует пробелы
    function sanitize_text(?string $value): string {
        $value = $value ?? '';
        // удаляем управляющие символы кроме пробелов и перевода строки
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
        $value = strip_tags($value);
        // заменяем множественные пробелы на один
        $value = preg_replace('/\s+/u', ' ', $value);
        return trim($value);
    }
}

if (!function_exists('sanitize_date')) {
    function sanitize_date(?string $date): ?string {
        if (!$date) return null;
        $dt = DateTime::createFromFormat('Y-m-d', $date);
        return $dt && $dt->format('Y-m-d') === $date ? $date : null;
    }
}

if (!function_exists('intsafe')) {
    function intsafe($value): ?int {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        return $int === false ? null : (int)$int;
    }
}
