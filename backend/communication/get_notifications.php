<?php
// backend/communication/get_notifications.php
session_start();

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Check if user is logged in
if (!isLoggedIn()) {
    errorResponse('Authentication required', 401);
}

try {
    $user_id = $_SESSION['user_id'];
    $type = $_GET['type'] ?? ''; // Filter by type if provided
    $unread_only = isset($_GET['unread_only']) && $_GET['unread_only'] === '1';
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 20);
    $offset = ($page - 1) * $limit;

    // Build WHERE clause
    $where_conditions = ['user_id = ?'];
    $params = [$user_id];

    if ($type) {
        $where_conditions[] = 'type = ?';
        $params[] = $type;
    }

    if ($unread_only) {
        $where_conditions[] = 'is_read = 0';
    }

    $where_clause = implode(' AND ', $where_conditions);

    // Get notifications
    $stmt = $pdo->prepare("
        SELECT 
            id,
            type,
            title,
            content,
            related_id,
            is_read,
            created_at,
            read_at
        FROM notifications
        WHERE $where_clause
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $notifications = $stmt->fetchAll();

    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM notifications
        WHERE $where_clause
    ");
    $count_stmt->execute(array_slice($params, 0, -2)); // Remove limit and offset
    $total = $count_stmt->fetchColumn();

    // Get unread count
    $unread_stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_total
        FROM notifications
        WHERE user_id = ? AND is_read = 0
    ");
    $unread_stmt->execute([$user_id]);
    $unread_total = $unread_stmt->fetchColumn();

    successResponse('Notifications retrieved successfully', [
        'notifications' => $notifications,
        'unread_total' => intval($unread_total),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => intval($total),
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Get notifications error: " . $e->getMessage());
    errorResponse('Failed to retrieve notifications', 500);
}
?>