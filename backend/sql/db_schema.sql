-- Create database
CREATE DATABASE IF NOT EXISTS creators_space DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE creators_space;

-- Users table (students, instructors, admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user','instructor','admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    profile_image VARCHAR(500) DEFAULT NULL,
    skills TEXT DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    date_of_birth DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_role (role),
    INDEX idx_users_active (is_active)
);

-- Courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    instructor_id INT,
    image_url VARCHAR(500),
    price DECIMAL(10,2) DEFAULT 0.00,
    duration VARCHAR(100),
    level ENUM('beginner','intermediate','advanced') DEFAULT 'beginner',
    category VARCHAR(100) DEFAULT 'general',
    prerequisites TEXT DEFAULT NULL,
    learning_objectives TEXT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    featured TINYINT(1) DEFAULT 0,
    total_lessons INT DEFAULT 0,
    total_duration_minutes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (instructor_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_courses_instructor (instructor_id),
    INDEX idx_courses_level (level),
    INDEX idx_courses_category (category),
    INDEX idx_courses_active (is_active),
    INDEX idx_courses_featured (featured),
    INDEX idx_courses_price (price)
);

-- Lessons table
CREATE TABLE lessons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255),
    content TEXT,
    video_url VARCHAR(500),
    attachments JSON DEFAULT NULL,
    position INT DEFAULT 0,
    duration VARCHAR(50),
    duration_minutes INT DEFAULT 0,
    is_free TINYINT(1) DEFAULT 0,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_lessons_course (course_id),
    INDEX idx_lessons_position (position),
    INDEX idx_lessons_free (is_free),
    INDEX idx_lessons_published (is_published)
);

-- Enrollments table
CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    last_accessed TIMESTAMP NULL,
    progress DECIMAL(5,2) DEFAULT 0.00,
    current_lesson_id INT DEFAULT NULL,
    status ENUM('active','completed','paused','cancelled') DEFAULT 'active',
    
    UNIQUE(user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (current_lesson_id) REFERENCES lessons(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_enrollments_user (user_id),
    INDEX idx_enrollments_course (course_id),
    INDEX idx_enrollments_status (status),
    INDEX idx_enrollments_progress (progress)
);

-- Certificates table
CREATE TABLE certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    certificate_code VARCHAR(100) UNIQUE,
    issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_certificates_user (user_id),
    INDEX idx_certificates_course (course_id),
    INDEX idx_certificates_code (certificate_code)
);

-- Bookmarks table (for bookmarked courses)
CREATE TABLE bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_bookmarks_user (user_id),
    INDEX idx_bookmarks_course (course_id)
);

-- Internships table
CREATE TABLE internships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    duration VARCHAR(100),
    location VARCHAR(255),
    is_remote TINYINT(1) DEFAULT 0,
    application_deadline DATE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_internships_company (company),
    INDEX idx_internships_location (location),
    INDEX idx_internships_remote (is_remote),
    INDEX idx_internships_active (is_active),
    INDEX idx_internships_deadline (application_deadline)
);

-- Internship applications table
CREATE TABLE internship_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    internship_id INT NOT NULL,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, internship_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (internship_id) REFERENCES internships(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_internship_apps_user (user_id),
    INDEX idx_internship_apps_internship (internship_id),
    INDEX idx_internship_apps_status (status)
);

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_services_active (is_active)
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content TEXT,
    excerpt TEXT,
    author_id INT,
    featured_image VARCHAR(500),
    is_published TINYINT(1) DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_blog_posts_author (author_id),
    INDEX idx_blog_posts_slug (slug),
    INDEX idx_blog_posts_published (is_published),
    INDEX idx_blog_posts_published_at (published_at)
);

-- Newsletter subscriptions table
CREATE TABLE newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_newsletter_email (email),
    INDEX idx_newsletter_active (is_active)
);

-- Campus ambassador applications table
CREATE TABLE campus_ambassador_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    college_name VARCHAR(255) NOT NULL,
    year_of_study VARCHAR(50),
    motivation TEXT,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_campus_apps_user (user_id),
    INDEX idx_campus_apps_status (status),
    INDEX idx_campus_apps_college (college_name)
);
