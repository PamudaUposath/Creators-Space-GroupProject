-- Course Requests Schema
-- This table stores instructor requests for new courses that need admin approval

CREATE TABLE IF NOT EXISTS course_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    instructor_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) DEFAULT 0.00,
    duration VARCHAR(100),
    level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    category VARCHAR(100),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT NULL,
    course_id INT NULL, -- Links to the actual course if approved
    
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL
);

-- Add index for faster queries
CREATE INDEX idx_course_requests_status ON course_requests(status);
CREATE INDEX idx_course_requests_instructor ON course_requests(instructor_id);
CREATE INDEX idx_course_requests_requested_at ON course_requests(requested_at);