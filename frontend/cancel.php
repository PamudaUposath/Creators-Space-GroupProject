<?php
session_start();
require_once '../backend/config/db_connect.php';

// Get user info if logged in
$user = null;
$isLoggedIn = false;

if (isset($_SESSION['user_id'])) {
    $isLoggedIn = true;
    try {
        $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Continue without user data
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled - Creators Space</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cancel-container {
            max-width: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            padding: 50px 30px;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .cancel-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: shake 0.8s ease-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .cancel-icon i {
            color: white;
            font-size: 2.5em;
        }
        
        h1 {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .cancel-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            margin: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            margin: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
        }
        
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 152, 0, 0.4);
            color: white;
        }
        
        .help-info {
            background: #fff3cd;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        
        .help-info h5 {
            color: #856404;
            margin-bottom: 10px;
        }
        
        .help-info p {
            margin: 5px 0;
            color: #856404;
        }
        
        .help-info ul {
            text-align: left;
            color: #856404;
        }
        
        .help-info li {
            margin: 5px 0;
        }
        
        .user-info {
            background: #fff0f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #ff6b6b;
        }
    </style>
</head>
<body>
    <div class="cancel-container">
        <div class="cancel-icon">
            <i class="fas fa-times"></i>
        </div>
        
        <h1>Payment Canceled</h1>
        
        <?php if ($user): ?>
            <div class="user-info">
                <p><strong>Hello <?php echo htmlspecialchars($user['first_name']); ?>!</strong></p>
                <p>Your payment was canceled and no charges were made.</p>
            </div>
        <?php endif; ?>
        
        <div class="cancel-message">
            <p>💳 Your payment has been canceled and no charges were made to your account.</p>
            <p>Don't worry! Your cart items are still saved and ready when you're ready to complete your purchase.</p>
        </div>
        
        <div class="help-info">
            <h5><i class="fas fa-question-circle"></i> Need Help?</h5>
            <ul>
                <li>Check your payment method details</li>
                <li>Ensure sufficient funds are available</li>
                <li>Try a different payment method</li>
                <li>Contact our support team if issues persist</li>
            </ul>
        </div>
        
        <div class="action-buttons">
            <?php if ($isLoggedIn): ?>
                <a href="cart.php" class="btn btn-warning">
                    <i class="fas fa-shopping-cart"></i> Return to Cart
                </a>
                <a href="checkout.php" class="btn btn-primary">
                    <i class="fas fa-credit-card"></i> Try Again
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Login to Continue
                </a>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <p class="text-muted">
                <i class="fas fa-shield-alt"></i> Your payment information is secure
            </p>
            <a href="courses.php" class="text-muted" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
            <span class="text-muted mx-2">|</span>
            <a href="index.php" class="text-muted" style="text-decoration: none;">
                <i class="fas fa-home"></i> Homepage
            </a>
        </div>
        
        <div class="mt-3">
            <small class="text-muted">
                Need assistance? Contact us at support@creatorsspace.com
            </small>
        </div>
    </div>
    
    <script>
        // Auto-redirect to cart after 60 seconds if logged in
        <?php if ($isLoggedIn): ?>
        setTimeout(function() {
            const autoRedirect = confirm("Would you like to return to your cart to try again?");
            if (autoRedirect) {
                window.location.href = 'cart.php';
            }
        }, 60000);
        <?php endif; ?>
        
        // Add interactive elements
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    </script>
</body>
</html>