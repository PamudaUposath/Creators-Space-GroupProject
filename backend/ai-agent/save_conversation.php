<?php
/**
 * Save AI agent conversation
 */

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

header('Content-Type: application/json');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['messages'])) {
        throw new Exception('Messages are required');
    }
    
    $messages = $input['messages'];
    $userId = $_SESSION['user_id'] ?? $input['user_id'] ?? null;
    
    if (!$userId) {
        throw new Exception('User ID is required');
    }
    
    // Validate messages format
    if (!is_array($messages) || empty($messages)) {
        throw new Exception('Messages must be a non-empty array');
    }
    
    // Save each message pair (user + bot response)
    $conversationId = null;
    $saved = 0;
    
    for ($i = 0; $i < count($messages); $i += 2) {
        if (!isset($messages[$i]) || !isset($messages[$i + 1])) {
            continue; // Skip incomplete pairs
        }
        
        $userMessage = $messages[$i];
        $botMessage = $messages[$i + 1];
        
        // Validate message structure
        if (!isset($userMessage['text'], $userMessage['sender'], $botMessage['text'], $botMessage['sender']) ||
            $userMessage['sender'] !== 'user' || $botMessage['sender'] !== 'bot') {
            continue;
        }
        
        // Create new conversation session if needed
        if (!$conversationId) {
            $stmt = $pdo->prepare("
                INSERT INTO ai_conversation_sessions (user_id, started_at) 
                VALUES (?, NOW())
            ");
            $stmt->execute([$userId]);
            $conversationId = $pdo->lastInsertId();
        }
        
        // Save the conversation pair
        $stmt = $pdo->prepare("
            INSERT INTO ai_conversations (
                session_id, 
                user_id, 
                user_message, 
                bot_response, 
                message_type,
                context,
                created_at
            ) VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $context = json_encode([
            'timestamp' => $userMessage['timestamp'] ?? time(),
            'message_type' => $botMessage['type'] ?? 'normal'
        ]);
        
        $stmt->execute([
            $conversationId,
            $userId,
            $userMessage['text'],
            $botMessage['text'],
            $botMessage['type'] ?? 'normal',
            $context
        ]);
        
        $saved++;
    }
    
    // Update conversation session
    if ($conversationId) {
        $stmt = $pdo->prepare("
            UPDATE ai_conversation_sessions 
            SET message_count = ?, last_activity = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$saved, $conversationId]);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Conversation saved successfully',
        'conversation_id' => $conversationId,
        'messages_saved' => $saved
    ]);

} catch (Exception $e) {
    error_log("Error saving AI conversation: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Could not save conversation'
    ]);
}
?>