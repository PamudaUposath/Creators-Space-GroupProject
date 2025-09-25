<!DOCTYPE html>
<html>
<head>
    <title>Cart Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        button { padding: 10px 20px; margin: 5px; }
        .results { background: #f5f5f5; padding: 10px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Add to Cart Functionality Test</h1>
    
    <!-- Session Check -->
    <div class="test-section">
        <h2>Session Status</h2>
        <?php 
        session_start();
        if (isset($_SESSION['user_id'])) {
            echo "<p style='color: green;'>✓ User is logged in (ID: " . $_SESSION['user_id'] . ")</p>";
        } else {
            echo "<p style='color: red;'>✗ User is not logged in</p>";
            echo "<p><a href='login.php'>Login to test cart functionality</a></p>";
        }
        ?>
    </div>

    <!-- Manual Cart Test -->
    <div class="test-section">
        <h2>Manual Cart Test</h2>
        <button id="testCartBtn" onclick="testAddToCart()">Test Add to Cart (Course ID: 3)</button>
        <div id="results" class="results"></div>
    </div>

    <script>
        async function testAddToCart() {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = 'Testing...';
            
            try {
                console.log('Making request to cart API...');
                const response = await fetch('../backend/api/cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        course_id: 3,
                        quantity: 1
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                let responseText = await response.text();
                console.log('Raw response:', responseText);
                
                resultsDiv.innerHTML = `
                    <strong>Response Status:</strong> ${response.status}<br>
                    <strong>Content-Type:</strong> ${contentType}<br>
                    <strong>Raw Response:</strong> ${responseText}
                `;

                if (contentType && contentType.includes('application/json')) {
                    try {
                        const data = JSON.parse(responseText);
                        resultsDiv.innerHTML += `<br><strong>Parsed JSON:</strong> ${JSON.stringify(data, null, 2)}`;
                    } catch (e) {
                        resultsDiv.innerHTML += `<br><strong>JSON Parse Error:</strong> ${e.message}`;
                    }
                }

            } catch (error) {
                console.error('Error:', error);
                resultsDiv.innerHTML = 'Error: ' + error.message;
            }
        }
    </script>
</body>
</html>