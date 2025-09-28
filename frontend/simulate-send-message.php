<?php
// Test message sending API directly
session_start();

// Simulate logged-in user (user ID 2 - Test User Student)
$_SESSION['user_id'] = 2;
$_SESSION['role'] = 'student';
$_SESSION['first_name'] = 'Test';

// Simulate POST data
$_POST['receiver_id'] = 14;
$_POST['message'] = 'Test message to verify conversation grouping - ' . date('H:i:s');

// Include the send_message.php file
include '../backend/communication/send_message.php';
?>