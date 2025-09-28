<?php
session_start();

// Set up session for testing (simulate Admin user)
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
$_SESSION['first_name'] = 'Admin';
$_SESSION['last_name'] = 'User';

echo "<h2>Backend API Test</h2>";
echo "<p>Testing as Admin User (ID: 1)</p>";

echo "<h3>1. Test get_conversations.php</h3>";
?>
<iframe src="/Creators-Space-GroupProject/backend/communication/get_conversations.php" width="100%" height="300" style="border: 1px solid #ccc;"></iframe>

<h3>2. Test get_messages.php (Admin with John Instructor - User ID 2)</h3>
<iframe src="/Creators-Space-GroupProject/backend/communication/get_messages.php?other_user_id=2" width="100%" height="300" style="border: 1px solid #ccc;"></iframe>

<h3>3. Current Session Info</h3>
<pre><?php print_r($_SESSION); ?></pre>

<h3>4. Database Check</h3>
<?php
require_once '../backend/config/db_connect.php';

echo "<h4>Messages between Admin (1) and John (2):</h4>";
$stmt = $pdo->prepare("
    SELECT m.*, u.first_name as sender_name 
    FROM messages m 
    JOIN users u ON m.sender_id = u.id
    WHERE (m.sender_id = 1 AND m.receiver_id = 2) OR (m.sender_id = 2 AND m.receiver_id = 1)
    ORDER BY m.created_at ASC
");
$stmt->execute();
$messages = $stmt->fetchAll();

if (empty($messages)) {
    echo "<p style='color: red;'>❌ No messages found between Admin (1) and John (2)</p>";
} else {
    echo "<p style='color: green;'>✅ Found " . count($messages) . " messages:</p>";
    foreach ($messages as $msg) {
        echo "<div style='border: 1px solid #ccc; margin: 5px; padding: 10px;'>";
        echo "<strong>From:</strong> " . $msg['sender_name'] . " (ID: " . $msg['sender_id'] . ")<br>";
        echo "<strong>To:</strong> " . $msg['receiver_id'] . "<br>";
        echo "<strong>Message:</strong> " . $msg['message'] . "<br>";
        echo "<strong>Created:</strong> " . $msg['created_at'] . "<br>";
        echo "</div>";
    }
}

echo "<h4>All Conversations:</h4>";
$stmt = $pdo->prepare("SELECT * FROM conversations ORDER BY id");
$stmt->execute();
$conversations = $stmt->fetchAll();

foreach ($conversations as $conv) {
    echo "<div style='border: 1px solid #ddd; margin: 5px; padding: 10px;'>";
    echo "<strong>Conv ID:</strong> " . $conv['id'] . "<br>";
    echo "<strong>Participants:</strong> " . $conv['participant_1_id'] . " ↔ " . $conv['participant_2_id'] . "<br>";
    echo "<strong>Course:</strong> " . ($conv['course_id'] ?: 'General') . "<br>";
    echo "<strong>Last Message ID:</strong> " . $conv['last_message_id'] . "<br>";
    echo "</div>";
}
?>