<?php
echo "Current directory: " . __DIR__ . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";

$relative_path = '../backend/auth/signup_process.php';
$absolute_path = realpath(__DIR__ . '/' . $relative_path);
echo "Relative path: " . $relative_path . "\n";
echo "Resolved absolute path: " . $absolute_path . "\n";
echo "File exists: " . (file_exists($absolute_path) ? 'Yes' : 'No') . "\n";
?>
