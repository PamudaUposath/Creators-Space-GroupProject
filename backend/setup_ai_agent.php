<?php
/**
 * AI Agent Database Setup Script
 * Run this script to set up the AI agent database tables
 */

require_once __DIR__ . '/config/db_connect.php';

echo "Setting up AI Agent database tables...\n";

try {
    // Read and execute the AI agent schema
    $sqlFile = __DIR__ . '/sql/ai_agent_schema.sql';
    
    if (!file_exists($sqlFile)) {
        throw new Exception("AI agent schema file not found: $sqlFile");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^\s*--/', $stmt) && 
                   !preg_match('/^\s*\/\*/', $stmt);
        }
    );
    
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        if (trim($statement) === '') continue;
        
        try {
            $pdo->exec($statement);
            $executed++;
            
            // Show progress for major operations
            if (strpos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE (\w+)/', $statement, $matches);
                if ($matches) {
                    echo "✓ Created table: {$matches[1]}\n";
                }
            } elseif (strpos($statement, 'CREATE VIEW') !== false) {
                preg_match('/CREATE VIEW (\w+)/', $statement, $matches);
                if ($matches) {
                    echo "✓ Created view: {$matches[1]}\n";
                }
            } elseif (strpos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO (\w+)/', $statement, $matches);
                if ($matches) {
                    echo "✓ Inserted sample data into: {$matches[1]}\n";
                }
            }
            
        } catch (PDOException $e) {
            // Log error but continue with other statements
            $errors[] = "Statement failed: " . substr($statement, 0, 100) . "... Error: " . $e->getMessage();
            
            // If table already exists, that's okay
            if (strpos($e->getMessage(), 'already exists') !== false) {
                preg_match('/CREATE TABLE (\w+)/', $statement, $matches);
                if ($matches) {
                    echo "- Table {$matches[1]} already exists\n";
                }
                continue;
            }
        }
    }
    
    echo "\n=== AI Agent Setup Complete ===\n";
    echo "Executed: $executed statements\n";
    
    if (!empty($errors)) {
        echo "Errors encountered:\n";
        foreach ($errors as $error) {
            echo "- $error\n";
        }
    }
    
    // Verify tables were created
    echo "\nVerifying AI Agent tables...\n";
    $tables = ['ai_conversation_sessions', 'ai_conversations', 'ai_user_preferences', 'ai_knowledge_base', 'ai_recommendations', 'ai_analytics'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table LIMIT 1");
            echo "✓ Table '$table' is accessible\n";
        } catch (PDOException $e) {
            echo "✗ Table '$table' is not accessible: " . $e->getMessage() . "\n";
        }
    }
    
    // Test basic functionality
    echo "\nTesting AI Agent functionality...\n";
    
    // Test knowledge base
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ai_knowledge_base WHERE is_active = 1");
    $kbCount = $stmt->fetch()['count'];
    echo "✓ Knowledge base has $kbCount active entries\n";
    
    // Test user preferences
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ai_user_preferences");
    $prefCount = $stmt->fetch()['count'];
    echo "✓ User preferences: $prefCount entries\n";
    
    echo "\n🎉 AI Agent is ready to use!\n";
    echo "\nNext steps:\n";
    echo "1. The AI chat button will appear on all pages except login/signup\n";
    echo "2. Users can click the robot icon to start chatting\n";
    echo "3. The AI provides course recommendations, learning paths, and support\n";
    echo "4. All conversations are saved for analytics and improvements\n";
    
} catch (Exception $e) {
    echo "Setup failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>