<?php
// backend/communication/send_message.php
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
    $sender_id = $_SESSION['user_id'];
    $receiver_id = intval($_POST['receiver_id'] ?? 0);
    $course_id = !empty($_POST['course_id']) ? intval($_POST['course_id']) : null;
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $reply_to = !empty($_POST['reply_to']) ? intval($_POST['reply_to']) : null;

    // Validation
    if (!$receiver_id) {
        errorResponse('Receiver ID is required');
    }

    if (!$message) {
        errorResponse('Message content is required');
    }

    if (strlen($message) > 5000) {
        errorResponse('Message is too long (max 5000 characters)');
    }

    // Check if receiver exists and is active
    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$receiver_id]);
    $receiver = $stmt->fetch();

    if (!$receiver) {
        errorResponse('Invalid receiver');
    }

    // Check user roles - instructors can message students, students can message instructors
    $sender_role = $_SESSION['role'];
    $receiver_role = $receiver['role'];

    $allowed_combinations = [
        'instructor' => ['user', 'instructor'], // instructors can message students and other instructors
        'user' => ['instructor'], // students can only message instructors
        'admin' => ['user', 'instructor', 'admin'] // admins can message anyone
    ];

    if (!isset($allowed_combinations[$sender_role]) || 
        !in_array($receiver_role, $allowed_combinations[$sender_role])) {
        errorResponse('You are not allowed to message this user');
    }

    // If course_id is provided, verify the relationship
    if ($course_id) {
        $course_check_sql = "SELECT 1 FROM courses c 
                            LEFT JOIN enrollments e ON c.id = e.course_id 
                            WHERE c.id = ? AND (
                                (c.instructor_id = ? AND e.user_id = ?) OR 
                                (c.instructor_id = ? AND e.user_id = ?) OR
                                (c.instructor_id = ? OR c.instructor_id = ?)
                            )";
        $stmt = $pdo->prepare($course_check_sql);
        $stmt->execute([$course_id, $sender_id, $receiver_id, $receiver_id, $sender_id, $sender_id, $receiver_id]);
        
        if (!$stmt->fetch()) {
            errorResponse('Invalid course relationship');
        }
    }

    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, course_id, subject, message, reply_to_message_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$sender_id, $receiver_id, $course_id, $subject, $message, $reply_to]);
    
    $message_id = $pdo->lastInsertId();

    // Create or update conversation with proper participant ordering
    $participant_1 = min($sender_id, $receiver_id);
    $participant_2 = max($sender_id, $receiver_id);
    
    // First check if conversation exists (check both possible orderings for backwards compatibility)
    $check_conv = $pdo->prepare("
        SELECT id FROM conversations 
        WHERE ((participant_1_id = ? AND participant_2_id = ?) OR (participant_1_id = ? AND participant_2_id = ?))
        AND (course_id = ? OR (course_id IS NULL AND ? IS NULL))
    ");
    $check_conv->execute([$participant_1, $participant_2, $participant_2, $participant_1, $course_id, $course_id]);
    $existing_conv = $check_conv->fetch();
    
    if ($existing_conv) {
        // Update existing conversation
        $conv_stmt = $pdo->prepare("
            UPDATE conversations 
            SET last_message_id = ?, last_message_at = NOW()
            WHERE id = ?
        ");
        $conv_stmt->execute([$message_id, $existing_conv['id']]);
    } else {
        // Create new conversation with proper ordering
        $conv_stmt = $pdo->prepare("
            INSERT INTO conversations (participant_1_id, participant_2_id, course_id, last_message_id, last_message_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $conv_stmt->execute([$participant_1, $participant_2, $course_id, $message_id]);
    }

    // Create notification for receiver
    $notification_title = $subject ?: "New message from " . $_SESSION['first_name'];
    $notification_content = strlen($message) > 100 ? substr($message, 0, 97) . '...' : $message;
    
    $notif_stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, type, title, content, related_id)
        VALUES (?, 'message', ?, ?, ?)
    ");
    $notif_stmt->execute([$receiver_id, $notification_title, $notification_content, $message_id]);

    successResponse('Message sent successfully', [
        'message_id' => $message_id,
        'sent_at' => date('Y-m-d H:i:s')
    ]);

} catch (PDOException $e) {
    error_log("Send message error: " . $e->getMessage());
    errorResponse('Failed to send message. Please try again.', 500);
}
?>