<?php
/**
 * Profile API - Handle profile operations (get, update)
 * Creators-Space Project
 */

// Start output buffering to catch any unwanted output
ob_start();

session_start();
require_once '../config/db_connect.php';

// Clean any output that might have been generated
ob_clean();

// Enable CORS for API requests
header('Content-Type: application/json');
header('Access-Control/**
 * Remove user profile image
 */
function handleRemoveProfileImage($pdo, $user_id) {
    try {rigin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    sendJsonResponse([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit();
}

// Helper function to ensure clean JSON output
function sendJsonResponse($data) {
    // Clean any output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    // Set JSON header
    header('Content-Type: application/json');
    
    // Send response
    echo json_encode($data);
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

// Handle image upload requests first (before JSON parsing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    handleImageUpload($pdo, $user_id);
    exit();
}

// Parse JSON input for regular profile updates
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            handleGetProfile($pdo, $user_id);
            break;
        case 'POST':
        case 'PUT':
            handleUpdateProfile($pdo, $user_id, $input);
            break;
        case 'DELETE':
            handleRemoveProfileImage($pdo, $user_id);
            break;
        default:
            http_response_code(405);
            echo json_encode([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

/**
 * Get user profile
 */
function handleGetProfile($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, username, role, 
               profile_image, skills, bio, phone, date_of_birth, created_at
        FROM users 
        WHERE id = ? AND is_active = 1
    ");
    
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
}

/**
 * Update user profile
 */
function handleUpdateProfile($pdo, $user_id, $input) {
    // Validate required fields
    if (!isset($input['first_name']) || !isset($input['email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'First name and email are required'
        ]);
        return;
    }

    // Check if email is already taken by another user
    if (isset($input['email'])) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$input['email'], $user_id]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Email address is already in use'
            ]);
            return;
        }
    }

    // Check if username is already taken by another user
    if (isset($input['username']) && !empty($input['username'])) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$input['username'], $user_id]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Username is already taken'
            ]);
            return;
        }
    }

    // Prepare update query
    $updateFields = [];
    $params = [];

    // Define allowed fields
    $allowedFields = [
        'first_name', 'last_name', 'email', 'username', 
        'bio', 'skills', 'phone', 'date_of_birth'
    ];

    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateFields[] = "$field = ?";
            $params[] = $input[$field];
        }
    }

    if (empty($updateFields)) {
        echo json_encode([
            'success' => false,
            'message' => 'No valid fields to update'
        ]);
        return;
    }

    // Add user ID to params
    $params[] = $user_id;

    $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute($params)) {
        // Update session data
        if (isset($input['first_name'])) $_SESSION['first_name'] = $input['first_name'];
        if (isset($input['last_name'])) $_SESSION['last_name'] = $input['last_name'];
        if (isset($input['email'])) $_SESSION['email'] = $input['email'];

        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update profile'
        ]);
    }
}

/**
 * Handle image upload
 */
function handleImageUpload($pdo, $user_id) {
    if (!isset($_FILES['profile_image'])) {
        sendJsonResponse([
            'success' => false,
            'message' => 'No image file provided'
        ]);
        return;
    }

    $file = $_FILES['profile_image'];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'Invalid file type. Please upload JPEG, PNG, or GIF'
        ]);
        return;
    }

    if ($file['size'] > $maxSize) {
        sendJsonResponse([
            'success' => false,
            'message' => 'File too large. Maximum size is 5MB'
        ]);
        return;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
    $uploadPath = '../../frontend/assets/images/profiles/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $fullPath = $uploadPath . $filename;

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET profile_image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $imageUrl = './assets/images/profiles/' . $filename;
        
        if ($stmt->execute([$imageUrl, $user_id])) {
            // Update session if user is updating their own profile
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
                $_SESSION['profile_image'] = $imageUrl;
            }
            
            sendJsonResponse([
                'success' => true,
                'message' => 'Profile image updated successfully',
                'image_url' => $imageUrl
            ]);
        } else {
            // Remove uploaded file if database update failed
            unlink($fullPath);
            sendJsonResponse([
                'success' => false,
                'message' => 'Failed to update profile image in database'
            ]);
        }
    } else {
        sendJsonResponse([
            'success' => false,
            'message' => 'Failed to upload image'
        ]);
    }
}

/**
 * Remove user profile image
 */
function handleRemoveProfileImage($pdo, $user_id) {
    // Debug logging
    error_log("🔥 handleRemoveProfileImage called for user_id: " . $user_id);
    
    try {
        // Get current profile image path
        $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && !empty($user['profile_image'])) {
            // Delete the physical file if it exists and is not the default
            $imagePath = $user['profile_image'];
            $fullPath = '../../frontend/' . ltrim($imagePath, './');
            
            // Only delete if it's not the default user icon
            if (strpos($imagePath, 'userIcon_Square.png') === false && file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        
        // Update database to remove profile image
        $stmt = $pdo->prepare("UPDATE users SET profile_image = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        
        if ($stmt->execute([$user_id])) {
            // Update session data
            if (isset($_SESSION['profile_image'])) {
                unset($_SESSION['profile_image']);
            }
            
            sendJsonResponse([
                'success' => true,
                'message' => 'Profile image removed successfully'
            ]);
        } else {
            sendJsonResponse([
                'success' => false,
                'message' => 'Failed to remove profile image'
            ]);
        }
        
    } catch (Exception $e) {
        error_log("Error removing profile image: " . $e->getMessage());
        sendJsonResponse([
            'success' => false,
            'message' => 'Server error occurred'
        ]);
    }
}
?>