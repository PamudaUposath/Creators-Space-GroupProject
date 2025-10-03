-- Create lesson progress tracking table
CREATE TABLE IF NOT EXISTS lesson_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    course_id INT NOT NULL,
    last_watched_time DECIMAL(10,2) DEFAULT 0.00, -- Time in seconds where user stopped watching
    total_duration DECIMAL(10,2) DEFAULT 0.00,   -- Total video duration in seconds
    completion_percentage DECIMAL(5,2) DEFAULT 0.00, -- Percentage completed (0-100)
    is_completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_lesson (user_id, lesson_id)
);

-- Create indexes for better performance (if they don't exist)
CREATE INDEX IF NOT EXISTS idx_user_course_progress ON lesson_progress(user_id, course_id);
CREATE INDEX IF NOT EXISTS idx_lesson_progress ON lesson_progress(lesson_id);

-- Add some sample lessons first (if they don't exist)
INSERT IGNORE INTO lessons (id, course_id, title, content, video_url, position, duration, is_free, is_published) VALUES
(1, 1, 'Introduction to Web Development', 'Overview of modern web development technologies and career paths.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/intro_webdev.mp4', 1, '45 minutes', 1, 1),
(2, 1, 'HTML5 Fundamentals', 'Learn semantic HTML5 elements and document structure.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/html5_basics.mp4', 2, '1 hour 30 minutes', 1, 1),
(3, 1, 'CSS3 Styling and Layout', 'Master CSS Grid, Flexbox, and modern styling techniques.', 'https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/videos/css3_advanced.mp4', 3, '2 hours', 0, 1);

-- Add some sample progress data
INSERT INTO lesson_progress (user_id, lesson_id, course_id, last_watched_time, total_duration, completion_percentage, is_completed) VALUES
(2, 1, 1, 0, 2700, 0, 0),      -- First lesson, not started
(2, 2, 1, 3245, 5400, 60.09, 0), -- Second lesson, 60% watched
(2, 3, 1, 7200, 7200, 100, 1); -- Third lesson, completed

-- Update enrollments table to track overall course progress
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS overall_progress DECIMAL(5,2) DEFAULT 0.00;
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS last_accessed_lesson_id INT DEFAULT NULL;
ALTER TABLE enrollments ADD COLUMN IF NOT EXISTS last_watched_time DECIMAL(10,2) DEFAULT 0.00;