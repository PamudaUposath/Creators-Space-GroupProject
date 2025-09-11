USE creators_space;

-- Seed default admin user
-- Default password: AdminPass123! 
-- Please change this password after first login for security
-- To generate a new hash, use: php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"

INSERT INTO users (first_name, last_name, email, username, password_hash, role)
VALUES ('Admin', 'User', 'admin@creatorsspace.local', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Default instructor for demo purposes
INSERT INTO users (first_name, last_name, email, username, password_hash, role)
VALUES ('John', 'Instructor', 'instructor@creatorsspace.local', 'instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor');

-- Sample courses
INSERT INTO courses (title, slug, description, instructor_id, image_url, price, duration, level) VALUES
('Full Stack Web Development', 'full-stack-web-development', 'Learn complete web development from frontend to backend', 2, '/assets/images/full-stack-web-developer.png', 99.99, '12 weeks', 'intermediate'),
('UI/UX Design Fundamentals', 'ui-ux-design-fundamentals', 'Master the fundamentals of user interface and user experience design', 2, '/assets/images/uiux.jpeg', 79.99, '8 weeks', 'beginner'),
('JavaScript in 30 Days', 'javascript-30-days', 'Master JavaScript programming in 30 days with practical projects', 2, '/assets/images/jsin30days.png', 49.99, '4 weeks', 'beginner');

-- Sample services
INSERT INTO services (title, description, icon) VALUES
('Career Guidance', 'Get expert advice on your career path and professional development', 'career-guidance.svg'),
('Mock Interviews', 'Practice interviews with industry professionals', 'mock-interviews.svg'),
('Resume Tips', 'Professional resume writing and optimization tips', 'resume-tips.svg'),
('Project Learning', 'Hands-on learning through real-world projects', 'project-learning.svg'),
('Open Source Contribution', 'Learn to contribute to open source projects', 'opensource.svg');

-- Sample internships
INSERT INTO internships (title, company, description, requirements, duration, location, is_remote, application_deadline) VALUES
('Frontend Developer Intern', 'TechCorp', 'Work on exciting frontend projects using React and modern web technologies', 'Knowledge of HTML, CSS, JavaScript, React', '3 months', 'Remote', 1, '2025-12-31'),
('Backend Developer Intern', 'WebSolutions', 'Develop scalable backend systems using Node.js and databases', 'Knowledge of Node.js, databases, API development', '6 months', 'New York', 0, '2025-11-30'),
('UI/UX Design Intern', 'DesignStudio', 'Create beautiful and user-friendly interfaces for web and mobile apps', 'Portfolio showcasing UI/UX work, Figma knowledge', '4 months', 'Remote', 1, '2025-10-15');

-- Sample blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, author_id, featured_image, is_published, published_at) VALUES
('Getting Started with Tech Startups', 'getting-started-tech-startups', 'Learn the basics of starting a tech company...', 'A comprehensive guide to launching your tech startup', 2, '/assets/images/techstartup.jpeg', 1, NOW()),
('JavaScript in 30 Days Challenge', 'javascript-30-days-challenge', 'Take on the ultimate JavaScript learning challenge...', 'Master JavaScript programming with our 30-day challenge', 2, '/assets/images/jsin30days.png', 1, NOW()),
('UI/UX Design Best Practices', 'ui-ux-design-best-practices', 'Discover the essential principles of great design...', 'Learn the fundamental principles of effective UI/UX design', 2, '/assets/images/ui-ux.jpeg', 1, NOW());
