<?php
// Test conversation creation logic
session_start();

require_once __DIR__ . '/../backend/config/db_connect.php';

// Simulate logged-in user (user ID 2)
$_SESSION['user_id'] = 2;
$_SESSION['role'] = 'student';
$_SESSION['first_name'] = 'Test';

echo "<h2>Testing Conversation Creation Logic</h2>";

// Show current conversations
echo "<h3>Current Conversations (Before):</h3>";
$stmt = $pdo->prepare("SELECT * FROM conversations ORDER BY created_at DESC");
$stmt->execute();
$conversations = $stmt->fetchAll();
echo "<pre>";
print_r($conversations);
echo "</pre>";

// Show current messages
echo "<h3>Current Messages (Before):</h3>";
$stmt = $pdo->prepare("SELECT id, sender_id, receiver_id, message, created_at FROM messages ORDER BY created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll();
echo "<pre>";
print_r($messages);
echo "</pre>";

// Simulate sending a new message from user 2 to user 14 (should update existing conversation)
echo "<h3>Simulating Message Send (User 2 → User 14):</h3>";

$sender_id = 2;
$receiver_id = 14;
$course_id = null;
$message = "This is a test message to verify conversation grouping";

try {
    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, course_id, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$sender_id, $receiver_id, $course_id, $message]);
    $message_id = $pdo->lastInsertId();
    
    echo "Message inserted with ID: $message_id<br>";

    // Test the new conversation logic
    $participant_1 = min($sender_id, $receiver_id);
    $participant_2 = max($sender_id, $receiver_id);
    
    echo "Participant 1: $participant_1, Participant 2: $participant_2<br>";
    
    // Check if conversation exists
    $check_conv = $pdo->prepare("
        SELECT id FROM conversations 
        WHERE participant_1_id = ? AND participant_2_id = ? 
        AND (course_id = ? OR (course_id IS NULL AND ? IS NULL))
    ");
    $check_conv->execute([$participant_1, $participant_2, $course_id, $course_id]);
    $existing_conv = $check_conv->fetch();
    
    if ($existing_conv) {
        echo "Found existing conversation ID: " . $existing_conv['id'] . "<br>";
        // Update existing conversation
        $conv_stmt = $pdo->prepare("
            UPDATE conversations 
            SET last_message_id = ?, last_message_at = NOW()
            WHERE id = ?
        ");
        $conv_stmt->execute([$message_id, $existing_conv['id']]);
        echo "Updated existing conversation<br>";
    } else {
        echo "No existing conversation found, creating new one<br>";
        // Create new conversation
        $conv_stmt = $pdo->prepare("
            INSERT INTO conversations (participant_1_id, participant_2_id, course_id, last_message_id, last_message_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $conv_stmt->execute([$participant_1, $participant_2, $course_id, $message_id]);
        echo "Created new conversation<br>";
    }
    
    echo "<b>Success!</b><br>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

// Show conversations after
echo "<h3>Current Conversations (After):</h3>";
$stmt = $pdo->prepare("SELECT * FROM conversations ORDER BY created_at DESC");
$stmt->execute();
$conversations = $stmt->fetchAll();
echo "<pre>";
print_r($conversations);
echo "</pre>";

// Show messages after
echo "<h3>Current Messages (After):</h3>";
$stmt = $pdo->prepare("SELECT id, sender_id, receiver_id, message, created_at FROM messages ORDER BY created_at DESC");
$stmt->execute();
$messages = $stmt->fetchAll();
echo "<pre>";
print_r($messages);
echo "</pre>";
?>