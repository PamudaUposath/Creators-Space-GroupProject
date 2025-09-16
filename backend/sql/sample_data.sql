-- Sample Data for Creators-Space Platform
-- This file contains comprehensive sample data for testing and development
-- Run this after setting up the database schema

USE creators_space;

-- Clear existing data (optional - remove if you want to preserve existing data)
-- DELETE FROM certificates;
-- DELETE FROM enrollments;
-- DELETE FROM bookmarks;
-- DELETE FROM lessons;
-- DELETE FROM courses;
-- DELETE FROM internship_applications;
-- DELETE FROM internships;
-- DELETE FROM campus_ambassador_applications;
-- DELETE FROM newsletter_subscriptions;
-- DELETE FROM blog_posts;
-- DELETE FROM services;
-- DELETE FROM users WHERE role != 'admin';

-- Sample Users (Students and Instructors)
INSERT INTO users (first_name, last_name, email, username, password_hash, role, skills, created_at) VALUES
-- Default Admin (password: AdminPass123!)
('Admin', 'User', 'admin@creatorsspace.local', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administration, Management', DATE_SUB(NOW(), INTERVAL 90 DAY)),

-- Instructors (password: instructor123)
('Dr. Sarah', 'Johnson', 'sarah.johnson@creatorsspace.local', 'sarah.instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Full Stack Development, JavaScript, React, Node.js', DATE_SUB(NOW(), INTERVAL 60 DAY)),
('Michael', 'Chen', 'michael.chen@creatorsspace.local', 'michael.instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'UI/UX Design, Figma, Adobe Creative Suite', DATE_SUB(NOW(), INTERVAL 55 DAY)),
('Dr. Elena', 'Rodriguez', 'elena.rodriguez@creatorsspace.local', 'elena.instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Data Science, Python, Machine Learning, AI', DATE_SUB(NOW(), INTERVAL 50 DAY)),
('James', 'Wilson', 'james.wilson@creatorsspace.local', 'james.instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Mobile Development, Flutter, React Native', DATE_SUB(NOW(), INTERVAL 45 DAY)),
('Priya', 'Patel', 'priya.patel@creatorsspace.local', 'priya.instructor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'DevOps, Cloud Computing, AWS, Docker', DATE_SUB(NOW(), INTERVAL 40 DAY)),

-- Sample Students (password: student123)
('Alice', 'Anderson', 'alice.anderson@student.local', 'alice.student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'HTML, CSS, Basic JavaScript', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Bob', 'Thompson', 'bob.thompson@student.local', 'bob.student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Python, Basic Programming', DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Carol', 'Davis', 'carol.davis@student.local', 'carol.student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Graphic Design, Photoshop', DATE_SUB(NOW(), INTERVAL 20 DAY)),
('David', 'Martinez', 'david.martinez@student.local', 'david.student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Java, Object-Oriented Programming', DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Emma', 'White', 'emma.white@student.local', 'emma.student', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Digital Marketing, Content Creation', DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Comprehensive Course Catalog with Proper Image Paths
INSERT INTO courses (title, slug, description, instructor_id, image_url, price, duration, level, created_at) VALUES
-- Web Development Courses
('Full Stack Web Development', 'full-stack-web-development', 'Master both frontend and backend development with modern technologies. Learn HTML, CSS, JavaScript, React, Node.js, and database management.', 2, 'frontend/assets/images/full-stack-web-developer.png', 299.99, '12 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 30 DAY)),

('JavaScript in 30 Days', 'javascript-30-days', 'Complete JavaScript mastery course with daily challenges and real-world projects. From basics to advanced concepts.', 2, 'frontend/assets/images/blogpage/jsin30days.png', 89.99, '4 weeks', 'beginner', DATE_SUB(NOW(), INTERVAL 25 DAY)),

('React.js Masterclass', 'react-masterclass', 'Advanced React development including hooks, context, Redux, and modern patterns. Build production-ready applications.', 2, 'frontend/assets/images/webdev.png', 199.99, '8 weeks', 'advanced', DATE_SUB(NOW(), INTERVAL 20 DAY)),

('Node.js Backend Development', 'nodejs-backend', 'Complete backend development with Node.js, Express, MongoDB, and RESTful APIs. Authentication and deployment included.', 2, 'frontend/assets/images/webdev.png', 179.99, '10 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 18 DAY)),

-- Design Courses  
('UI/UX Design Fundamentals', 'ui-ux-design-fundamentals', 'Learn user interface and user experience design principles. Master Figma, prototyping, and design thinking methodology.', 3, 'frontend/assets/images/blogpage/uiux.jpeg', 149.99, '6 weeks', 'beginner', DATE_SUB(NOW(), INTERVAL 15 DAY)),

('Advanced UI/UX Design', 'advanced-ui-ux-design', 'Advanced design concepts including design systems, accessibility, and user research. Portfolio development included.', 3, 'frontend/assets/images/blogpage/uiux.jpeg', 249.99, '10 weeks', 'advanced', DATE_SUB(NOW(), INTERVAL 12 DAY)),

-- Data Science & AI Courses
('Data Science with Python', 'data-science-python', 'Comprehensive data science course covering pandas, numpy, matplotlib, and machine learning with scikit-learn.', 4, 'frontend/assets/images/blogpage/datascience.jpg', 279.99, '14 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 10 DAY)),

('Machine Learning Fundamentals', 'machine-learning-fundamentals', 'Introduction to machine learning algorithms, supervised and unsupervised learning, and model evaluation.', 4, 'frontend/assets/images/blogpage/ai-ml.jpeg', 229.99, '12 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 8 DAY)),

('Artificial Intelligence Essentials', 'ai-essentials', 'Explore AI concepts, neural networks, deep learning basics, and practical applications in modern technology.', 4, 'frontend/assets/images/blogpage/ai-ml.jpeg', 199.99, '10 weeks', 'beginner', DATE_SUB(NOW(), INTERVAL 7 DAY)),

-- Mobile Development
('Flutter Mobile Development', 'flutter-mobile-development', 'Build cross-platform mobile apps with Flutter and Dart. iOS and Android deployment covered.', 5, 'frontend/assets/images/blogpage/flutter.png', 249.99, '12 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 6 DAY)),

('React Native Development', 'react-native-development', 'Create native mobile applications using React Native. Navigation, state management, and app store deployment.', 5, 'frontend/assets/images/webdev.png', 229.99, '10 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- DevOps & Cloud
('DevOps Engineering Bootcamp', 'devops-bootcamp', 'Complete DevOps pipeline setup with Docker, Kubernetes, CI/CD, and cloud deployment strategies.', 6, 'frontend/assets/images/blogpage/devops.png', 349.99, '16 weeks', 'advanced', DATE_SUB(NOW(), INTERVAL 4 DAY)),

('AWS Cloud Computing', 'aws-cloud-computing', 'Master Amazon Web Services with hands-on projects. EC2, S3, Lambda, and serverless architecture.', 6, 'frontend/assets/images/blogpage/cloud.jpg', 199.99, '8 weeks', 'intermediate', DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Programming Languages
('Python for Beginners', 'python-beginners', 'Start your programming journey with Python. Syntax, data structures, OOP, and practical applications.', 4, 'frontend/assets/images/blogpage/python.png', 129.99, '8 weeks', 'beginner', DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Sample Lessons for the first course (Full Stack Web Development)
INSERT INTO lessons (course_id, title, content, video_url, position, duration, is_free) VALUES
(1, 'Introduction to Web Development', 'Overview of modern web development technologies and career paths.', 'https://example.com/intro-video', 1, '45 minutes', 1),
(1, 'HTML5 Fundamentals', 'Learn semantic HTML5 elements and document structure.', 'https://example.com/html-video', 2, '1 hour 30 minutes', 1),
(1, 'CSS3 Styling and Layout', 'Master CSS Grid, Flexbox, and modern styling techniques.', 'https://example.com/css-video', 3, '2 hours', 0),
(1, 'JavaScript ES6+ Features', 'Modern JavaScript syntax, arrow functions, async/await, and modules.', 'https://example.com/js-video', 4, '2 hours 15 minutes', 0),
(1, 'React.js Introduction', 'Component-based development with React hooks and state management.', 'https://example.com/react-video', 5, '2 hours 30 minutes', 0);

-- Sample Enrollments
INSERT INTO enrollments (user_id, course_id, enrolled_at, progress) VALUES
(7, 1, DATE_SUB(NOW(), INTERVAL 20 DAY), 75.50),
(7, 5, DATE_SUB(NOW(), INTERVAL 15 DAY), 45.25),
(8, 2, DATE_SUB(NOW(), INTERVAL 18 DAY), 90.00),
(8, 14, DATE_SUB(NOW(), INTERVAL 10 DAY), 30.75),
(9, 5, DATE_SUB(NOW(), INTERVAL 12 DAY), 65.00),
(9, 6, DATE_SUB(NOW(), INTERVAL 8 DAY), 20.50),
(10, 7, DATE_SUB(NOW(), INTERVAL 14 DAY), 55.25),
(11, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), 15.00);

-- Sample Bookmarks
INSERT INTO bookmarks (user_id, course_id, created_at) VALUES
(7, 3, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(7, 12, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(8, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(9, 7, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(10, 5, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(11, 2, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Sample Services
INSERT INTO services (title, description, icon, created_at) VALUES
('Career Guidance', 'Get personalized career advice from industry experts. One-on-one sessions to help you plan your tech career path.', 'career.svg', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('Mock Interviews', 'Practice technical and behavioral interviews with experienced professionals. Get detailed feedback and improvement tips.', 'interview.svg', DATE_SUB(NOW(), INTERVAL 25 DAY)),
('Resume Review', 'Professional resume writing and optimization services. Make your resume stand out to recruiters and hiring managers.', 'resume.svg', DATE_SUB(NOW(), INTERVAL 20 DAY)),
('Project Mentorship', 'Work on real-world projects with guidance from industry mentors. Build a strong portfolio that showcases your skills.', 'project.svg', DATE_SUB(NOW(), INTERVAL 15 DAY)),
('Code Review', 'Get your code reviewed by senior developers. Learn best practices and improve your coding skills.', 'code.svg', DATE_SUB(NOW(), INTERVAL 10 DAY)),
('Technical Writing', 'Learn to write technical documentation, blog posts, and tutorials. Improve your communication skills.', 'writing.svg', DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Sample Internships
INSERT INTO internships (title, company, description, requirements, duration, location, is_remote, application_deadline, created_at) VALUES
('Frontend Developer Intern', 'TechCorp Inc.', 'Work with our frontend team to build responsive web applications using React.js and modern CSS frameworks. You will collaborate with designers and backend developers to create seamless user experiences.', 'Proficiency in HTML, CSS, JavaScript, React.js. Portfolio demonstrating frontend projects. Understanding of responsive design principles.', '3 months', 'San Francisco, CA', 1, '2025-06-30', DATE_SUB(NOW(), INTERVAL 20 DAY)),

('Backend Developer Intern', 'WebSolutions Ltd.', 'Join our backend development team to build scalable APIs and microservices. Work with Node.js, Python, and cloud technologies to support millions of users.', 'Knowledge of Node.js or Python, database design, RESTful APIs. Understanding of cloud platforms (AWS/Azure). Problem-solving skills.', '6 months', 'New York, NY', 0, '2025-07-15', DATE_SUB(NOW(), INTERVAL 18 DAY)),

('UI/UX Design Intern', 'DesignHub Studios', 'Create intuitive and beautiful user interfaces for web and mobile applications. Work closely with product teams to conduct user research and create design systems.', 'Portfolio showcasing UI/UX projects, proficiency in Figma/Sketch, understanding of design principles, user research experience preferred.', '4 months', 'Remote', 1, '2025-08-01', DATE_SUB(NOW(), INTERVAL 15 DAY)),

('Data Science Intern', 'Analytics Pro', 'Analyze large datasets to extract meaningful insights for business decisions. Build machine learning models and create data visualizations for stakeholders.', 'Python programming, pandas/numpy, basic machine learning knowledge, SQL skills, statistics background preferred.', '5 months', 'Austin, TX', 1, '2025-09-30', DATE_SUB(NOW(), INTERVAL 12 DAY)),

('DevOps Engineer Intern', 'CloudTech Solutions', 'Learn modern DevOps practices including CI/CD, containerization, and cloud infrastructure management. Work with Docker, Kubernetes, and AWS.', 'Basic understanding of Linux, scripting languages, version control (Git), cloud platforms knowledge helpful.', '4 months', 'Seattle, WA', 0, '2025-08-15', DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Sample Blog Posts  
INSERT INTO blog_posts (title, slug, content, excerpt, author_id, featured_image, is_published, published_at, created_at) VALUES
('Getting Started with Tech Startups', 'getting-started-tech-startups', 'Starting a tech startup can be an exciting but challenging journey. In this comprehensive guide, we will explore the essential steps to launch your own technology company, from ideation to funding and scaling. Learn about market validation, building an MVP, finding co-founders, and navigating the startup ecosystem.', 'A comprehensive guide for aspiring entrepreneurs looking to launch their first tech startup.', 2, 'frontend/assets/images/blogpage/techstartup.jpeg', 1, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY)),

('JavaScript in 30 Days: Complete Learning Path', 'javascript-30-days-learning-path', 'Master JavaScript programming with our structured 30-day learning challenge. This article outlines daily goals, practical exercises, and projects that will take you from beginner to confident JavaScript developer. Includes ES6+ features, DOM manipulation, async programming, and modern frameworks introduction.', 'Transform your JavaScript skills with our intensive 30-day learning program.', 2, 'frontend/assets/images/blogpage/jsin30days.png', 1, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY)),

('UI/UX Design Trends for 2025', 'ui-ux-design-trends-2025', 'Explore the latest design trends shaping the digital landscape in 2025. From micro-interactions and dark mode optimization to AI-assisted design tools and accessibility-first approaches. Learn how to implement these trends in your projects while maintaining usability and brand consistency.', 'Discover the cutting-edge design trends that will define user experiences in 2025.', 3, 'frontend/assets/images/blogpage/uiux.jpeg', 1, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY)),

('Machine Learning for Beginners: A Practical Guide', 'machine-learning-beginners-guide', 'Demystify machine learning with this beginner-friendly guide. Learn about different types of ML algorithms, when to use them, and how to implement your first models using Python and scikit-learn. Includes practical examples and real-world applications.', 'Start your machine learning journey with practical examples and clear explanations.', 4, 'frontend/assets/images/blogpage/ai-ml.jpeg', 1, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));

-- Sample Newsletter Subscriptions
INSERT INTO newsletter_subscriptions (email, subscribed_at) VALUES
('newsletter1@example.com', DATE_SUB(NOW(), INTERVAL 45 DAY)),
('newsletter2@example.com', DATE_SUB(NOW(), INTERVAL 30 DAY)),
('newsletter3@example.com', DATE_SUB(NOW(), INTERVAL 15 DAY)),
('newsletter4@example.com', DATE_SUB(NOW(), INTERVAL 7 DAY)),
('newsletter5@example.com', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Sample Campus Ambassador Applications
INSERT INTO campus_ambassador_applications (user_id, college_name, year_of_study, motivation, status, applied_at) VALUES
(7, 'Stanford University', 'Junior', 'I am passionate about technology education and want to bring Creators-Space opportunities to my campus. I have leadership experience in tech clubs and can effectively promote your platform.', 'accepted', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(8, 'MIT', 'Senior', 'As a computer science major, I understand the value of practical learning. I would love to help fellow students discover the amazing courses on Creators-Space.', 'pending', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(9, 'UC Berkeley', 'Sophomore', 'I believe in democratizing tech education. Creators-Space aligns with my values and I want to help make quality education accessible to all students.', 'accepted', DATE_SUB(NOW(), INTERVAL 15 DAY));

-- Sample Certificates (for completed courses)
INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES
(8, 2, 'CERT-JS30-2024-001', DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 1, 'CERT-FSWD-2024-002', DATE_SUB(NOW(), INTERVAL 3 DAY));

COMMIT;

-- Display summary of inserted data
SELECT 'Sample data insertion completed!' as Status;
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'user') as Students,
    (SELECT COUNT(*) FROM users WHERE role = 'instructor') as Instructors,
    (SELECT COUNT(*) FROM courses) as Courses,
    (SELECT COUNT(*) FROM lessons) as Lessons,
    (SELECT COUNT(*) FROM enrollments) as Enrollments,
    (SELECT COUNT(*) FROM bookmarks) as Bookmarks,
    (SELECT COUNT(*) FROM internships) as Internships,
    (SELECT COUNT(*) FROM blog_posts) as BlogPosts,
    (SELECT COUNT(*) FROM services) as Services,
    (SELECT COUNT(*) FROM certificates) as Certificates;