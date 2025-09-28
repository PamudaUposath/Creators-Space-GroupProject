<?php
// Test script to verify statistics function
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/lib/helpers.php';

echo "<h2>Platform Statistics Test</h2>\n";

try {
    $stats = getPlatformStatistics($pdo);
    
    echo "<h3>Raw Statistics:</h3>\n";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    echo "<h3>Formatted for Display:</h3>\n";
    echo "<ul>";
    echo "<li>Students Enrolled: {$stats['students_display']}</li>";
    echo "<li>Expert Instructors: {$stats['instructors_display']}</li>";
    echo "<li>Courses Available: {$stats['courses_display']}</li>";
    echo "<li>Success Rate: {$stats['success_rate_display']}</li>";
    echo "</ul>";
    
    echo "<p><strong>Statistics fetched successfully!</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>