<?php
/**
 * Shopping Cart Functionality
 * Handles cart operations like adding items, removing items, and getting cart summary
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Initialize cart in session if not exists
 */
function initializeCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add item to cart
 */
function addToCart($courseId, $courseName, $price, $image = null) {
    initializeCart();
    
    // Check if item already exists in cart
    $itemExists = false;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['course_id'] == $courseId) {
            $_SESSION['cart'][$key]['quantity']++;
            $itemExists = true;
            break;
        }
    }
    
    // If item doesn't exist, add new item
    if (!$itemExists) {
        $_SESSION['cart'][] = [
            'course_id' => $courseId,
            'course_name' => $courseName,
            'price' => $price,
            'image' => $image,
            'quantity' => 1,
            'added_at' => time()
        ];
    }
    
    return true;
}

/**
 * Remove item from cart
 */
function removeFromCart($courseId) {
    initializeCart();
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['course_id'] == $courseId) {
            unset($_SESSION['cart'][$key]);
            // Re-index array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            return true;
        }
    }
    
    return false;
}

/**
 * Update item quantity in cart
 */
function updateCartQuantity($courseId, $quantity) {
    initializeCart();
    
    if ($quantity <= 0) {
        return removeFromCart($courseId);
    }
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['course_id'] == $courseId) {
            $_SESSION['cart'][$key]['quantity'] = intval($quantity);
            return true;
        }
    }
    
    return false;
}

/**
 * Get all cart items
 */
function getCartItems() {
    initializeCart();
    return $_SESSION['cart'];
}

/**
 * Get cart summary (count and total)
 */
function getCartSummary() {
    initializeCart();
    
    $count = 0;
    $total = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
        $total += ($item['price'] * $item['quantity']);
    }
    
    return [
        'count' => $count,
        'total' => $total,
        'items' => count($_SESSION['cart'])
    ];
}

/**
 * Clear entire cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
    return true;
}

/**
 * Check if item exists in cart
 */
function isInCart($courseId) {
    initializeCart();
    
    foreach ($_SESSION['cart'] as $item) {
        if ($item['course_id'] == $courseId) {
            return true;
        }
    }
    
    return false;
}

/**
 * Get cart total price
 */
function getCartTotal() {
    $summary = getCartSummary();
    return $summary['total'];
}

/**
 * Get cart item count
 */
function getCartCount() {
    $summary = getCartSummary();
    return $summary['count'];
}
?>