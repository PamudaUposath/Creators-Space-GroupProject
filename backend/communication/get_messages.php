<?php
// backend/communication/get_messages.php
session_start();

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Check if user is logged in
if (!isLoggedIn()) {
    errorResponse('Authentication required', 401);
}

try {
    $user_id = $_SESSION['user_id'];
    $other_user_id = intval($_GET['other_user_id'] ?? 0);
    $course_id = !empty($_GET['course_id']) ? intval($_GET['course_id']) : null;
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 50);
    $offset = ($page - 1) * $limit;

    if (!$other_user_id) {
        errorResponse('Other user ID is required');
    }

    // Build the WHERE clause for course filtering
    $course_where = $course_id ? "AND m.course_id = ?" : "AND m.course_id IS NULL";
    $params = [$user_id, $other_user_id, $other_user_id, $user_id];
    
    if ($course_id) {
        $params[] = $course_id;
    }

    // Get messages between two users for a specific course (or general messages)
    $sql = "
        SELECT 
            m.id,
            m.sender_id,
            m.receiver_id,
            m.course_id,
            m.subject,
            m.message,
            m.is_read,
            m.reply_to_message_id,
            m.created_at,
            m.read_at,
            u.first_name as sender_first_name,
            u.last_name as sender_last_name,
            u.role as sender_role,
            c.title as course_title,
            reply_m.message as reply_to_message
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        LEFT JOIN courses c ON m.course_id = c.id
        LEFT JOIN messages reply_m ON m.reply_to_message_id = reply_m.id
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
        AND m.is_deleted_by_sender = 0 AND m.is_deleted_by_receiver = 0
        $course_where
        ORDER BY m.created_at ASC
        LIMIT ? OFFSET ?
    ";

    $params[] = $limit;
    $params[] = $offset;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();

    // Mark messages as read for the current user
    $mark_read_params = [$user_id, $other_user_id];
    if ($course_id) {
        $mark_read_sql = "UPDATE messages SET is_read = 1, read_at = NOW() 
                          WHERE receiver_id = ? AND sender_id = ? AND is_read = 0 AND course_id = ?";
        $mark_read_params[] = $course_id;
    } else {
        $mark_read_sql = "UPDATE messages SET is_read = 1, read_at = NOW() 
                          WHERE receiver_id = ? AND sender_id = ? AND is_read = 0 AND course_id IS NULL";
    }
    
    $mark_stmt = $pdo->prepare($mark_read_sql);
    $mark_stmt->execute($mark_read_params);

    // Get total count for pagination
    $count_params = [$user_id, $other_user_id, $other_user_id, $user_id];
    if ($course_id) {
        $count_params[] = $course_id;
    }
    
    $count_sql = "
        SELECT COUNT(*) as total
        FROM messages m
        WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
        AND m.is_deleted_by_sender = 0 AND m.is_deleted_by_receiver = 0
        $course_where
    ";
    
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($count_params);
    $total = $count_stmt->fetchColumn();

    // Get other user info
    $user_stmt = $pdo->prepare("SELECT first_name, last_name, role FROM users WHERE id = ?");
    $user_stmt->execute([$other_user_id]);
    $other_user = $user_stmt->fetch();

    // Format messages
    $formatted_messages = [];
    foreach ($messages as $msg) {
        $formatted_messages[] = [
            'id' => $msg['id'],
            'sender' => [
                'id' => $msg['sender_id'],
                'name' => trim($msg['sender_first_name'] . ' ' . $msg['sender_last_name']),
                'role' => $msg['sender_role']
            ],
            'receiver_id' => $msg['receiver_id'],
            'course' => $msg['course_id'] ? [
                'id' => $msg['course_id'],
                'title' => $msg['course_title']
            ] : null,
            'subject' => $msg['subject'],
            'message' => $msg['message'],
            'is_read' => (bool)$msg['is_read'],
            'reply_to' => $msg['reply_to_message_id'] ? [
                'id' => $msg['reply_to_message_id'],
                'message' => $msg['reply_to_message']
            ] : null,
            'created_at' => $msg['created_at'],
            'read_at' => $msg['read_at'],
            'is_from_me' => $msg['sender_id'] == $user_id
        ];
    }

    successResponse('Messages retrieved successfully', [
        'messages' => $formatted_messages,
        'other_user' => [
            'id' => $other_user_id,
            'name' => trim($other_user['first_name'] . ' ' . $other_user['last_name']),
            'role' => $other_user['role']
        ],
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => intval($total),
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Get messages error: " . $e->getMessage());
    errorResponse('Failed to retrieve messages', 500);
}
?>