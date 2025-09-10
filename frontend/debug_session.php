<?php
// Debug session information
session_start();

echo "<h2>Session Debug Information</h2>";
echo "<h3>Session Status:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>User Authentication Status:</h3>";
$isLoggedIn = isset($_SESSION['user_id']);
echo "Is Logged In: " . ($isLoggedIn ? 'YES' : 'NO') . "<br>";

if ($isLoggedIn) {
    echo "User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "<br>";
    echo "First Name: " . ($_SESSION['first_name'] ?? 'Not set') . "<br>";
    echo "Last Name: " . ($_SESSION['last_name'] ?? 'Not set') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'Not set') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'Not set') . "<br>";
}

echo "<h3>Navigation Links:</h3>";
echo "<a href='index.php'>Go to Home Page</a><br>";
echo "<a href='login.php'>Go to Login Page</a><br>";
echo "<a href='../backend/auth/logout.php'>Logout</a><br>";
?>
