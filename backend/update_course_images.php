<?php
// Update course images in the database

require_once __DIR__ . '/config/db_connect.php';

try {
    echo "🖼️  Updating course images...\n\n";

    // Define image mappings for existing courses
    $courseImages = [
        'Full Stack Web Development' => './assets/images/full-stack-web-developer.png',
        'UI/UX Design Fundamentals' => './assets/images/blogpage/uiux.jpeg',
        'JavaScript in 30 Days' => './assets/images/blogpage/jsin30days.png',
        'Python for Beginners' => './assets/images/webdev.png',
        'React.js Masterclass' => './assets/images/full-stack-web-developer.png',
        'Data Science with Python' => './assets/images/google-looker-seeklogo.svg',
        // Add images for the additional courses
        'Blockchain Development' => './assets/images/webdev.png',
        'Cloud Computing with AWS' => './assets/images/google-looker-seeklogo.svg',
        'Cybersecurity Essentials' => './assets/images/webdev.png',
        'Digital Marketing Mastery' => './assets/images/blogpage/techstartup.jpeg',
        'Game Development with Unity' => './assets/images/webdev.png',
        'JavaScript Advanced Concepts' => './assets/images/blogpage/jsin30days.png',
        'Machine Learning Fundamentals' => './assets/images/google-looker-seeklogo.svg',
        'Mobile App Development' => './assets/images/webdev.png'
    ];

    // Update each course with its corresponding image
    $stmt = $pdo->prepare("UPDATE courses SET image_url = ? WHERE title = ?");
    
    $updatedCount = 0;
    foreach ($courseImages as $title => $imageUrl) {
        $stmt->execute([$imageUrl, $title]);
        if ($stmt->rowCount() > 0) {
            echo "✅ Updated image for: $title → $imageUrl\n";
            $updatedCount++;
        } else {
            echo "⚠️  Course not found: $title\n";
        }
    }

    echo "\n📊 Updated $updatedCount course images successfully!\n";

    // Show updated courses
    echo "\n📚 Current courses with images:\n";
    echo "================================\n";
    
    $courses = $pdo->query("SELECT title, image_url FROM courses WHERE is_active = 1 ORDER BY title")->fetchAll();
    
    foreach ($courses as $course) {
        $imageStatus = $course['image_url'] ? "✅ " . $course['image_url'] : "❌ No image";
        echo "• {$course['title']}: $imageStatus\n";
    }

} catch (PDOException $e) {
    echo "❌ Error updating course images: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Course image update completed!\n";
?>