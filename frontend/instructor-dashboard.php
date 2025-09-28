<?php
// frontend/instructor-dashboard.php
session_start();

// Check if user is logged in as instructor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

// Get instructor data
require_once '../backend/config/db_connect.php';

try {
    $instructor_id = $_SESSION['user_id'];
    
    // Get instructor's courses
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COUNT(DISTINCT e.id) as enrolled_students,
               COUNT(DISTINCT cert.id) as certificates_issued,
               AVG(e.progress) as avg_progress
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        LEFT JOIN certificates cert ON c.id = cert.course_id
        WHERE c.instructor_id = ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$instructor_id]);
    $courses = $stmt->fetchAll();

    // Get total statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT c.id) as total_courses,
            COUNT(DISTINCT e.id) as total_students,
            COUNT(DISTINCT cert.id) as total_certificates,
            COALESCE(SUM(c.price * (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id)), 0) as total_revenue
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        LEFT JOIN certificates cert ON c.id = cert.course_id
        WHERE c.instructor_id = ?
    ");
    $stmt->execute([$instructor_id]);
    $stats = $stmt->fetch();

    // Get recent enrollments
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, u.email, c.title as course_title, e.enrolled_at, e.progress
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN courses c ON e.course_id = c.id
        WHERE c.instructor_id = ?
        ORDER BY e.enrolled_at DESC
        LIMIT 5
    ");
    $stmt->execute([$instructor_id]);
    $recent_enrollments = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $courses = [];
    $stats = ['total_courses' => 0, 'total_students' => 0, 'total_certificates' => 0, 'total_revenue' => 0];
    $recent_enrollments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Dashboard - Creators Space</title>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
        }

        /* Modern Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, rgba(102,126,234,0.95) 0%, rgba(118,75,162,0.95) 100%);
            backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255,255,255,0.2);
            padding: 1rem 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .navbar:hover::before {
            opacity: 1;
        }

        .navbar-container {
            max-width: 1400px !important;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 2rem !important;
            position: relative;
            z-index: 2;
            height: 100% !important;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-left: auto;
            justify-content: flex-end;
        }

        /* Logo Section */
        .navbar h1 {
            margin: 0 !important;
            position: relative;
            margin-right: auto;
            font-size: 24px !important;
            font-weight: bold !important;
            color: black !important;
        }

        .navbar h1 a {
            display: flex !important;
            align-items: center;
            gap: 0.8rem !important;
            text-decoration: none;
            color: #ffffff !important;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            transition: all 0.3s ease;
            text-shadow: 0 2px 10px rgba(0,0,0,0.5);
            width: auto;
        }

        .navbar h1 a:hover {
            color: #667eea !important;
            text-shadow: 0 0 20px rgba(102,126,234,0.8);
            transform: translateY(-1px);
        }

        #navbar-logo {
            width: 50px !important;
            height: 50px !important;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .navbar h1 a:hover #navbar-logo {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        /* Navigation Links */
        .navbar .nav-links {
            display: flex !important;
            align-items: center;
            gap: 2rem !important;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar .nav-links a {
            position: relative;
            color: #ffffff !important;
            text-decoration: none;
            padding: 0.5rem 0;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            margin: 10px 2px;
        }

        .navbar .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .navbar .nav-links a:hover {
            color: #ffffff !important;
            text-shadow: 0 0 8px rgba(255,255,255,0.6);
        }

        .navbar .nav-links a:hover::after {
            width: 100%;
        }

        /* User Section */
        #userSection {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 25px;
            padding: 0.4rem 0.8rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            max-width: fit-content;
        }

        #userSection span {
            color: #ffffff !important;
            font-weight: 500;
            font-size: 0.75rem;
            margin-right: 0.2rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            white-space: nowrap;
            max-width: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Button Styles */
        .navbar .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.6rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            border: 1px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            color: #ffffff !important;
            margin: 10px 2px;
        }

        .navbar .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .navbar .btn:hover::before {
            left: 100%;
        }

        .navbar .btn.profile-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important;
            color: #ffffff !important;
            border-color: rgba(255,255,255,0.2) !important;
            box-shadow: 0 8px 25px rgba(76,175,80,0.3);
            font-size: 0.9rem !important;
            padding: 0 !important;
            width: 35px !important;
            height: 35px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 35px !important;
            max-width: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            text-align: center !important;
            line-height: 1 !important;
        }

        .navbar .btn.logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            text-align: center;
            min-width: auto;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2);
        }

        .navbar .btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .navbar .btn.profile-btn:hover {
            box-shadow: 0 15px 35px rgba(76,175,80,0.4);
        }

        .navbar .btn.logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            background: linear-gradient(135deg, #ff5252 0%, #f44336 100%);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }

        .theme-btn {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #ffffff;
            padding: 0.6rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .theme-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .theme-btn:hover::before {
            left: 100%;
        }

        .theme-btn:hover {
            background: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.3);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255,255,255,0.1);
        }

        .theme-btn:active {
            transform: translateY(0) scale(0.95);
        }

        #theme-icon {
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .theme-btn:hover #theme-icon {
            transform: rotate(15deg);
        }

        /* Dark mode styles */
        body.dark-mode {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #ffffff;
        }

        body.dark-mode .navbar {
            background: linear-gradient(135deg, rgba(10,10,20,0.95) 0%, rgba(20,20,40,0.95) 100%) !important;
            border-bottom: 1px solid rgba(255,255,255,0.1) !important;
        }

        body.dark-mode .theme-btn {
            background: rgba(255,255,255,0.15);
            border-color: rgba(255,255,255,0.25);
        }

        body.dark-mode .theme-btn:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.35);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .welcome-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: white;
        }

        .stat-icon.courses { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.students { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-icon.certificates { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .stat-icon.revenue { background: linear-gradient(135deg, #43e97b, #38f9d7); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-weight: 500;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .courses-section, .recent-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .course-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }

        .course-card:hover {
            border-color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .course-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 0.5rem;
        }

        .course-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
        }

        .course-stats span {
            background: #f1f5f9;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            color: #475569;
            font-weight: 500;
        }

        .enrollment-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .enrollment-item:last-child {
            border-bottom: none;
        }

        .student-name {
            font-weight: 600;
            color: #2d3748;
        }

        .course-name {
            color: #667eea;
            font-size: 0.9rem;
        }

        .enrollment-date {
            color: #64748b;
            font-size: 0.85rem;
        }

        .progress-bar {
            background: #f1f5f9;
            height: 6px;
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .progress-fill {
            background: linear-gradient(135deg, #667eea, #764ba2);
            height: 100%;
            border-radius: 3px;
            transition: width 0.3s ease;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border: 1px solid #667eea;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .header-content {
                padding: 0 1rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-container">
            <h1>
                <a href="instructor-dashboard.php">
                    <img id="navbar-logo" width="80px" src="./assets/images/logo-nav-light.png" alt="logo Creators-Space">
                    Creators-Space
                </a>
            </h1>
            
            <div class="navbar-right">
                <div class="nav-links align-items-center">
                    <a href="instructor-dashboard.php">Dashboard</a>
                    <a href="instructor-courses.php">My Courses</a>
                    <a href="instructor-students.php">Students</a>
                    
                    <!-- Dark/Light Mode Toggle -->
                    <div class="theme-toggle">
                        <button id="theme-toggle-btn" class="theme-btn" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- User Section -->
                <div id="userSection">
                    <a href="#" class="btn profile-btn" title="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                    <a href="../backend/auth/logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="welcome-section">
            <h1 class="welcome-title">
                <i class="fas fa-chalkboard-teacher"></i> Instructor Dashboard
            </h1>
            <p class="welcome-subtitle">
                Manage your courses, track student progress, and grow your impact
            </p>
        </div>

        <!-- Statistics Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon courses">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['total_courses']); ?></div>
                <div class="stat-label">Total Courses</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon students">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['total_students']); ?></div>
                <div class="stat-label">Students Enrolled</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon certificates">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['total_certificates']); ?></div>
                <div class="stat-label">Certificates Issued</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <!-- Main Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Courses Section -->
            <div class="courses-section">
                <h2 class="section-title">
                    <i class="fas fa-book"></i> Your Courses
                </h2>

                <?php if (empty($courses)): ?>
                    <div class="empty-state">
                        <i class="fas fa-book-open"></i>
                        <h3>No courses yet</h3>
                        <p>Start creating your first course to share your expertise with students.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="course-card">
                            <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <div class="course-meta">
                                <span><i class="fas fa-layer-group"></i> <?php echo ucfirst($course['level']); ?></span>
                                <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['duration']); ?></span>
                                <span><i class="fas fa-dollar-sign"></i> $<?php echo number_format($course['price'], 2); ?></span>
                            </div>
                            <div class="course-stats">
                                <span><?php echo $course['enrolled_students']; ?> Students</span>
                                <span><?php echo $course['certificates_issued']; ?> Certificates</span>
                                <span><?php echo number_format($course['avg_progress'] ?? 0, 1); ?>% Avg Progress</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="action-buttons">
                    <a href="instructor-courses.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Course
                    </a>
                    <a href="instructor-courses.php" class="btn btn-secondary">
                        <i class="fas fa-cog"></i> Manage Courses
                    </a>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="recent-section">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i> Recent Enrollments
                </h2>

                <?php if (empty($recent_enrollments)): ?>
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <p>No recent enrollments</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_enrollments as $enrollment): ?>
                        <div class="enrollment-item">
                            <div class="student-name">
                                <?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?>
                            </div>
                            <div class="course-name">
                                <?php echo htmlspecialchars($enrollment['course_title']); ?>
                            </div>
                            <div class="enrollment-date">
                                <?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $enrollment['progress']; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Add some interactive functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Theme toggle functionality
            const themeToggleBtn = document.getElementById('theme-toggle-btn');
            const themeIcon = document.getElementById('theme-icon');
            
            // Load saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-mode');
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
            
            // Theme toggle functionality
            themeToggleBtn.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    themeIcon.className = 'fas fa-sun';
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.className = 'fas fa-moon';
                    localStorage.setItem('theme', 'light');
                }
                
                // Add a little animation to the button
                themeToggleBtn.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    themeToggleBtn.style.transform = '';
                }, 150);
            });

            // Course card hover effects
            const courseCards = document.querySelectorAll('.course-card');
            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Animate progress bars
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });

            console.log('Instructor dashboard loaded successfully');
        });
    </script>
</body>
</html>