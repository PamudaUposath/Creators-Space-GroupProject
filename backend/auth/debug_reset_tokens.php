<?php
// Debug script to check reset tokens
require_once __DIR__ . '/../config/db_connect.php';

// Get the token from URL if provided
$token = $_GET['token'] ?? '';

echo "<h2>Reset Token Debug Information</h2>";
echo "<p><strong>Token from URL:</strong> " . htmlspecialchars($token) . "</p>";
echo "<p><strong>Token length:</strong> " . strlen($token) . "</p>";

if (!empty($token)) {
    try {
        // Check if token exists in database
        $stmt = $pdo->prepare("SELECT id, email, reset_token, reset_expires, created_at FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<h3>Token Found in Database:</h3>";
            echo "<p><strong>User ID:</strong> " . $user['id'] . "</p>";
            echo "<p><strong>Email:</strong> " . $user['email'] . "</p>";
            echo "<p><strong>Reset Token:</strong> " . htmlspecialchars($user['reset_token']) . "</p>";
            echo "<p><strong>Reset Expires:</strong> " . $user['reset_expires'] . "</p>";
            echo "<p><strong>Current Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
            echo "<p><strong>Expires Timestamp:</strong> " . strtotime($user['reset_expires']) . "</p>";
            echo "<p><strong>Current Timestamp:</strong> " . time() . "</p>";
            echo "<p><strong>Time Difference (seconds):</strong> " . (strtotime($user['reset_expires']) - time()) . "</p>";
            
            if (strtotime($user['reset_expires']) > time()) {
                echo "<p style='color: green;'><strong>✓ Token is VALID</strong></p>";
            } else {
                echo "<p style='color: red;'><strong>✗ Token is EXPIRED</strong></p>";
            }
        } else {
            echo "<p style='color: red;'><strong>Token NOT found in database</strong></p>";
            
            // Check if there are any reset tokens at all
            $stmt = $pdo->prepare("SELECT id, email, reset_token, reset_expires FROM users WHERE reset_token IS NOT NULL AND reset_token != ''");
            $stmt->execute();
            $allTokens = $stmt->fetchAll();
            
            if ($allTokens) {
                echo "<h3>All Reset Tokens in Database:</h3>";
                foreach ($allTokens as $tokenData) {
                    echo "<p><strong>User ID:</strong> " . $tokenData['id'] . " | ";
                    echo "<strong>Email:</strong> " . $tokenData['email'] . " | ";
                    echo "<strong>Token:</strong> " . substr($tokenData['reset_token'], 0, 20) . "... | ";
                    echo "<strong>Expires:</strong> " . $tokenData['reset_expires'] . "</p>";
                }
            } else {
                echo "<p>No reset tokens found in database</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p>No token provided. Add ?token=YOUR_TOKEN to the URL</p>";
}

// Show recent reset token requests
try {
    $stmt = $pdo->prepare("SELECT id, email, reset_token, reset_expires FROM users WHERE reset_token IS NOT NULL ORDER BY reset_expires DESC LIMIT 5");
    $stmt->execute();
    $recentTokens = $stmt->fetchAll();
    
    if ($recentTokens) {
        echo "<h3>Recent Reset Token Requests:</h3>";
        foreach ($recentTokens as $tokenData) {
            echo "<p><strong>User:</strong> " . $tokenData['email'] . " | ";
            echo "<strong>Token:</strong> " . substr($tokenData['reset_token'], 0, 20) . "... | ";
            echo "<strong>Expires:</strong> " . $tokenData['reset_expires'];
            if (strtotime($tokenData['reset_expires']) > time()) {
                echo " <span style='color: green;'>(Valid)</span>";
            } else {
                echo " <span style='color: red;'>(Expired)</span>";
            }
            echo "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>Error fetching recent tokens: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>