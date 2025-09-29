<?php
// backend/admin/courses.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Require admin authentication
requireAdmin();

// Handle course actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            case 'add_course':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $instructor_id = $_POST['instructor_id'] ?? null;
                $price = $_POST['price'] ?? 0;
                $level = $_POST['level'] ?? 'beginner';
                $category = $_POST['category'] ?? 'general';

                if (empty($title)) {
                    throw new Exception('Course title is required');
                }

                $stmt = $pdo->prepare("
                    INSERT INTO courses (title, description, instructor_id, price, level, category, is_active, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, 1, NOW())
                ");
                $stmt->execute([$title, $description, $instructor_id, $price, $level, $category]);
                $message = 'Course added successfully!';
                break;

            case 'toggle_status':
                $course_id = $_POST['course_id'] ?? 0;
                $stmt = $pdo->prepare("UPDATE courses SET is_active = NOT is_active WHERE id = ?");
                $stmt->execute([$course_id]);
                $message = 'Course status updated successfully!';
                break;

            case 'delete_course':
                $course_id = $_POST['course_id'] ?? 0;
                $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
                $stmt->execute([$course_id]);
                $message = 'Course deleted successfully!';
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get courses with instructor info
$stmt = $pdo->query("
    SELECT 
        c.*,
        CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
        u.email as instructor_email,
        COUNT(e.id) as enrollment_count
    FROM courses c
    LEFT JOIN users u ON c.instructor_id = u.id
    LEFT JOIN enrollments e ON c.id = e.course_id
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$courses = $stmt->fetchAll();

// Get instructors for dropdown
$stmt = $pdo->query("
    SELECT id, CONCAT(first_name, ' ', last_name) as name, email
    FROM users 
    WHERE role IN ('instructor', 'admin')
    ORDER BY first_name
");
$instructors = $stmt->fetchAll();

// Get course statistics
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_courses,
        COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_courses,
        SUM(CASE WHEN price > 0 THEN 1 ELSE 0 END) as paid_courses,
        AVG(price) as avg_price
    FROM courses
");
$stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
        }

        .section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a6fd8;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #545b62;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background: #f8f9fa;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.25);
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #212529;
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
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Admin'); ?>!</span>
                <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </header>

    <nav class="nav">
        <div class="nav-content">
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php">Users</a>
                <a href="courses.php" class="active">Courses</a>
                <a href="course-requests.php">Course Requests</a>
                <a href="enrollments.php">Enrollments</a>
                <a href="student-reports.php">Student Reports</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <h1 style="margin-bottom: 2rem;">Course Management</h1>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['total_courses']); ?></div>
                <div class="stat-label">Total Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['active_courses']); ?></div>
                <div class="stat-label">Active Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['paid_courses']); ?></div>
                <div class="stat-label">Paid Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($stats['avg_price'], 2); ?></div>
                <div class="stat-label">Average Price</div>
            </div>
        </div>

        <!-- Add Course Form -->
        <div class="section">
            <h2 style="margin-bottom: 1.5rem;">Add New Course</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add_course">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="title">Course Title *</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="instructor_id">Instructor</label>
                        <select id="instructor_id" name="instructor_id" class="form-control">
                            <option value="">Select Instructor</option>
                            <?php foreach ($instructors as $instructor): ?>
                                <option value="<?php echo $instructor['id']; ?>">
                                    <?php echo htmlspecialchars($instructor['name'] . ' (' . $instructor['email'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" class="form-control" min="0" step="0.01" value="0">
                    </div>
                    <div class="form-group">
                        <label for="level">Level</label>
                        <select id="level" name="level" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" class="form-control" placeholder="e.g., Web Development">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Course
                </button>
            </form>
        </div>

        <!-- Courses List -->
        <div class="section">
            <h2 style="margin-bottom: 1.5rem;">All Courses</h2>
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Price</th>
                            <th>Level</th>
                            <th>Category</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                                    <?php if ($course['description']): ?>
                                        <br><small style="color: #666;">
                                            <?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?>
                                            <?php echo strlen($course['description']) > 100 ? '...' : ''; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($course['instructor_name']): ?>
                                        <?php echo htmlspecialchars($course['instructor_name']); ?>
                                        <br><small style="color: #666;"><?php echo htmlspecialchars($course['instructor_email']); ?></small>
                                    <?php else: ?>
                                        <span style="color: #999;">No instructor assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($course['price'] > 0): ?>
                                        <span class="badge badge-warning">$<?php echo number_format($course['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Free</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars(ucfirst($course['level'])); ?></td>
                                <td><?php echo htmlspecialchars($course['category']); ?></td>
                                <td>
                                    <strong><?php echo number_format($course['enrollment_count']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($course['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" class="btn <?php echo $course['is_active'] ? 'btn-secondary' : 'btn-success'; ?>" style="margin-right: 5px;">
                                            <i class="fas <?php echo $course['is_active'] ? 'fa-pause' : 'fa-play'; ?>"></i>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                        <input type="hidden" name="action" value="delete_course">
                                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>