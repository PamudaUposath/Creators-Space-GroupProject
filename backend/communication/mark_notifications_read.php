<?php
// backend/communication/mark_notifications_read.php
session_start();

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Check if user is logged in
if (!isLoggedIn()) {
    errorResponse('Authentication required', 401);
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

try {
    $user_id = $_SESSION['user_id'];
    $notification_ids = $_POST['notification_ids'] ?? [];
    $mark_all = isset($_POST['mark_all']) && $_POST['mark_all'] === '1';

    if ($mark_all) {
        // Mark all notifications as read for the user
        $stmt = $pdo->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$user_id]);
        $affected_rows = $stmt->rowCount();
        
        successResponse("Marked $affected_rows notifications as read");
        
    } else {
        // Mark specific notifications as read
        if (!is_array($notification_ids) || empty($notification_ids)) {
            errorResponse('Notification IDs are required');
        }

        // Validate that all notification IDs belong to the current user
        $placeholders = str_repeat('?,', count($notification_ids) - 1) . '?';
        $check_stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM notifications 
            WHERE id IN ($placeholders) AND user_id = ?
        ");
        $check_stmt->execute([...$notification_ids, $user_id]);
        
        if ($check_stmt->fetchColumn() != count($notification_ids)) {
            errorResponse('Invalid notification IDs');
        }

        // Mark the specific notifications as read
        $stmt = $pdo->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id IN ($placeholders) AND user_id = ?
        ");
        $stmt->execute([...$notification_ids, $user_id]);
        
        $affected_rows = $stmt->rowCount();
        successResponse("Marked $affected_rows notifications as read");
    }

} catch (PDOException $e) {
    error_log("Mark notifications read error: " . $e->getMessage());
    errorResponse('Failed to mark notifications as read', 500);
}
?>