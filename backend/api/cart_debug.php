<?php
/**
 * Debug Cart API - For testing cart functionality
 */

session_start();
require_once '../config/db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Debug session information
$debug_info = [
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'user_logged_in' => isset($_SESSION['user_id']),
    'user_id' => $_SESSION['user_id'] ?? 'not_set',
    'method' => $_SERVER['REQUEST_METHOD'],
    'input' => file_get_contents('php://input')
];

// Log debug info
error_log("Cart API Debug: " . print_r($debug_info, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated',
        'debug' => $debug_info
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON input',
            'debug' => $debug_info
        ]);
        exit();
    }
    
    if (!isset($input['course_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Course ID is required',
            'debug' => $debug_info,
            'input' => $input
        ]);
        exit();
    }
    
    $course_id = $input['course_id'];
    $quantity = $input['quantity'] ?? 1;
    
    try {
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            echo json_encode([
                'success' => false,
                'message' => 'Course not found',
                'debug' => $debug_info,
                'course_id' => $course_id
            ]);
            exit();
        }
        
        // Try to insert into cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        $result = $stmt->execute([$user_id, $course_id, $quantity]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Course added to cart successfully',
                'debug' => $debug_info,
                'course' => $course,
                'inserted_id' => $pdo->lastInsertId()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to insert into cart',
                'debug' => $debug_info,
                'error_info' => $stmt->errorInfo()
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'debug' => $debug_info
        ]);
    }
} elseif ($method === 'GET') {
    // Get cart items for debugging
    try {
        $stmt = $pdo->prepare("
            SELECT c.id as cart_id, c.quantity, c.added_at,
                   co.id as course_id, co.title, co.price
            FROM cart c
            JOIN courses co ON c.course_id = co.id
            WHERE c.user_id = ?
            ORDER BY c.added_at DESC
        ");
        
        $stmt->execute([$user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'items' => $items,
            'count' => count($items),
            'debug' => $debug_info
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage(),
            'debug' => $debug_info
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not supported',
        'debug' => $debug_info
    ]);
}
?>