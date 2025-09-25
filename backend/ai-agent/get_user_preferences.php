<?php
/**
 * Get user preferences for AI agent
 */

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

header('Content-Type: application/json');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get user ID from session or parameter
    $userId = $_SESSION['user_id'] ?? $_GET['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode([
            'success' => true,
            'preferences' => [
                'skills' => null,
                'interests' => [],
                'skill_level' => 'beginner',
                'learning_goals' => []
            ]
        ]);
        exit;
    }
    
    // Fetch user data
    $stmt = $pdo->prepare("
        SELECT first_name, skills, role, created_at 
        FROM users 
        WHERE id = ? AND is_active = 1
    ");
    
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Get user's enrolled courses to infer interests
    $stmt = $pdo->prepare("
        SELECT c.category, c.level, COUNT(*) as count
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = ?
        GROUP BY c.category, c.level
        ORDER BY count DESC
    ");
    
    $stmt->execute([$userId]);
    $enrollments = $stmt->fetchAll();
    
    // Analyze user preferences
    $interests = [];
    $skillLevels = [];
    
    foreach ($enrollments as $enrollment) {
        $interests[] = $enrollment['category'];
        $skillLevels[] = $enrollment['level'];
    }
    
    // Determine primary skill level
    $skillLevel = 'beginner';
    if (!empty($skillLevels)) {
        $levelCounts = array_count_values($skillLevels);
        $skillLevel = array_keys($levelCounts, max($levelCounts))[0];
    }
    
    // Parse user skills
    $userSkills = [];
    if ($user['skills']) {
        $userSkills = array_map('trim', explode(',', $user['skills']));
    }
    
    // Generate learning goals based on user activity
    $learningGoals = [];
    if (empty($interests)) {
        $learningGoals = ['Explore different technology areas', 'Find your passion in tech'];
    } else {
        $primaryInterest = array_count_values($interests);
        $mainInterest = array_keys($primaryInterest, max($primaryInterest))[0];
        $learningGoals = ["Advance in " . str_replace('-', ' ', $mainInterest)];
    }
    
    $preferences = [
        'user_id' => $userId,
        'name' => $user['first_name'],
        'skills' => $userSkills,
        'interests' => array_unique($interests),
        'skill_level' => $skillLevel,
        'learning_goals' => $learningGoals,
        'enrolled_courses_count' => count($enrollments),
        'member_since' => $user['created_at'],
        'role' => $user['role']
    ];
    
    echo json_encode([
        'success' => true,
        'preferences' => $preferences
    ]);

} catch (Exception $e) {
    error_log("Error fetching user preferences: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Could not fetch user preferences',
        'preferences' => [
            'skills' => null,
            'interests' => [],
            'skill_level' => 'beginner',
            'learning_goals' => []
        ]
    ]);
}
?>