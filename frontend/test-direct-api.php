<?php
session_start();

// Set session manually for testing
$_SESSION['user_id'] = 1;  // Admin
$_SESSION['role'] = 'admin';

echo "<h2>Direct API Test</h2>";
echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test the get_messages API directly
echo "<h3>Testing get_messages.php directly</h3>";
echo "<p>Testing Admin (1) ↔ John (2) conversation</p>";

include_once '../backend/communication/get_messages.php';
?>