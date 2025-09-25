<?php
// Test admin panel navigation
echo "Admin panel test - " . date('Y-m-d H:i:s');
echo "<br>";
echo "Testing navigation links...";
echo "<br>";

// Test database connection
try {
    require_once __DIR__ . '/../config/db_connect.php';
    echo "✅ Database connection: OK<br>";
} catch (Exception $e) {
    echo "❌ Database connection: " . $e->getMessage() . "<br>";
}

// Test helpers
try {
    require_once __DIR__ . '/../lib/helpers.php';
    echo "✅ Helpers loaded: OK<br>";
} catch (Exception $e) {
    echo "❌ Helpers: " . $e->getMessage() . "<br>";
}

// Test admin files exist
$adminFiles = [
    'dashboard.php',
    'users.php', 
    'courses.php',
    'enrollments.php'
];

foreach ($adminFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $file: EXISTS<br>";
    } else {
        echo "❌ $file: MISSING<br>";
    }
}

// Test removed files don't exist
$removedFiles = [
    'internships.php',
    'blog.php',
    'settings.php'
];

foreach ($removedFiles as $file) {
    if (!file_exists(__DIR__ . '/' . $file)) {
        echo "✅ $file: CORRECTLY REMOVED<br>";
    } else {
        echo "⚠️ $file: STILL EXISTS (should be removed)<br>";
    }
}
?>