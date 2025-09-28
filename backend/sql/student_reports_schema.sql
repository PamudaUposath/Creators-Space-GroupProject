-- Student Reports Table Schema
-- This table stores reports submitted by instructors about students

CREATE TABLE IF NOT EXISTS student_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    instructor_id INT NOT NULL,
    student_id INT NOT NULL,
    course_id INT NULL, -- Optional, if report is course-specific
    report_type ENUM('academic_concern', 'behavior_issue', 'attendance_problem', 'inappropriate_conduct', 'plagiarism', 'other') NOT NULL DEFAULT 'other',
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    status ENUM('pending', 'under_review', 'resolved', 'dismissed') NOT NULL DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL, -- Admin user who reviewed the report
    reviewed_at TIMESTAMP NULL,
    admin_notes TEXT NULL, -- Admin's notes/response
    resolution_action TEXT NULL, -- Action taken to resolve the issue
    
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_instructor_reports (instructor_id),
    INDEX idx_student_reports (student_id),
    INDEX idx_report_status (status),
    INDEX idx_report_date (submitted_at)
);

-- Sample data for testing
INSERT INTO student_reports (instructor_id, student_id, course_id, report_type, subject, description, severity, status) VALUES
(2, 3, 1, 'academic_concern', 'Consistent Low Performance', 'Student has been consistently underperforming despite multiple attempts to provide assistance. Shows lack of engagement with course materials.', 'medium', 'pending'),
(2, 4, 2, 'behavior_issue', 'Disruptive Behavior in Course Discussion', 'Student has been posting inappropriate comments in course discussions and showing disrespect towards other students.', 'high', 'pending');