<?php
// backend/lib/helpers.php

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate secure random token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Check if user is instructor
 */
function isInstructor() {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'instructor';
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin($redirectUrl = '/backend/public/admin_login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Redirect to login if not admin
 */
function requireAdmin($redirectUrl = '/backend/public/admin_login.php') {
    if (!isAdmin()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function errorResponse($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}

/**
 * Send success response
 */
function successResponse($message, $data = null) {
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    jsonResponse($response);
}

/**
 * Rate limiting check (simple implementation)
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) { // 15 minutes
    $attempts = $_SESSION["rate_limit_$identifier"] ?? [];
    $now = time();
    
    // Remove attempts outside time window
    $attempts = array_filter($attempts, function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });
    
    if (count($attempts) >= $maxAttempts) {
        return false;
    }
    
    $attempts[] = $now;
    $_SESSION["rate_limit_$identifier"] = $attempts;
    return true;
}

/**
 * Send email (placeholder - implement with your preferred mail service)
 */
function sendEmail($to, $subject, $message, $headers = '') {
    // For development, you can log emails or use a service like Mailtrap
    // For production, integrate with SMTP or a service like SendGrid
    
    // Simple PHP mail (not recommended for production)
    // return mail($to, $subject, $message, $headers);
    
    // For now, just log the email
    error_log("EMAIL TO: $to, SUBJECT: $subject, MESSAGE: $message");
    return true; // Return true for development
}

/**
 * Generate password reset email content
 */
function getPasswordResetEmailContent($resetLink, $firstName) {
    return "
    <html>
    <body>
        <h2>Password Reset Request</h2>
        <p>Hello $firstName,</p>
        <p>You have requested to reset your password. Click the link below to reset it:</p>
        <p><a href='$resetLink'>Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this, please ignore this email.</p>
        <p>Best regards,<br>Creators-Space Team</p>
    </body>
    </html>
    ";
}

/**
 * Log user activity (for security monitoring)
 */
function logActivity($userId, $action, $details = '') {
    // This could write to a separate activity log table
    error_log("USER ACTIVITY - User ID: $userId, Action: $action, Details: $details");
}

/**
 * Get platform statistics for about page
 */
function getPlatformStatistics($pdo) {
    try {
        $stats = [];
        
        // Get total students enrolled (users with role 'user', excluding removed)
        $stmt = $pdo->query("SELECT COUNT(*) as total_students FROM users WHERE role = 'user' AND is_active = 1 AND (remove IS NULL OR remove = 0)");
        $stats['students'] = $stmt->fetchColumn();
        
        // Get total expert instructors (users with role 'instructor', excluding removed)
        $stmt = $pdo->query("SELECT COUNT(*) as total_instructors FROM users WHERE role = 'instructor' AND is_active = 1 AND (remove IS NULL OR remove = 0)");
        $stats['instructors'] = $stmt->fetchColumn();
        
        // Get total courses available
        $stmt = $pdo->query("SELECT COUNT(*) as total_courses FROM courses WHERE is_active = 1");
        $stats['courses'] = $stmt->fetchColumn();
        
        // Calculate success rate (students who completed at least one course)
        $stmt = $pdo->query("
            SELECT 
                COUNT(DISTINCT user_id) as completed_students,
                (SELECT COUNT(*) FROM users WHERE role = 'user' AND is_active = 1 AND (remove IS NULL OR remove = 0)) as total_students
            FROM enrollments 
            WHERE progress = 100.00 OR completed_at IS NOT NULL
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['total_students'] > 0) {
            $stats['success_rate'] = round(($result['completed_students'] / $result['total_students']) * 100);
        } else {
            $stats['success_rate'] = 0;
        }
        
        // Format numbers for display
        $stats['students_display'] = formatNumber($stats['students']);
        $stats['instructors_display'] = formatNumber($stats['instructors']);
        $stats['courses_display'] = formatNumber($stats['courses']);
        $stats['success_rate_display'] = $stats['success_rate'] . '%';
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Error fetching platform statistics: " . $e->getMessage());
        // Return default values in case of error
        return [
            'students' => 0,
            'instructors' => 0,
            'courses' => 0,
            'success_rate' => 0,
            'students_display' => '0',
            'instructors_display' => '0',
            'courses_display' => '0',
            'success_rate_display' => '0%'
        ];
    }
}

/**
 * Format numbers for display (e.g., 1500 -> 1.5K, 12000 -> 12K)
 */
function formatNumber($number) {
    if ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'K';
    } else {
        return $number;
    }
}

/**
 * Get featured courses from database
 */
function getFeaturedCourses($pdo, $limit = 3) {
    try {
        // First try to get courses marked as featured
        $stmt = $pdo->prepare("
            SELECT c.id, c.title, c.description, c.image_url, c.duration, c.level, 
                   c.price, c.category, c.slug,
                   u.first_name as instructor_first_name, u.last_name as instructor_last_name,
                   COUNT(e.id) as enrollment_count
            FROM courses c 
            LEFT JOIN users u ON c.instructor_id = u.id 
            LEFT JOIN enrollments e ON c.id = e.course_id 
            WHERE c.is_active = 1 AND c.featured = 1 
            GROUP BY c.id 
            ORDER BY c.created_at DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        $courses = $stmt->fetchAll();
        
        // If no featured courses, get most popular courses (by enrollment count)
        if (empty($courses)) {
            $stmt = $pdo->prepare("
                SELECT c.id, c.title, c.description, c.image_url, c.duration, c.level, 
                       c.price, c.category, c.slug,
                       u.first_name as instructor_first_name, u.last_name as instructor_last_name,
                       COUNT(e.id) as enrollment_count
                FROM courses c 
                LEFT JOIN users u ON c.instructor_id = u.id 
                LEFT JOIN enrollments e ON c.id = e.course_id 
                WHERE c.is_active = 1 
                GROUP BY c.id 
                ORDER BY enrollment_count DESC, c.created_at DESC 
                LIMIT ?
            ");
            
            $stmt->execute([$limit]);
            $courses = $stmt->fetchAll();
        }
        
        // Add some default data and format the courses
        foreach ($courses as &$course) {
            // Generate badge based on enrollment count or other criteria
            if (!isset($course['badge'])) {
                if ($course['enrollment_count'] > 100) {
                    $course['badge'] = 'Popular';
                } elseif (strtotime($course['created_at'] ?? '') > strtotime('-30 days')) {
                    $course['badge'] = 'New';
                } else {
                    $course['badge'] = 'Trending';
                }
            }
            
            // Generate rating (placeholder - you can implement actual rating system later)
            if (!isset($course['rating'])) {
                $course['rating'] = number_format(4.5 + (rand(3, 9) / 10), 1);
                $course['review_count'] = $course['enrollment_count'] > 0 ? 
                    rand(max(1, $course['enrollment_count'] / 5), $course['enrollment_count']) : 
                    rand(50, 500);
            }
            
            // Format instructor name
            $course['instructor_name'] = trim(($course['instructor_first_name'] ?? '') . ' ' . ($course['instructor_last_name'] ?? ''));
            if (empty($course['instructor_name'])) {
                $course['instructor_name'] = 'Creators Space Team';
            }
            
            // Ensure image URL is available
            if (empty($course['image_url'])) {
                // Set default images based on category or title
                if (stripos($course['title'], 'web') !== false || stripos($course['title'], 'full stack') !== false) {
                    $course['image_url'] = './assets/images/full-stack-web-developer.png';
                } elseif (stripos($course['title'], 'python') !== false || stripos($course['title'], 'data') !== false) {
                    $course['image_url'] = './assets/images/webdev.png';
                } elseif (stripos($course['title'], 'ui') !== false || stripos($course['title'], 'ux') !== false || stripos($course['title'], 'design') !== false) {
                    $course['image_url'] = './assets/images/google-looker-seeklogo.svg';
                } else {
                    $course['image_url'] = './assets/images/webdev.png';
                }
            }
            
            // Format level for display
            $course['level_display'] = ucfirst($course['level'] ?? 'beginner');
        }
        
        return $courses;
        
    } catch (Exception $e) {
        error_log("Error fetching featured courses: " . $e->getMessage());
        return [];
    }
}
?>
