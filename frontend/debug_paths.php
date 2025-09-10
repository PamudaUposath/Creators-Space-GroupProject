<?php
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "HTTP Host: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Server Name: " . $_SERVER['SERVER_NAME'] . "\n";

// Check if the backend file exists
$backend_path = $_SERVER['DOCUMENT_ROOT'] . '/Creators-Space-GroupProject/backend/auth/signup_process.php';
echo "Looking for backend file at: " . $backend_path . "\n";
echo "Backend file exists: " . (file_exists($backend_path) ? 'Yes' : 'No') . "\n";

// Also check relative path
$relative_backend = __DIR__ . '/../backend/auth/signup_process.php';
echo "Relative path: " . $relative_backend . "\n";
echo "Relative file exists: " . (file_exists($relative_backend) ? 'Yes' : 'No') . "\n";
?>
