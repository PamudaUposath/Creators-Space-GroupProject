<?php
session_start();
require_once '../backend/config/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
try {
    $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: cart.php?error=user_not_found');
        exit();
    }
} catch (PDOException $e) {
    header('Location: cart.php?error=database_error');
    exit();
}

// Get cart items and calculate total
try {
    $stmt = $pdo->prepare("
        SELECT c.quantity, co.price, co.title
        FROM cart c
        JOIN courses co ON c.course_id = co.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($cart_items)) {
        header('Location: cart.php?error=empty_cart');
        exit();
    }
    
    // Calculate total
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
} catch (PDOException $e) {
    header('Location: cart.php?error=database_error');
    exit();
}

// PayHere Configuration (Sandbox)
$merchant_id = "1232176";  // PayHere Sandbox Merchant ID (for testing)
$merchant_secret = "NDAxODg5MTQzMzA0Nzg0NzUyMjQxNzM4MzA4NDE1MDM3MjE1NDc=";  // PayHere Sandbox Secret
$currency = "LKR";
$order_id = "ORDER_" . time() . "_" . $user_id;

// Generate hash for security
$hash = strtoupper(
    md5(
        $merchant_id . 
        $order_id . 
        number_format($total, 2, '.', '') . 
        $currency .  
        strtoupper(md5($merchant_secret)) 
    )
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Creators Space</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Arial', sans-serif;
        }
        
        .checkout-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .checkout-header {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .checkout-body {
            padding: 30px;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .order-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 1.2em;
            color: #4CAF50;
            border-top: 2px solid #4CAF50;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .user-details {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .pay-btn {
            background: linear-gradient(135deg, #FF6B6B 0%, #ee5a52 100%);
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.4);
        }
        
        .back-btn {
            background: #6c757d;
            border: none;
            color: white;
            padding: 10px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background: #545b62;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <div class="checkout-header">
            <i class="fas fa-credit-card fa-2x mb-3"></i>
            <h2>Checkout</h2>
            <p>Complete your course purchase</p>
        </div>
        
        <div class="checkout-body">
            <a href="cart.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </a>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h4><i class="fas fa-receipt"></i> Order Summary</h4>
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <span><?php echo htmlspecialchars($item['title']); ?> (×<?php echo $item['quantity']; ?>)</span>
                        <span>LKR <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div class="order-item total-row">
                    <span>Total Amount</span>
                    <span>LKR <?php echo number_format($total, 2); ?></span>
                </div>
            </div>
            
            <!-- User Details -->
            <div class="user-details">
                <h4><i class="fas fa-user"></i> Billing Details</h4>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            </div>
            
            <!-- PayHere Payment Form -->
            <form method="post" action="https://sandbox.payhere.lk/pay/checkout" id="payhere-form">
                <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                <input type="hidden" name="return_url" value="<?php echo 'http://localhost/Creators-Space-GroupProject/frontend/success.php'; ?>">
                <input type="hidden" name="cancel_url" value="<?php echo 'http://localhost/Creators-Space-GroupProject/frontend/cancel.php'; ?>">
                <input type="hidden" name="notify_url" value="<?php echo 'http://localhost/Creators-Space-GroupProject/frontend/notify.php'; ?>">
                
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <input type="hidden" name="items" value="Creators Space Courses">
                <input type="hidden" name="currency" value="<?php echo $currency; ?>">
                <input type="hidden" name="amount" value="<?php echo number_format($total, 2, '.', ''); ?>">
                
                <input type="hidden" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                <input type="hidden" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                <input type="hidden" name="phone" value="0771234567">
                <input type="hidden" name="address" value="Colombo">
                <input type="hidden" name="city" value="Colombo">
                <input type="hidden" name="country" value="Sri Lanka">
                
                <input type="hidden" name="hash" value="<?php echo $hash; ?>">
                
                <button type="submit" class="pay-btn">
                    <i class="fas fa-credit-card"></i>
                    Pay LKR <?php echo number_format($total, 2); ?> with PayHere
                </button>
            </form>
            
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-shield-alt"></i>
                    Secure payment powered by PayHere (Sandbox Mode)
                </small>
            </div>
        </div>
    </div>
    
    <script>
        // Optional: Add some client-side validation
        document.getElementById('payhere-form').addEventListener('submit', function(e) {
            const confirmed = confirm('Proceed with payment of LKR <?php echo number_format($total, 2); ?>?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>