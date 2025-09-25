<?php
/**
 * Simple Cart API Test
 */

session_start();
require_once '../config/db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON headers
header('Content-Type: application/json');

// Debug information
$debug = [
    'session_id' => session_id(),
    'session_data' => $_SESSION,
    'method' => $_SERVER['REQUEST_METHOD'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'user_authenticated' => isset($_SESSION['user_id']),
];

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated',
        'debug' => $debug
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get cart items
        $stmt = $pdo->prepare("
            SELECT c.id as cart_id, c.quantity, c.added_at,
                   co.id as course_id, co.title, co.price
            FROM cart c
            JOIN courses co ON c.course_id = co.id
            WHERE c.user_id = ?
            ORDER BY c.added_at DESC
        ");
        
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart retrieved successfully',
            'data' => $cart_items,
            'count' => count($cart_items),
            'debug' => $debug
        ]);
        
    } elseif ($method === 'POST') {
        // Add to cart
        $input = json_decode(file_get_contents('php://input'), true);
        $course_id = $input['course_id'] ?? null;
        $quantity = $input['quantity'] ?? 1;
        
        if (!$course_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Course ID is required',
                'debug' => $debug,
                'input' => $input
            ]);
            exit();
        }
        
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = ?");
        $stmt->execute([$course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            echo json_encode([
                'success' => false,
                'message' => 'Course not found',
                'debug' => $debug
            ]);
            exit();
        }
        
        // Check if already in cart
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo json_encode([
                'success' => false,
                'message' => 'Course already in cart',
                'action' => 'already_exists',
                'debug' => $debug
            ]);
        } else {
            // Add to cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$user_id, $course_id, $quantity])) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Course added to cart successfully',
                    'action' => 'added',
                    'debug' => $debug
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to add course to cart',
                    'debug' => $debug
                ]);
            }
        }
        
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed',
            'debug' => $debug
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
        'debug' => $debug
    ]);
}
?>