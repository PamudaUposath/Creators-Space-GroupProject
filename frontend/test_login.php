<?php
session_start();

// Set up test session for user ID 1 (admin)
$_SESSION['user_id'] = 1;
$_SESSION['first_name'] = 'Admin';
$_SESSION['last_name'] = 'User';
$_SESSION['email'] = 'admin@creatorsspace.local';
$_SESSION['role'] = 'admin';

echo "Test session created for Admin User (ID: 1)<br>";
echo "You can now test the My Courses page: <a href='mycourses.php'>Go to My Courses</a>";
?>