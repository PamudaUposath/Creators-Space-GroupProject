-- AI Agent Database Schema
-- Add these tables to support the AI learning assistant functionality

-- AI Conversation Sessions table
CREATE TABLE ai_conversation_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    message_count INT DEFAULT 0,
    session_duration_minutes INT DEFAULT 0,
    user_satisfaction TINYINT DEFAULT NULL, -- 1-5 rating
    ended_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_ai_sessions_user (user_id),
    INDEX idx_ai_sessions_date (started_at),
    INDEX idx_ai_sessions_activity (last_activity)
);

-- AI Conversations table
CREATE TABLE ai_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    user_id INT NOT NULL,
    user_message TEXT NOT NULL,
    bot_response TEXT NOT NULL,
    message_type ENUM('normal', 'suggestion', 'warning', 'success') DEFAULT 'normal',
    intent VARCHAR(100) DEFAULT NULL, -- detected user intent
    context JSON DEFAULT NULL, -- additional context data
    processing_time_ms INT DEFAULT NULL, -- response time
    user_feedback TINYINT DEFAULT NULL, -- thumbs up/down (1/0)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (session_id) REFERENCES ai_conversation_sessions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_ai_conversations_session (session_id),
    INDEX idx_ai_conversations_user (user_id),
    INDEX idx_ai_conversations_date (created_at),
    INDEX idx_ai_conversations_intent (intent),
    INDEX idx_ai_conversations_type (message_type)
);

-- AI User Preferences table
CREATE TABLE ai_user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    preferred_learning_style ENUM('visual', 'auditory', 'kinesthetic', 'reading') DEFAULT NULL,
    skill_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    interests JSON DEFAULT NULL, -- array of interest categories
    learning_goals JSON DEFAULT NULL, -- array of learning goals
    preferred_pace ENUM('slow', 'moderate', 'fast') DEFAULT 'moderate',
    notification_preferences JSON DEFAULT NULL,
    language_preference VARCHAR(10) DEFAULT 'en',
    timezone VARCHAR(50) DEFAULT 'UTC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_ai_prefs_user (user_id),
    INDEX idx_ai_prefs_level (skill_level),
    INDEX idx_ai_prefs_style (preferred_learning_style)
);

-- AI Knowledge Base table (for storing dynamic FAQ and responses)
CREATE TABLE ai_knowledge_base (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    keywords JSON DEFAULT NULL, -- for better matching
    usage_count INT DEFAULT 0,
    effectiveness_score DECIMAL(3,2) DEFAULT 0.00, -- based on user feedback
    is_active TINYINT(1) DEFAULT 1,
    created_by INT DEFAULT NULL, -- admin user who created it
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_ai_kb_category (category),
    INDEX idx_ai_kb_active (is_active),
    INDEX idx_ai_kb_usage (usage_count),
    INDEX idx_ai_kb_score (effectiveness_score),
    
    -- Full text search
    FULLTEXT INDEX idx_ai_kb_question (question),
    FULLTEXT INDEX idx_ai_kb_answer (answer)
);

-- AI Recommendations table (track what was recommended to whom)
CREATE TABLE ai_recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_id INT DEFAULT NULL,
    recommendation_type ENUM('course', 'learning_path', 'project', 'resource') NOT NULL,
    item_id INT DEFAULT NULL, -- course ID, project ID, etc.
    item_data JSON DEFAULT NULL, -- full recommendation data
    reason TEXT DEFAULT NULL, -- why this was recommended
    user_action ENUM('viewed', 'clicked', 'enrolled', 'ignored', 'dismissed') DEFAULT NULL,
    relevance_score DECIMAL(3,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    action_taken_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES ai_conversation_sessions(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_ai_recs_user (user_id),
    INDEX idx_ai_recs_type (recommendation_type),
    INDEX idx_ai_recs_date (created_at),
    INDEX idx_ai_recs_action (user_action),
    INDEX idx_ai_recs_score (relevance_score)
);

-- AI Analytics table (for tracking agent performance)
CREATE TABLE ai_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    metric_name VARCHAR(100) NOT NULL,
    metric_value DECIMAL(10,2) NOT NULL,
    metric_count INT DEFAULT 1,
    additional_data JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_daily_metric (date, metric_name),
    
    -- Indexes for performance
    INDEX idx_ai_analytics_date (date),
    INDEX idx_ai_analytics_metric (metric_name),
    INDEX idx_ai_analytics_value (metric_value)
);

-- Sample data for AI Knowledge Base
INSERT INTO ai_knowledge_base (category, question, answer, keywords) VALUES
('enrollment', 'How do I enroll in a course?', 'To enroll in a course: 1) Browse our course catalog 2) Click on the course you want 3) Click "Enroll Now" 4) Complete payment if required 5) Start learning immediately!', '["enroll", "register", "join", "course", "signup"]'),

('certificates', 'How do I get a certificate?', 'You automatically receive a digital certificate when you complete a course with at least 80% progress. Certificates include a unique verification code and can be shared on LinkedIn.', '["certificate", "completion", "credential", "diploma", "verification"]'),

('payment', 'What payment methods are accepted?', 'We accept all major credit cards (Visa, MasterCard, AmEx), PayPal, and bank transfers. Many courses are also available for free!', '["payment", "pay", "credit card", "paypal", "money", "cost", "price"]'),

('progress', 'How do I track my progress?', 'Visit "My Courses" in your dashboard to see: completion percentages, current lessons, time spent, and upcoming deadlines for all your enrolled courses.', '["progress", "track", "completion", "dashboard", "my courses"]'),

('mobile', 'Can I learn on my phone?', 'Yes! Our platform is fully mobile-responsive. You can access courses, watch videos, take notes, and track progress on any smartphone or tablet.', '["mobile", "phone", "tablet", "app", "device", "responsive"]'),

('support', 'How do I get help?', 'Get help through: 1) This AI chat assistant (available 24/7) 2) Email support 3) Community forums 4) Live chat during business hours 5) Video tutorials in our help section.', '["help", "support", "contact", "assistance", "problem", "issue"]'),

('refund', 'What is your refund policy?', 'We offer a 30-day money-back guarantee for all paid courses. If you\'re not satisfied, contact support for a full refund within 30 days of purchase.', '["refund", "money back", "guarantee", "return", "cancel", "unsatisfied"]'),

('prerequisites', 'Do courses have prerequisites?', 'Prerequisites vary by course and are clearly listed on each course page. Beginner courses typically have no prerequisites, while advanced courses may require specific knowledge or skills.', '["prerequisites", "requirements", "prior knowledge", "skills needed", "preparation"]');

-- Sample data for AI User Preferences (will be populated as users interact)
INSERT INTO ai_user_preferences (user_id, skill_level, interests, learning_goals) 
SELECT 
    id, 
    'beginner',
    '["web-development"]',
    '["Learn programming basics", "Build first website"]'
FROM users 
WHERE role = 'user' 
LIMIT 5;

-- Create indexes for better performance with AI queries
ALTER TABLE courses ADD FULLTEXT INDEX idx_courses_search (title, description);
ALTER TABLE users ADD INDEX idx_users_skills (skills);

-- Views for AI Analytics
CREATE VIEW ai_daily_stats AS
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_conversations,
    COUNT(DISTINCT user_id) as unique_users,
    AVG(TIMESTAMPDIFF(SECOND, created_at, 
        (SELECT MAX(created_at) FROM ai_conversations ac2 
         WHERE ac2.session_id = ac1.session_id)
    )) / 60 as avg_session_minutes
FROM ai_conversations ac1
GROUP BY DATE(created_at)
ORDER BY date DESC;

CREATE VIEW ai_popular_intents AS
SELECT 
    intent,
    COUNT(*) as frequency,
    AVG(CASE WHEN user_feedback = 1 THEN 100.0 ELSE 0.0 END) as satisfaction_rate
FROM ai_conversations 
WHERE intent IS NOT NULL
GROUP BY intent
ORDER BY frequency DESC;

CREATE VIEW ai_user_engagement AS
SELECT 
    u.id,
    u.first_name,
    u.email,
    COUNT(DISTINCT acs.id) as total_sessions,
    COUNT(ac.id) as total_messages,
    MAX(acs.last_activity) as last_chat,
    AVG(acs.message_count) as avg_messages_per_session
FROM users u
LEFT JOIN ai_conversation_sessions acs ON u.id = acs.user_id
LEFT JOIN ai_conversations ac ON acs.id = ac.session_id
WHERE u.role = 'user'
GROUP BY u.id, u.first_name, u.email
ORDER BY total_messages DESC;