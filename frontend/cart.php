<?php
session_start();
require_once '../backend/config/db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items
try {
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
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate total
    $total = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $cart_items));
    
} catch (Exception $e) {
    $cart_items = [];
    $total = 0;
    $error = "Error loading cart: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Creators-Space</title>
    <link rel="stylesheet" href="src/css/enhanced-modern.css">
    <link rel="stylesheet" href="src/css/cart.css">
    <link rel="stylesheet" href="src/css/notifications.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="cart-container">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <span class="separator">/</span>
            <a href="courses.php">Courses</a>
            <span class="separator">/</span>
            <span class="current">Shopping Cart</span>
        </nav>

        <div class="cart-content">
            <header class="cart-header">
                <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
                <p>Review your selected courses before checkout</p>
            </header>

            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif (empty($cart_items)): ?>
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Your cart is empty</h2>
                    <p>Explore our courses and add some to your cart to get started!</p>
                    <a href="courses.php" class="btn btn-primary">
                        <i class="fas fa-graduation-cap"></i> Browse Courses
                    </a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items">
                        <div class="cart-items-header">
                            <h2>Cart Items (<?php echo count($cart_items); ?>)</h2>
                        </div>

                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                                <div class="item-image">
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                                
                                <div class="item-details">
                                    <h3><a href="course-detail.php?id=<?php echo $item['course_id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></h3>
                                    <p class="item-description"><?php echo htmlspecialchars(substr($item['description'], 0, 150)); ?>...</p>
                                    
                                    <div class="item-meta">
                                        <span class="instructor">
                                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($item['instructor']); ?>
                                        </span>
                                        <span class="duration">
                                            <i class="fas fa-clock"></i> <?php echo htmlspecialchars($item['duration']); ?>
                                        </span>
                                        <span class="level level-<?php echo strtolower($item['level']); ?>">
                                            <i class="fas fa-signal"></i> <?php echo htmlspecialchars($item['level']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="item-actions">
                                    <div class="quantity-controls">
                                        <label>Quantity:</label>
                                        <div class="quantity-input">
                                            <button class="quantity-btn decrease" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" 
                                                   onchange="updateQuantity(<?php echo $item['cart_id']; ?>, this.value, true)">
                                            <button class="quantity-btn increase" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="item-price">
                                        <span class="price">$<?php echo number_format($item['price'], 2); ?></span>
                                        <?php if ($item['quantity'] > 1): ?>
                                            <span class="total-price">Total: $<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button class="remove-btn" onclick="removeFromCart(<?php echo $item['cart_id']; ?>)">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <div class="summary-card">
                            <h3>Order Summary</h3>
                            
                            <div class="summary-row">
                                <span>Items (<?php echo count($cart_items); ?>):</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            
                            <hr>
                            
                            <div class="summary-row total-row">
                                <span>Total:</span>
                                <span class="total-amount">$<?php echo number_format($total, 2); ?></span>
                            </div>
                            
                            <div class="checkout-actions">
                                <a href="checkout.php" class="btn btn-primary btn-checkout">
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </a>
                                
                                <a href="courses.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="src/js/cart.js?v=<?php echo time(); ?>"></script>
</body>
</html>