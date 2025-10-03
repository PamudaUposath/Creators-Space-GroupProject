<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

echo json_encode([
    'session_data' => [
        'is_logged_in' => isset($_SESSION['user_id']),
        'user_id' => $_SESSION['user_id'] ?? null,
        'user_name' => ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? ''),
        'session_keys' => array_keys($_SESSION)
    ],
    'sample_course_test' => [
        'course_id' => 1,
        'test_url' => 'get_continue_data.php?course_id=1'
    ]
]);
?>