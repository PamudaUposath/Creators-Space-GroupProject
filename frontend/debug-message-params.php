<?php
session_start();

echo "<h2>Debug Session and API Call</h2>";

echo "<h3>Current Session:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Test API Call Debug</h3>";

if (isset($_SESSION['user_id'])) {
    $current_user = $_SESSION['user_id'];
    $admin_user = 1; // Admin User ID
    
    echo "<p><strong>Current User ID:</strong> {$current_user}</p>";
    echo "<p><strong>Trying to load messages with Admin User (ID: 1)</strong></p>";
    
    // Test the API call manually
    require_once '../backend/config/db_connect.php';
    require_once '../backend/lib/helpers.php';
    
    echo "<h4>Direct Database Query Test:</h4>";
    
    try {
        $user_id = $current_user;
        $other_user_id = 1; // Admin
        $course_id = null;
        
        echo "<p>Query parameters: user_id={$user_id}, other_user_id={$other_user_id}, course_id=NULL</p>";
        
        // This is the same query used in get_messages.php
        $params = [$user_id, $other_user_id, $user_id, $other_user_id];
        $course_where = "AND m.course_id IS NULL";
        
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
                u.role as sender_role
            FROM messages m
            JOIN users u ON m.sender_id = u.id
            WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
            AND m.is_deleted_by_sender = 0 AND m.is_deleted_by_receiver = 0
            $course_where
            ORDER BY m.created_at ASC
        ";
        
        echo "<h4>SQL Query:</h4>";
        echo "<pre>$sql</pre>";
        echo "<h4>Parameters:</h4>";
        echo "<pre>";
        print_r($params);
        echo "</pre>";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();
        
        echo "<h4>Query Results:</h4>";
        echo "<p><strong>Messages found:</strong> " . count($messages) . "</p>";
        
        if (count($messages) > 0) {
            foreach ($messages as $msg) {
                echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
                echo "<strong>Message ID:</strong> {$msg['id']}<br>";
                echo "<strong>From:</strong> {$msg['sender_first_name']} (ID: {$msg['sender_id']})<br>";
                echo "<strong>To:</strong> {$msg['receiver_id']}<br>";
                echo "<strong>Message:</strong> {$msg['message']}<br>";
                echo "<strong>Course:</strong> " . ($msg['course_id'] ?: 'NULL') . "<br>";
                echo "<strong>Created:</strong> {$msg['created_at']}<br>";
                echo "</div>";
            }
        } else {
            echo "<p style='color: red;'>❌ No messages found with current query parameters</p>";
            
            // Let's check what messages actually exist
            echo "<h4>All Messages in Database:</h4>";
            $all_stmt = $pdo->prepare("
                SELECT m.id, m.sender_id, m.receiver_id, m.course_id, m.message, 
                       u1.first_name as sender_name, u2.first_name as receiver_name
                FROM messages m 
                JOIN users u1 ON m.sender_id = u1.id 
                JOIN users u2 ON m.receiver_id = u2.id 
                ORDER BY m.id
            ");
            $all_stmt->execute();
            $all_messages = $all_stmt->fetchAll();
            
            foreach ($all_messages as $msg) {
                echo "<div style='border: 1px solid #ddd; margin: 5px; padding: 10px;'>";
                echo "<strong>ID {$msg['id']}:</strong> {$msg['sender_name']} ({$msg['sender_id']}) → {$msg['receiver_name']} ({$msg['receiver_id']}) | Course: " . ($msg['course_id'] ?: 'NULL') . "<br>";
                echo "<strong>Message:</strong> {$msg['message']}<br>";
                echo "</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ No user logged in</p>";
}
?>