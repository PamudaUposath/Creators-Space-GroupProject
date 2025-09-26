<?php
/**
 * PayHere Payment Notification Handler
 * This file receives and processes payment notifications from PayHere
 */

// Start output buffering to prevent accidental output
ob_start();

// Include database connection
require_once '../backend/config/db_connect.php';

// PayHere Configuration (must match checkout.php)
$merchant_id = "1232176";  // Your PayHere Merchant ID
$merchant_secret = "NDAxODg5MTQzMzA0Nzg0NzUyMjQxNzM4MzA4NDE1MDM3MjE1NDc=";  // Your PayHere Merchant Secret

// Function to log messages
function logMessage($message) {
    $log_file = '../logs/payhere_notifications.log';
    $timestamp = date('Y-m-d H:i:s');
    
    // Create logs directory if it doesn't exist
    if (!file_exists('../logs')) {
        mkdir('../logs', 0755, true);
    }
    
    file_put_contents($log_file, "[$timestamp] $message" . PHP_EOL, FILE_APPEND | LOCK_EX);
}

// Function to send response to PayHere
function sendResponse($status) {
    ob_clean(); // Clear any output buffer
    echo $status;
    exit();
}

try {
    // Log the incoming notification
    logMessage("PayHere notification received: " . json_encode($_POST));
    
    // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        logMessage("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
        sendResponse('Invalid request method');
    }
    
    // Get POST data
    $merchant_id_received = $_POST['merchant_id'] ?? '';
    $order_id = $_POST['order_id'] ?? '';
    $payhere_amount = $_POST['payhere_amount'] ?? '';
    $payhere_currency = $_POST['payhere_currency'] ?? '';
    $status_code = $_POST['status_code'] ?? '';
    $md5sig = $_POST['md5sig'] ?? '';
    $payment_id = $_POST['payment_id'] ?? '';
    $method = $_POST['method'] ?? '';
    $status_message = $_POST['status_message'] ?? '';
    $card_holder_name = $_POST['card_holder_name'] ?? '';
    $card_no = $_POST['card_no'] ?? '';
    $card_expiry = $_POST['card_expiry'] ?? '';
    
    // Validate required fields
    if (empty($merchant_id_received) || empty($order_id) || empty($payhere_amount) || empty($status_code)) {
        logMessage("Missing required fields in notification");
        sendResponse('Missing required fields');
    }
    
    // Verify merchant ID
    if ($merchant_id_received !== $merchant_id) {
        logMessage("Merchant ID mismatch. Expected: $merchant_id, Received: $merchant_id_received");
        sendResponse('Merchant ID mismatch');
    }
    
    // Generate hash for verification
    $local_md5sig = strtoupper(
        md5(
            $merchant_id . 
            $order_id . 
            $payhere_amount . 
            $payhere_currency . 
            $status_code . 
            strtoupper(md5($merchant_secret))
        )
    );
    
    // Verify hash
    if ($local_md5sig !== $md5sig) {
        logMessage("Hash verification failed. Expected: $local_md5sig, Received: $md5sig");
        sendResponse('Hash verification failed');
    }
    
    // Extract user ID from order ID (assuming format: ORDER_timestamp_userid)
    $order_parts = explode('_', $order_id);
    $user_id = isset($order_parts[2]) ? intval($order_parts[2]) : 0;
    
    if ($user_id <= 0) {
        logMessage("Invalid user ID extracted from order ID: $order_id");
        sendResponse('Invalid order ID format');
    }
    
    // Check if payment already processed
    $stmt = $pdo->prepare("SELECT id FROM payments WHERE order_id = ?");
    $stmt->execute([$order_id]);
    
    if ($stmt->fetch()) {
        logMessage("Payment already processed for order: $order_id");
        sendResponse('Payment already processed');
    }
    
    // Process based on status code
    switch ($status_code) {
        case '2': // Success
            try {
                // Start transaction
                $pdo->beginTransaction();
                
                // Log successful payment
                $stmt = $pdo->prepare("
                    INSERT INTO payments (
                        user_id, order_id, payment_id, amount, currency, status, 
                        payment_method, card_holder_name, card_no, card_expiry,
                        status_message, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, 'completed', ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $user_id,
                    $order_id,
                    $payment_id,
                    $payhere_amount,
                    $payhere_currency,
                    $method,
                    $card_holder_name,
                    $card_no,
                    $card_expiry,
                    $status_message
                ]);
                
                // Get cart items for this user to create enrollments
                $stmt = $pdo->prepare("
                    SELECT c.course_id, c.quantity, co.title, co.price
                    FROM cart c
                    JOIN courses co ON c.course_id = co.id
                    WHERE c.user_id = ?
                ");
                $stmt->execute([$user_id]);
                $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Create enrollments for each course
                foreach ($cart_items as $item) {
                    // Check if already enrolled
                    $check_stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
                    $check_stmt->execute([$user_id, $item['course_id']]);
                    
                    if (!$check_stmt->fetch()) {
                        // Create enrollment
                        $enroll_stmt = $pdo->prepare("
                            INSERT INTO enrollments (user_id, course_id, enrolled_at, status)
                            VALUES (?, ?, NOW(), 'active')
                        ");
                        $enroll_stmt->execute([$user_id, $item['course_id']]);
                        
                        logMessage("Created enrollment for user $user_id in course {$item['course_id']}");
                    }
                }
                
                // Clear the user's cart
                $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
                $stmt->execute([$user_id]);
                
                // Commit transaction
                $pdo->commit();
                
                logMessage("Payment successful for order $order_id. Amount: $payhere_amount $payhere_currency");
                sendResponse('Payment successful');
                
            } catch (Exception $e) {
                // Rollback transaction
                $pdo->rollBack();
                logMessage("Error processing successful payment: " . $e->getMessage());
                sendResponse('Error processing payment');
            }
            break;
            
        case '0': // Pending
            // Log pending payment
            $stmt = $pdo->prepare("
                INSERT INTO payments (
                    user_id, order_id, payment_id, amount, currency, status,
                    payment_method, status_message, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                status = 'pending', updated_at = NOW()
            ");
            
            $stmt->execute([
                $user_id, $order_id, $payment_id, $payhere_amount, 
                $payhere_currency, $method, $status_message
            ]);
            
            logMessage("Payment pending for order $order_id");
            sendResponse('Payment pending');
            break;
            
        case '-1': // Canceled
        case '-2': // Failed
        case '-3': // Chargedback
            // Log failed/canceled payment
            $status_text = [
                '-1' => 'canceled',
                '-2' => 'failed',
                '-3' => 'chargedback'
            ][$status_code] ?? 'unknown';
            
            $stmt = $pdo->prepare("
                INSERT INTO payments (
                    user_id, order_id, payment_id, amount, currency, status,
                    payment_method, status_message, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                status = ?, updated_at = NOW()
            ");
            
            $stmt->execute([
                $user_id, $order_id, $payment_id, $payhere_amount, 
                $payhere_currency, $status_text, $method, $status_message, $status_text
            ]);
            
            logMessage("Payment $status_text for order $order_id");
            sendResponse("Payment $status_text");
            break;
            
        default:
            logMessage("Unknown status code: $status_code for order $order_id");
            sendResponse('Unknown status');
    }
    
} catch (Exception $e) {
    logMessage("Error processing PayHere notification: " . $e->getMessage());
    sendResponse('Server error');
}
?>