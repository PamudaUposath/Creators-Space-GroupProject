<?php
/**
 * Complete Cart Debug Script
 */

session_start();
require_once '../backend/config/db_connect.php';

echo "<h1>Cart Debug Report</h1>";
echo "<div style='font-family: monospace;'>";

// 1. Check session
echo "<h2>1. Session Status</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "User logged in: " . (isset($_SESSION['user_id']) ? 'YES' : 'NO') . "<br>";
if (isset($_SESSION['user_id'])) {
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'Not set') . "<br>";
    echo "Email: " . ($_SESSION['email'] ?? 'Not set') . "<br>";
}

// 2. Login if needed
if (!isset($_SESSION['user_id']) && isset($_GET['login'])) {
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, username, password_hash, role FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute(['test@gmail.com']);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify('password123', $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo "<p style='color: green;'>✓ Login successful! Refreshing...</p>";
            echo "<script>setTimeout(function(){ location.reload(); }, 1000);</script>";
        } else {
            echo "<p style='color: red;'>✗ Login failed!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Login error: " . $e->getMessage() . "</p>";
    }
}

// 3. Check database cart items if logged in
if (isset($_SESSION['user_id'])) {
    echo "<h2>2. Database Cart Items</h2>";
    try {
        $stmt = $pdo->prepare("
            SELECT c.id as cart_id, c.quantity, c.added_at,
                   co.id as course_id, co.title, co.price
            FROM cart c
            JOIN courses co ON c.course_id = co.id
            WHERE c.user_id = ?
            ORDER BY c.added_at DESC
        ");
        
        $stmt->execute([$_SESSION['user_id']]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Cart items found: " . count($cart_items) . "<br><br>";
        
        if (count($cart_items) > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Cart ID</th><th>Course ID</th><th>Title</th><th>Price</th><th>Quantity</th><th>Added</th></tr>";
            foreach ($cart_items as $item) {
                echo "<tr>";
                echo "<td>" . $item['cart_id'] . "</td>";
                echo "<td>" . $item['course_id'] . "</td>";
                echo "<td>" . $item['title'] . "</td>";
                echo "<td>$" . $item['price'] . "</td>";
                echo "<td>" . $item['quantity'] . "</td>";
                echo "<td>" . $item['added_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No cart items found for user ID: " . $_SESSION['user_id'];
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
    
    // 4. Test API call
    echo "<h2>3. API Test</h2>";
    echo "<button onclick='testAPI()'>Test Get Cart API</button>";
    echo "<button onclick='addTestItem()'>Test Add Item API</button>";
    echo "<div id='apiResult'></div>";
    
    echo "<h2>4. Quick Actions</h2>";
    echo "<a href='cart.php' target='_blank'>Open Cart Page</a> | ";
    echo "<a href='course-detail.php?id=1' target='_blank'>Open Course Detail</a> | ";
    echo "<a href='?logout=1'>Logout</a>";
    
} else {
    echo "<h2>2. Login Required</h2>";
    echo "<a href='?login=1'>Login as Test User</a>";
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: cart_debug.php');
    exit();
}

echo "</div>";
?>

<script>
async function testAPI() {
    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'GET',
            credentials: 'same-origin'
        });
        
        const data = await response.json();
        document.getElementById('apiResult').innerHTML = '<h3>API Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
    } catch (error) {
        document.getElementById('apiResult').innerHTML = '<p style="color: red;">API Error: ' + error.message + '</p>';
    }
}

async function addTestItem() {
    try {
        const response = await fetch('/Creators-Space-GroupProject/backend/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                course_id: 2,
                quantity: 1
            })
        });

        const data = await response.json();
        document.getElementById('apiResult').innerHTML = '<h3>Add Item Response:</h3><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        
        if (data.success) {
            setTimeout(function() { location.reload(); }, 1000);
        }
    } catch (error) {
        document.getElementById('apiResult').innerHTML = '<p style="color: red;">Add Error: ' + error.message + '</p>';
    }
}
</script>