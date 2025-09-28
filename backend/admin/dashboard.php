<?php
// backend/admin/dashboard.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Require admin authentication
requireAdmin();

// Get dashboard statistics
try {
    // Get total users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $totalUsers = $stmt->fetch()['total'];
    
    // Get total courses
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses");
    $totalCourses = $stmt->fetch()['total'];
    
    // Get total enrollments
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    $totalEnrollments = $stmt->fetch()['total'];
    
    // Get total instructors
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'instructor'");
    $totalInstructors = $stmt->fetch()['total'];
    
    // Get total revenue
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(c.price), 0) as total_revenue
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id
    ");
    $totalRevenue = $stmt->fetch()['total_revenue'];
    
    // Get recent users (last 10)
    $stmt = $pdo->query("
        SELECT first_name, last_name, email, created_at, role 
        FROM users 
        WHERE role = 'user' 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $recentUsers = $stmt->fetchAll();
    
    // Get popular courses with more details
    $stmt = $pdo->query("
        SELECT 
            c.title, 
            c.price,
            c.level,
            COUNT(e.id) as enrollment_count,
            u.first_name as instructor_first_name,
            u.last_name as instructor_last_name
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        LEFT JOIN users u ON c.instructor_id = u.id
        GROUP BY c.id, c.title, c.price, c.level, u.first_name, u.last_name
        ORDER BY enrollment_count DESC
        LIMIT 5
    ");
    $popularCourses = $stmt->fetchAll();
    
    // Get monthly enrollment statistics
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(enrolled_at, '%Y-%m') as month,
            COUNT(*) as enrollments
        FROM enrollments 
        WHERE enrolled_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(enrolled_at, '%Y-%m') 
        ORDER BY month DESC 
        LIMIT 6
    ");
    $monthlyEnrollments = $stmt->fetchAll();
    
    // Get course request statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests
        FROM course_requests
    ");
    $courseRequestStats = $stmt->fetch();
    
    // Get recent pending course requests
    $stmt = $pdo->query("
        SELECT cr.*, u.first_name, u.last_name, u.email
        FROM course_requests cr
        JOIN users u ON cr.instructor_id = u.id
        WHERE cr.status = 'pending'
        ORDER BY cr.requested_at DESC
        LIMIT 5
    ");
    $pendingRequests = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $totalUsers = $totalCourses = $totalEnrollments = $totalInstructors = 0;
    $totalRevenue = 0;
    $recentUsers = $popularCourses = $monthlyEnrollments = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Creators-Space</title>
    <link rel="shortcut icon" href="/frontend/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #333;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .nav {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #555;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .nav-links a:hover,
        .nav-links a.active {
            background: #667eea;
            color: white;
        }
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 2rem;
        }
        .section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .section-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            font-weight: bold;
            color: #495057;
        }
        .section-content {
            padding: 1.5rem;
        }
        .user-list {
            list-style: none;
        }
        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .user-item:last-child {
            border-bottom: none;
        }
        .user-name {
            font-weight: 500;
        }
        .user-email {
            color: #666;
            font-size: 0.9rem;
        }
        .user-date {
            color: #999;
            font-size: 0.8rem;
        }
        .course-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .course-item:last-child {
            border-bottom: none;
        }
        .enrollment-count {
            background: #667eea;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        .course-title {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }
        .course-details {
            color: #666;
            font-size: 0.85rem;
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                flex-wrap: wrap;
                gap: 1rem;
            }
        }
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                Creators-Space Admin
            </div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </header>

    <nav class="nav">
        <div class="nav-content">
            <div class="nav-links">
                <a href="dashboard.php" class="active">Dashboard</a>
                <a href="users.php">Users</a>
                <a href="courses.php">Courses</a>
                <a href="course-requests.php">Course Requests</a>
                <a href="enrollments.php">Enrollments</a>
                <a href="student-reports.php">Student Reports</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <h1 style="margin-bottom: 2rem;">Dashboard Overview</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalUsers); ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalCourses); ?></div>
                <div class="stat-label">Total Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalEnrollments); ?></div>
                <div class="stat-label">Total Enrollments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($totalInstructors); ?></div>
                <div class="stat-label">Instructors</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($totalRevenue, 0); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <div class="content-grid">
            <div class="section">
                <div class="section-header">Recent Users</div>
                <div class="section-content">
                    <?php if (empty($recentUsers)): ?>
                        <p>No users found.</p>
                    <?php else: ?>
                        <ul class="user-list">
                            <?php foreach ($recentUsers as $user): ?>
                                <li class="user-item">
                                    <div>
                                        <div class="user-name">
                                            <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                        </div>
                                        <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                    <div class="user-date">
                                        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="/backend/admin/users.php" class="btn">View All Users</a>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-header">Popular Courses</div>
                <div class="section-content">
                    <?php if (empty($popularCourses)): ?>
                        <p>No courses found.</p>
                    <?php else: ?>
                        <ul class="user-list">
                            <?php foreach ($popularCourses as $course): ?>
                                <li class="course-item">
                                    <div>
                                        <div class="course-title"><?php echo htmlspecialchars($course['title']); ?></div>
                                        <div class="course-details">
                                            Level: <?php echo ucfirst($course['level']); ?> | 
                                            Price: $<?php echo number_format($course['price'], 2); ?> |
                                            Instructor: <?php echo htmlspecialchars($course['instructor_first_name'] . ' ' . $course['instructor_last_name']); ?>
                                        </div>
                                    </div>
                                    <div class="enrollment-count">
                                        <?php echo $course['enrollment_count']; ?> enrolled
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="/backend/admin/courses.php" class="btn">Manage Courses</a>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-header">Monthly Enrollments</div>
                <div class="section-content">
                    <?php if (empty($monthlyEnrollments)): ?>
                        <p>No enrollment data available.</p>
                    <?php else: ?>
                        <ul class="user-list">
                            <?php foreach ($monthlyEnrollments as $monthly): ?>
                                <li class="course-item">
                                    <div>
                                        <div class="course-title"><?php echo date('F Y', strtotime($monthly['month'] . '-01')); ?></div>
                                    </div>
                                    <div class="enrollment-count">
                                        <?php echo $monthly['enrollments']; ?> enrollments
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="/backend/admin/enrollments.php" class="btn">View All Enrollments</a>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-header">
                    Course Requests
                    <?php if ($courseRequestStats['pending_requests'] > 0): ?>
                        <span style="background: #f59e0b; color: white; padding: 0.2rem 0.5rem; border-radius: 10px; font-size: 0.8rem; margin-left: 0.5rem;">
                            <?php echo $courseRequestStats['pending_requests']; ?> pending
                        </span>
                    <?php endif; ?>
                </div>
                <div class="section-content">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                        <div style="text-align: center; padding: 0.5rem; background: #f3f4f6; border-radius: 6px;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #374151;"><?php echo $courseRequestStats['total_requests']; ?></div>
                            <div style="font-size: 0.8rem; color: #6b7280;">Total</div>
                        </div>
                        <div style="text-align: center; padding: 0.5rem; background: #fef3c7; border-radius: 6px;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #92400e;"><?php echo $courseRequestStats['pending_requests']; ?></div>
                            <div style="font-size: 0.8rem; color: #92400e;">Pending</div>
                        </div>
                        <div style="text-align: center; padding: 0.5rem; background: #d1fae5; border-radius: 6px;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #065f46;"><?php echo $courseRequestStats['approved_requests']; ?></div>
                            <div style="font-size: 0.8rem; color: #065f46;">Approved</div>
                        </div>
                        <div style="text-align: center; padding: 0.5rem; background: #fee2e2; border-radius: 6px;">
                            <div style="font-size: 1.2rem; font-weight: bold; color: #991b1b;"><?php echo $courseRequestStats['rejected_requests']; ?></div>
                            <div style="font-size: 0.8rem; color: #991b1b;">Rejected</div>
                        </div>
                    </div>

                    <?php if (empty($pendingRequests)): ?>
                        <p>No pending course requests.</p>
                    <?php else: ?>
                        <ul class="user-list">
                            <?php foreach ($pendingRequests as $request): ?>
                                <li class="user-item">
                                    <div>
                                        <div class="user-name"><?php echo htmlspecialchars($request['title']); ?></div>
                                        <div class="user-email">
                                            by <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                            | $<?php echo number_format($request['price'], 2); ?>
                                            | <?php echo $request['level']; ?>
                                        </div>
                                    </div>
                                    <div class="user-date">
                                        <?php echo date('M d', strtotime($request['requested_at'])); ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <div style="margin-top: 1rem; text-align: center;">
                        <a href="course-requests.php" class="btn">Manage Course Requests</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
