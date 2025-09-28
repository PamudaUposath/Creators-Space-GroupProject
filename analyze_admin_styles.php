<?php
echo "=== ADMIN PAGES STYLE ANALYSIS ===\n\n";

$adminPages = [
    'backend/admin/dashboard.php' => 'Dashboard',
    'backend/admin/users.php' => 'Users',
    'backend/admin/courses.php' => 'Courses',
    'backend/admin/course-requests.php' => 'Course Requests',
    'backend/admin/enrollments.php' => 'Enrollments',
    'backend/admin/student-reports.php' => 'Student Reports'
];

$cssElements = [
    '.header' => 'Header styling',
    '.nav' => 'Navigation styling',
    '.nav-links' => 'Navigation links',
    '.main-content' => 'Main content wrapper',
    '.btn' => 'Button styling',
    '.table' => 'Table styling',
    'font-family: \'Inter\'' => 'Font family consistency'
];

foreach ($adminPages as $file => $pageName) {
    echo "=== {$pageName} ({$file}) ===\n";
    
    if (!file_exists($file)) {
        echo "❌ File not found\n\n";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Check for each CSS element
    foreach ($cssElements as $selector => $description) {
        if (strpos($content, $selector) !== false) {
            echo "✅ {$description}\n";
        } else {
            echo "❌ Missing: {$description}\n";
        }
    }
    
    // Check for specific style patterns
    $patterns = [
        'background: linear-gradient(135deg, #667eea' => 'Header gradient',
        'font-family: \'Inter\'' => 'Inter font',
        'box-shadow:' => 'Shadow effects',
        'border-radius:' => 'Rounded corners',
        'transition:' => 'Smooth transitions'
    ];
    
    echo "\nStyle patterns found:\n";
    foreach ($patterns as $pattern => $name) {
        $count = substr_count($content, $pattern);
        if ($count > 0) {
            echo "✅ {$name} (found {$count} times)\n";
        } else {
            echo "❌ {$name} not found\n";
        }
    }
    
    // Check CSS structure consistency
    if (strpos($content, '<style>') !== false && strpos($content, '</style>') !== false) {
        echo "✅ Has embedded CSS\n";
        
        // Extract CSS section
        preg_match('/<style>(.*?)<\/style>/s', $content, $matches);
        if (isset($matches[1])) {
            $css = $matches[1];
            $cssLines = count(explode("\n", $css));
            echo "📊 CSS lines: ~{$cssLines}\n";
        }
    } else {
        echo "❌ No embedded CSS found\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== INCONSISTENCY DETECTION ===\n\n";

// Check for major style inconsistencies
$allStyles = [];
foreach ($adminPages as $file => $pageName) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        preg_match('/<style>(.*?)<\/style>/s', $content, $matches);
        if (isset($matches[1])) {
            $allStyles[$pageName] = $matches[1];
        }
    }
}

// Look for common elements that should be consistent
$commonElements = ['.header', '.nav', '.nav-links', '.btn', '.main-content'];

foreach ($commonElements as $element) {
    echo "Checking {$element} consistency:\n";
    $elementStyles = [];
    
    foreach ($allStyles as $pageName => $css) {
        if (preg_match('/' . preg_quote($element, '/') . '\s*\{([^}]+)\}/s', $css, $matches)) {
            $elementStyles[$pageName] = trim($matches[1]);
        }
    }
    
    if (count(array_unique($elementStyles)) === 1) {
        echo "✅ {$element} is consistent across all pages\n";
    } else {
        echo "⚠️  {$element} has variations:\n";
        foreach ($elementStyles as $page => $style) {
            echo "  - {$page}: " . substr(str_replace("\n", " ", $style), 0, 100) . "...\n";
        }
    }
    echo "\n";
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "1. Consider extracting common CSS into a shared admin.css file\n";
echo "2. Ensure all pages use the same color scheme and spacing\n";
echo "3. Standardize button and form styling across pages\n";
echo "4. Check responsive design consistency\n";
?>