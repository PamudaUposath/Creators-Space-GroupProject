@echo off
echo Setting up AI Agent Database Tables...

REM Connect to MySQL and create the tables
C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_conversation_sessions (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, message_count INT DEFAULT 0, session_duration_minutes INT DEFAULT 0, user_satisfaction TINYINT DEFAULT NULL, ended_at TIMESTAMP NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_ai_sessions_user (user_id), INDEX idx_ai_sessions_date (started_at), INDEX idx_ai_sessions_activity (last_activity));"

echo ✓ Created ai_conversation_sessions table

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_conversations (id INT AUTO_INCREMENT PRIMARY KEY, session_id INT NOT NULL, user_id INT NOT NULL, user_message TEXT NOT NULL, bot_response TEXT NOT NULL, message_type ENUM('normal', 'suggestion', 'warning', 'success') DEFAULT 'normal', intent VARCHAR(100) DEFAULT NULL, context JSON DEFAULT NULL, processing_time_ms INT DEFAULT NULL, user_feedback TINYINT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (session_id) REFERENCES ai_conversation_sessions(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_ai_conversations_session (session_id), INDEX idx_ai_conversations_user (user_id), INDEX idx_ai_conversations_date (created_at), INDEX idx_ai_conversations_intent (intent), INDEX idx_ai_conversations_type (message_type));"

echo ✓ Created ai_conversations table

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_user_preferences (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL UNIQUE, preferred_learning_style ENUM('visual', 'auditory', 'kinesthetic', 'reading') DEFAULT NULL, skill_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner', interests JSON DEFAULT NULL, learning_goals JSON DEFAULT NULL, preferred_pace ENUM('slow', 'moderate', 'fast') DEFAULT 'moderate', notification_preferences JSON DEFAULT NULL, language_preference VARCHAR(10) DEFAULT 'en', timezone VARCHAR(50) DEFAULT 'UTC', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_ai_prefs_user (user_id), INDEX idx_ai_prefs_level (skill_level), INDEX idx_ai_prefs_style (preferred_learning_style));"

echo ✓ Created ai_user_preferences table

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_knowledge_base (id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(100) NOT NULL, question TEXT NOT NULL, answer TEXT NOT NULL, keywords JSON DEFAULT NULL, usage_count INT DEFAULT 0, effectiveness_score DECIMAL(3,2) DEFAULT 0.00, is_active TINYINT(1) DEFAULT 1, created_by INT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_ai_kb_category (category), INDEX idx_ai_kb_active (is_active), INDEX idx_ai_kb_usage (usage_count), INDEX idx_ai_kb_score (effectiveness_score));"

echo ✓ Created ai_knowledge_base table

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_recommendations (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, session_id INT DEFAULT NULL, recommendation_type ENUM('course', 'learning_path', 'project', 'resource') NOT NULL, item_id INT DEFAULT NULL, item_data JSON DEFAULT NULL, reason TEXT DEFAULT NULL, user_action ENUM('viewed', 'clicked', 'enrolled', 'ignored', 'dismissed') DEFAULT NULL, relevance_score DECIMAL(3,2) DEFAULT 0.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, action_taken_at TIMESTAMP NULL, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (session_id) REFERENCES ai_conversation_sessions(id) ON DELETE SET NULL, INDEX idx_ai_recs_user (user_id), INDEX idx_ai_recs_type (recommendation_type), INDEX idx_ai_recs_date (created_at), INDEX idx_ai_recs_action (user_action), INDEX idx_ai_recs_score (relevance_score));"

echo ✓ Created ai_recommendations table

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "CREATE TABLE IF NOT EXISTS ai_analytics (id INT AUTO_INCREMENT PRIMARY KEY, date DATE NOT NULL, metric_name VARCHAR(100) NOT NULL, metric_value DECIMAL(10,2) NOT NULL, metric_count INT DEFAULT 1, additional_data JSON DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY unique_daily_metric (date, metric_name), INDEX idx_ai_analytics_date (date), INDEX idx_ai_analytics_metric (metric_name), INDEX idx_ai_analytics_value (metric_value));"

echo ✓ Created ai_analytics table

REM Insert sample FAQ data
C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "INSERT IGNORE INTO ai_knowledge_base (category, question, answer) VALUES ('enrollment', 'How do I enroll in a course?', 'To enroll in a course: 1) Browse our course catalog 2) Click on the course you want 3) Click \"Enroll Now\" 4) Complete payment if required 5) Start learning immediately!');"

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "INSERT IGNORE INTO ai_knowledge_base (category, question, answer) VALUES ('certificates', 'How do I get a certificate?', 'You automatically receive a digital certificate when you complete a course with at least 80%% progress. Certificates include a unique verification code and can be shared on LinkedIn.');"

C:\xampp\mysql\bin\mysql.exe -u root -h 127.0.0.1 creators_space -e "INSERT IGNORE INTO ai_knowledge_base (category, question, answer) VALUES ('payment', 'What payment methods are accepted?', 'We accept all major credit cards (Visa, MasterCard, AmEx), PayPal, and bank transfers. Many courses are also available for free!');"

echo ✓ Inserted sample FAQ data

echo.
echo 🎉 AI Agent database setup complete!
echo The AI chatbot is now ready to use.
echo.
pause