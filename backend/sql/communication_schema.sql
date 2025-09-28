-- Communication System Database Schema
-- This adds messaging functionality between instructors and students

USE creators_space;

-- Messages table for direct communication
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    course_id INT DEFAULT NULL, -- Optional: link message to specific course
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    is_deleted_by_sender TINYINT(1) DEFAULT 0,
    is_deleted_by_receiver TINYINT(1) DEFAULT 0,
    reply_to_message_id INT DEFAULT NULL, -- For threading replies
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (reply_to_message_id) REFERENCES messages(id) ON DELETE SET NULL,
    
    INDEX idx_sender (sender_id),
    INDEX idx_receiver (receiver_id),
    INDEX idx_course (course_id),
    INDEX idx_created (created_at),
    INDEX idx_unread (receiver_id, is_read),
    INDEX idx_thread (reply_to_message_id)
);

-- Conversations table to group messages
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    participant_1_id INT NOT NULL,
    participant_2_id INT NOT NULL,
    course_id INT DEFAULT NULL,
    last_message_id INT DEFAULT NULL,
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (participant_1_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (participant_2_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (last_message_id) REFERENCES messages(id) ON DELETE SET NULL,
    
    UNIQUE KEY unique_conversation (participant_1_id, participant_2_id, course_id),
    INDEX idx_participants (participant_1_id, participant_2_id),
    INDEX idx_last_message (last_message_at)
);

-- Announcements table for instructor broadcast messages
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    course_id INT DEFAULT NULL, -- NULL means general announcement to all students
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    INDEX idx_instructor (instructor_id),
    INDEX idx_course (course_id),
    INDEX idx_created (created_at),
    INDEX idx_active (is_active)
);

-- Announcement reads table to track who has read announcements
CREATE TABLE announcement_reads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    announcement_id INT NOT NULL,
    user_id INT NOT NULL,
    read_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (announcement_id) REFERENCES announcements(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_read (announcement_id, user_id),
    INDEX idx_announcement (announcement_id),
    INDEX idx_user (user_id)
);

-- Notifications table for system notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('message', 'announcement', 'course_update', 'certificate', 'general') NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT DEFAULT NULL,
    related_id INT DEFAULT NULL, -- ID of related message, course, etc.
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_unread (user_id, is_read),
    INDEX idx_created (created_at)
);

-- Communication preferences table
CREATE TABLE communication_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    email_messages TINYINT(1) DEFAULT 1,
    email_announcements TINYINT(1) DEFAULT 1,
    email_course_updates TINYINT(1) DEFAULT 1,
    push_notifications TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_prefs (user_id)
);

-- Add sample communication preferences for existing users
INSERT IGNORE INTO communication_preferences (user_id)
SELECT id FROM users WHERE is_active = 1;