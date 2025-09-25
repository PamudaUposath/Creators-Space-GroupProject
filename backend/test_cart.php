<?php
/**
 * Test script to simulate adding to cart with a logged-in user
 */

session_start();
require_once 'config/db_connect.php';

// Simulate being logged in as user ID 14 (testuser)
$_SESSION['user_id'] = 14;
$_SESSION['username'] = 'testuser';
$_SESSION['email'] = 'test@gmail.com';

echo "Session set with user ID: " . $_SESSION['user_id'] . "\n";
echo "Username: " . $_SESSION['username'] . "\n";

// Now test adding a course to cart
$course_id = 1;
$user_id = $_SESSION['user_id'];

try {
    // Check if course exists
    $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($course) {
        echo "Course found: " . $course['title'] . " - $" . $course['price'] . "\n";
        
        // Try to add to cart
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        $result = $stmt->execute([$user_id, $course_id, 1]);
        
        if ($result) {
            echo "Successfully added to cart!\n";
            
            // Check if it's really in the cart
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$user_id, $course_id]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cart_item) {
                echo "Confirmed: Item is in cart with ID " . $cart_item['id'] . "\n";
                echo "Quantity: " . $cart_item['quantity'] . "\n";
                echo "Added at: " . $cart_item['added_at'] . "\n";
            } else {
                echo "ERROR: Item not found in cart after insert!\n";
            }
        } else {
            echo "Failed to add to cart\n";
            echo "Error info: " . print_r($stmt->errorInfo(), true) . "\n";
        }
    } else {
        echo "Course not found!\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// List all cart items for this user
echo "\nAll cart items for user:\n";
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
    
    if (empty($items)) {
        echo "No items in cart\n";
    } else {
        foreach ($items as $item) {
            echo "- " . $item['title'] . " (Qty: " . $item['quantity'] . ") - $" . $item['price'] . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error fetching cart items: " . $e->getMessage() . "\n";
}
?>