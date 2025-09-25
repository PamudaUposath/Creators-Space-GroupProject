<?php
session_start();
require_once '../backend/config/db_connect.php';

echo "<h1>Simple Login Test</h1>";

// Try to login
if (!isset($_SESSION['user_id'])) {
    $email = 'test@gmail.com';
    $password = 'password123';
    
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, username, password_hash, role FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        echo "<p style='color: green;'>✓ Logged in successfully!</p>";
    } else {
        echo "<p style='color: red;'>✗ Login failed!</p>";
    }
}

echo "<p>User ID: " . ($_SESSION['user_id'] ?? 'Not set') . "</p>";
echo "<p>Session ID: " . session_id() . "</p>";

// Test direct cart add
if (isset($_SESSION['user_id'])) {
    echo "<h2>Direct Cart Test</h2>";
    
    try {
        // Check if course exists
        $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = 1");
        $stmt->execute();
        $course = $stmt->fetch();
        
        if (!$course) {
            echo "<p style='color: red;'>Course ID 1 not found!</p>";
        } else {
            echo "<p>Course found: " . $course['title'] . " ($" . $course['price'] . ")</p>";
            
            // Check if already in cart
            $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$_SESSION['user_id'], 1]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                echo "<p>Course already in cart (ID: " . $existing['id'] . ")</p>";
            } else {
                // Add to cart
                $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], 1, 1])) {
                    echo "<p style='color: green;'>✓ Added to cart successfully!</p>";
                } else {
                    echo "<p style='color: red;'>✗ Failed to add to cart</p>";
                }
            }
        }
        
        // Show cart contents
        echo "<h3>Current Cart Contents:</h3>";
        $stmt = $pdo->prepare("
            SELECT c.id, c.quantity, co.title, co.price 
            FROM cart c 
            JOIN courses co ON c.course_id = co.id 
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $items = $stmt->fetchAll();
        
        if (count($items) > 0) {
            foreach ($items as $item) {
                echo "<p>- " . $item['title'] . " (Qty: " . $item['quantity'] . ", $" . $item['price'] . ")</p>";
            }
        } else {
            echo "<p>Cart is empty</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<h2>Navigation</h2>
<p><a href="cart.php">Go to Cart Page</a></p>
<p><a href="course-detail.php?id=1">Go to Course Detail</a></p>