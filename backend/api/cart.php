<?php
/**
 * Cart API - Handle cart operations (add, remove, update, get)
 * Creators-Space Project
 */

session_start();
require_once '../config/db_connect.php';

// Enable CORS for API requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            handleGetCart($pdo, $user_id);
            break;
        case 'POST':
            handleAddToCart($pdo, $user_id, $input);
            break;
        case 'PUT':
            handleUpdateCart($pdo, $user_id, $input);
            break;
        case 'DELETE':
            handleRemoveFromCart($pdo, $user_id, $input);
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
 * Get user's cart items
 */
function handleGetCart($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT c.id as cart_id, c.quantity, c.added_at,
               co.id as course_id, co.title, co.description, co.price, 
               co.image_url, co.duration, co.level,
               u.username as instructor
        FROM cart c
        JOIN courses co ON c.course_id = co.id
        LEFT JOIN users u ON co.instructor_id = u.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total
    $total = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $items));
    
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ]);
}

/**
 * Add course to cart
 */
function handleAddToCart($pdo, $user_id, $input) {
    if (!isset($input['course_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Course ID is required'
        ]);
        return;
    }
    
    $course_id = $input['course_id'];
    $quantity = $input['quantity'] ?? 1;
    
    // Check if course exists
    $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Course not found'
        ]);
        return;
    }
    
    // Check if user is already enrolled in this course
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in this course'
        ]);
        return;
    }
    
    // Check if course is already in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Update quantity
        $new_quantity = $existing['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$new_quantity, $existing['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Cart updated successfully',
            'action' => 'updated'
        ]);
    } else {
        // Add new item
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $course_id, $quantity]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Course added to cart successfully',
            'action' => 'added'
        ]);
    }
}

/**
 * Update cart item quantity
 */
function handleUpdateCart($pdo, $user_id, $input) {
    if (!isset($input['cart_id']) || !isset($input['quantity'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Cart ID and quantity are required'
        ]);
        return;
    }
    
    $cart_id = $input['cart_id'];
    $quantity = max(1, $input['quantity']); // Ensure minimum quantity of 1
    
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?");
    $stmt->execute([$quantity, $cart_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cart item updated successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Cart item not found'
        ]);
    }
}

/**
 * Remove course from cart
 */
function handleRemoveFromCart($pdo, $user_id, $input) {
    if (!isset($input['cart_id']) && !isset($input['course_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Cart ID or Course ID is required'
        ]);
        return;
    }
    
    if (isset($input['cart_id'])) {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$input['cart_id'], $user_id]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE course_id = ? AND user_id = ?");
        $stmt->execute([$input['course_id'], $user_id]);
    }
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Course removed from cart successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Cart item not found'
        ]);
    }
}
?>