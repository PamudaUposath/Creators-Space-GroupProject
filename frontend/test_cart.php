<?php
/**
 * Test page to simulate login and test cart functionality
 */

session_start();

// Check if we want to login as test user
if (isset($_GET['login'])) {
    $_SESSION['user_id'] = 14;
    $_SESSION['username'] = 'testuser';
    $_SESSION['email'] = 'test@gmail.com';
    echo "<p style='color: green;'>Successfully logged in as testuser (ID: 14)</p>";
}

// Check if we want to logout
if (isset($_GET['logout'])) {
    session_destroy();
    session_start();
    echo "<p style='color: red;'>Logged out successfully</p>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart Test Page</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .button { display: inline-block; padding: 10px 20px; margin: 5px; color: white; text-decoration: none; border-radius: 5px; }
        .login { background-color: #007bff; }
        .logout { background-color: #dc3545; }
        .test { background-color: #28a745; }
        .cart { background-color: #17a2b8; }
    </style>
</head>
<body>
    <h1>Cart Testing Page</h1>
    
    <h2>Current Session Status:</h2>
    <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
    <p><strong>Logged In:</strong> <?php echo isset($_SESSION['user_id']) ? 'Yes' : 'No'; ?></p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p><strong>User ID:</strong> <?php echo $_SESSION['user_id']; ?></p>
        <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $_SESSION['email']; ?></p>
    <?php endif; ?>
    
    <h2>Actions:</h2>
    <a href="?login=1" class="button login">Login as Test User</a>
    <a href="?logout=1" class="button logout">Logout</a>
    <a href="cart.php" class="button cart" target="_blank">Open Cart Page</a>
    <a href="course-detail.php?id=1" class="button test" target="_blank">Open Course Detail</a>
    
    <h2>Test Add to Cart via AJAX:</h2>
    <button onclick="testAddToCart()" class="button test">Test Add Course 2 to Cart</button>
    <div id="result"></div>
    
    <script>
    async function testAddToCart() {
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
            document.getElementById('result').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        } catch (error) {
            document.getElementById('result').innerHTML = '<p style="color: red;">Error: ' + error.message + '</p>';
        }
    }
    </script>
</body>
</html>