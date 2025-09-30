<?php
// backend/config/db_connect.php

// Database configuration
$DB_HOST = '127.0.0.1';
$DB_NAME = 'creators_space';
$DB_USER = 'root'; // Change this for production
$DB_PASS = ''; // Change this for production (XAMPP default is empty)
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHARSET";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production, do not echo details. Log them instead.
    http_response_code(500);
    echo "Database connection failed.";
    error_log($e->getMessage());
    exit;
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
