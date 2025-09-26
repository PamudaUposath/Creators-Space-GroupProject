<?php
session_start();
require_once '../backend/config/db_connect.php';

echo "<!DOCTYPE html><html><head><title>Cart Debug</title></head><body>";
echo "<h1>Cart Database Debug</h1>";

// Check session
echo "<h2>1. Session Status</h2>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User logged in: ID = " . $_SESSION['user_id'] . "<br>";
    echo "Name: " . ($_SESSION['first_name'] ?? 'Unknown') . "<br>";
    $user_id = $_SESSION['user_id'];
} else {
    echo "❌ User NOT logged in<br>";
    echo "<p><a href='login.php'>Please login first</a></p>";
    echo "</body></html>";
    exit;
}

// Test database connection
echo "<h2>2. Database Connection</h2>";
try {
    $test = $pdo->query("SELECT 1");
    echo "✅ Database connection working<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Check cart table
echo "<h2>3. Cart Table Status</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM cart");
    $result = $stmt->fetch();
    echo "✅ Cart table accessible, current entries: " . $result['count'] . "<br>";
} catch (Exception $e) {
    echo "❌ Cart table error: " . $e->getMessage() . "<br>";
}

// Test manual cart insertion
echo "<h2>4. Manual Cart Test</h2>";
try {
    // Check if course 1 exists
    $stmt = $pdo->prepare("SELECT id, title FROM courses WHERE id = 1");
    $stmt->execute();
    $course = $stmt->fetch();
    
    if ($course) {
        echo "✅ Course exists: " . $course['title'] . "<br>";
        
        // Try to insert into cart
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND course_id = 1");
        $stmt->execute([$user_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            echo "⚠️ Course already in cart (ID: " . $existing['id'] . ", Qty: " . $existing['quantity'] . ")<br>";
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, course_id, quantity) VALUES (?, 1, 1)");
            $result = $stmt->execute([$user_id]);
            
            if ($result) {
                echo "✅ Successfully inserted into cart<br>";
            } else {
                echo "❌ Failed to insert into cart<br>";
            }
        }
    } else {
        echo "❌ Course 1 does not exist<br>";
    }
} catch (Exception $e) {
    echo "❌ Manual cart test failed: " . $e->getMessage() . "<br>";
}

// Show current cart contents
echo "<h2>5. Current Cart Contents</h2>";
try {
    $stmt = $pdo->prepare("
        SELECT c.*, co.title, co.price 
        FROM cart c 
        JOIN courses co ON c.course_id = co.id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $items = $stmt->fetchAll();
    
    if ($items) {
        echo "<table border='1'><tr><th>ID</th><th>Course</th><th>Price</th><th>Quantity</th><th>Added</th></tr>";
        foreach ($items as $item) {
            echo "<tr>";
            echo "<td>" . $item['id'] . "</td>";
            echo "<td>" . $item['title'] . "</td>";
            echo "<td>$" . $item['price'] . "</td>";
            echo "<td>" . $item['quantity'] . "</td>";
            echo "<td>" . $item['added_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No items in cart<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching cart: " . $e->getMessage() . "<br>";
}

// Test the actual cart API
echo "<h2>6. Cart API Test</h2>";
echo "<button onclick='testAPI()'>Test Add to Cart API</button>";
echo "<div id='apiResult'></div>";

echo "<script>
async function testAPI() {
    const resultDiv = document.getElementById('apiResult');
    resultDiv.innerHTML = 'Testing...';
    
    try {
        const response = await fetch('../backend/api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            credentials: 'same-origin',
            body: JSON.stringify({ course_id: 2, quantity: 1 })
        });
        
        console.log('Response status:', response.status);
        
        if (response.ok) {
            const data = await response.json();
            resultDiv.innerHTML = '<strong>API Response:</strong><pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } else {
            const errorText = await response.text();
            resultDiv.innerHTML = '<strong>API Error (' + response.status + '):</strong><pre>' + errorText + '</pre>';
        }
    } catch (error) {
        resultDiv.innerHTML = '<strong>JavaScript Error:</strong><pre>' + error.message + '</pre>';
    }
}
</script>";

echo "</body></html>";
?>