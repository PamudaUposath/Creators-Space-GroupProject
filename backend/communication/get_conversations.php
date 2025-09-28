<?php
// backend/communication/get_conversations.php
session_start();

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Check if user is logged in
if (!isLoggedIn()) {
    errorResponse('Authentication required', 401);
}

try {
    $user_id = $_SESSION['user_id'];
    $page = intval($_GET['page'] ?? 1);
    $limit = intval($_GET['limit'] ?? 20);
    $offset = ($page - 1) * $limit;

    // Get conversations with latest message info
    $stmt = $pdo->prepare("
        SELECT 
            c.id as conversation_id,
            c.course_id,
            c.last_message_at,
            CASE 
                WHEN c.participant_1_id = ? THEN c.participant_2_id 
                ELSE c.participant_1_id 
            END as other_user_id,
            CASE 
                WHEN c.participant_1_id = ? THEN u2.first_name 
                ELSE u1.first_name 
            END as other_user_first_name,
            CASE 
                WHEN c.participant_1_id = ? THEN u2.last_name 
                ELSE u1.last_name 
            END as other_user_last_name,
            CASE 
                WHEN c.participant_1_id = ? THEN u2.role 
                ELSE u1.role 
            END as other_user_role,
            course.title as course_title,
            m.message as last_message,
            m.sender_id as last_message_sender_id,
            m.created_at as last_message_created_at,
            (SELECT COUNT(*) FROM messages 
             WHERE receiver_id = ? AND is_read = 0 
             AND ((sender_id = c.participant_1_id AND receiver_id = c.participant_2_id) 
                  OR (sender_id = c.participant_2_id AND receiver_id = c.participant_1_id))
             AND (course_id = c.course_id OR (course_id IS NULL AND c.course_id IS NULL))
            ) as unread_count
        FROM conversations c
        JOIN users u1 ON c.participant_1_id = u1.id
        JOIN users u2 ON c.participant_2_id = u2.id
        LEFT JOIN courses course ON c.course_id = course.id
        LEFT JOIN messages m ON c.last_message_id = m.id
        WHERE (c.participant_1_id = ? OR c.participant_2_id = ?)
        AND u1.role != 'admin' AND u2.role != 'admin'
        ORDER BY c.last_message_at DESC
        LIMIT ? OFFSET ?
    ");
    
    $stmt->execute([
        $user_id, $user_id, $user_id, $user_id, $user_id,
        $user_id, $user_id, $limit, $offset
    ]);
    
    $conversations = $stmt->fetchAll();

    // Get total count for pagination
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM conversations c
        JOIN users u1 ON c.participant_1_id = u1.id
        JOIN users u2 ON c.participant_2_id = u2.id
        WHERE (c.participant_1_id = ? OR c.participant_2_id = ?)
        AND u1.role != 'admin' AND u2.role != 'admin'
    ");
    $count_stmt->execute([$user_id, $user_id]);
    $total = $count_stmt->fetchColumn();

    // Format the response
    $formatted_conversations = [];
    foreach ($conversations as $conv) {
        $formatted_conversations[] = [
            'conversation_id' => $conv['conversation_id'],
            'other_user' => [
                'id' => $conv['other_user_id'],
                'name' => trim($conv['other_user_first_name'] . ' ' . $conv['other_user_last_name']),
                'role' => $conv['other_user_role']
            ],
            'course' => $conv['course_id'] ? [
                'id' => $conv['course_id'],
                'title' => $conv['course_title']
            ] : null,
            'last_message' => [
                'content' => $conv['last_message'],
                'sender_id' => $conv['last_message_sender_id'],
                'created_at' => $conv['last_message_created_at'],
                'is_from_me' => $conv['last_message_sender_id'] == $user_id
            ],
            'unread_count' => intval($conv['unread_count']),
            'last_activity' => $conv['last_message_at']
        ];
    }

    successResponse('Conversations retrieved successfully', [
        'conversations' => $formatted_conversations,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => intval($total),
            'total_pages' => ceil($total / $limit)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Get conversations error: " . $e->getMessage());
    errorResponse('Failed to retrieve conversations', 500);
}
?>