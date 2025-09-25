<?php
// backend/admin/enrollments.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Require admin authentication
requireAdmin();

// Handle enrollment actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'update_status':
                $enrollment_id = $_POST['enrollment_id'] ?? 0;
                $status = $_POST['status'] ?? 'active';
                
                $stmt = $pdo->prepare("UPDATE enrollments SET status = ? WHERE id = ?");
                $stmt->execute([$status, $enrollment_id]);
                $message = 'Enrollment status updated successfully!';
                break;
                
            case 'update_progress':
                $enrollment_id = $_POST['enrollment_id'] ?? 0;
                $progress = max(0, min(100, $_POST['progress'] ?? 0));
                
                $stmt = $pdo->prepare("UPDATE enrollments SET progress = ? WHERE id = ?");
                $stmt->execute([$progress, $enrollment_id]);
                $message = 'Progress updated successfully!';
                break;
                
            case 'delete_enrollment':
                $enrollment_id = $_POST['enrollment_id'] ?? 0;
                $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
                $stmt->execute([$enrollment_id]);
                $message = 'Enrollment deleted successfully!';
                break;
                
            case 'bulk_action':
                $enrollment_ids = $_POST['enrollment_ids'] ?? [];
                $bulk_action = $_POST['bulk_action'] ?? '';
                
                if (!empty($enrollment_ids) && !empty($bulk_action)) {
                    $placeholders = str_repeat('?,', count($enrollment_ids) - 1) . '?';
                    
                    switch ($bulk_action) {
                        case 'activate':
                            $stmt = $pdo->prepare("UPDATE enrollments SET status = 'active' WHERE id IN ($placeholders)");
                            $stmt->execute($enrollment_ids);
                            $message = count($enrollment_ids) . ' enrollments activated successfully!';
                            break;
                        case 'suspend':
                            $stmt = $pdo->prepare("UPDATE enrollments SET status = 'suspended' WHERE id IN ($placeholders)");
                            $stmt->execute($enrollment_ids);
                            $message = count($enrollment_ids) . ' enrollments suspended successfully!';
                            break;
                        case 'delete':
                            $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id IN ($placeholders)");
                            $stmt->execute($enrollment_ids);
                            $message = count($enrollment_ids) . ' enrollments deleted successfully!';
                            break;
                    }
                }
                break;
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$course_filter = $_GET['course'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build WHERE clause for filtering
$where_conditions = ['1=1'];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = 'e.status = ?';
    $params[] = $status_filter;
}

if ($course_filter !== 'all') {
    $where_conditions[] = 'e.course_id = ?';
    $params[] = $course_filter;
}

if (!empty($search)) {
    $where_conditions[] = '(CONCAT(u.first_name, " ", u.last_name) LIKE ? OR u.email LIKE ? OR c.title LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(' AND ', $where_conditions);

// Get enrollments with user and course info
$stmt = $pdo->prepare("
    SELECT 
        e.*,
        CONCAT(u.first_name, ' ', u.last_name) as student_name,
        u.email as student_email,
        u.profile_image as student_image,
        c.title as course_title,
        c.price as course_price,
        CONCAT(instructor.first_name, ' ', instructor.last_name) as instructor_name
    FROM enrollments e
    JOIN users u ON e.user_id = u.id
    JOIN courses c ON e.course_id = c.id
    LEFT JOIN users instructor ON c.instructor_id = instructor.id
    WHERE $where_clause
    ORDER BY e.enrolled_at DESC
");
$stmt->execute($params);
$enrollments = $stmt->fetchAll();

// Get enrollment statistics
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total_enrollments,
        COUNT(CASE WHEN status = 'active' THEN 1 END) as active_enrollments,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_enrollments,
        COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_enrollments,
        AVG(progress) as avg_progress,
        SUM(CASE WHEN c.price > 0 THEN c.price ELSE 0 END) as total_revenue
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
");
$stats = $stmt->fetch();

// Get courses for filter dropdown
$stmt = $pdo->query("SELECT id, title FROM courses ORDER BY title");
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Management - Admin Panel</title>
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
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        .form-control {
            padding: 0.5rem;
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
        .badge-warning {
            background: #ffc107;
            color: #212529;
        }
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        .badge-secondary {
            background: #6c757d;
            color: white;
        }
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        .progress-bar {
            width: 100px;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            height: 100%;
            background: #28a745;
            transition: width 0.3s;
        }
        .student-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .bulk-actions {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            display: none;
        }
        .bulk-actions.active {
            display: block;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
                Creators-Space Admin
            </div>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? 'Admin'); ?>!</span>
                <a href="/backend/auth/logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </div>
        </div>
    </header>

    <nav class="nav">
        <div class="nav-content">
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php">Users</a>
                <a href="courses.php">Courses</a>
                <a href="enrollments.php" class="active">Enrollments</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <h1 style="margin-bottom: 2rem;">Enrollment Management</h1>

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
                <div class="stat-number"><?php echo number_format($stats['total_enrollments']); ?></div>
                <div class="stat-label">Total Enrollments</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['active_enrollments']); ?></div>
                <div class="stat-label">Active</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['completed_enrollments']); ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['suspended_enrollments']); ?></div>
                <div class="stat-label">Suspended</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($stats['avg_progress'], 1); ?>%</div>
                <div class="stat-label">Avg Progress</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">$<?php echo number_format($stats['total_revenue'], 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
        </div>

        <!-- Enrollments List -->
        <div class="section">
            <h2 style="margin-bottom: 1.5rem;">All Enrollments</h2>
            
            <!-- Filters -->
            <form method="GET" class="filters">
                <div class="filter-group">
                    <label>Status:</label>
                    <select name="status" class="form-control">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Course:</label>
                    <select name="course" class="form-control">
                        <option value="all" <?php echo $course_filter === 'all' ? 'selected' : ''; ?>>All Courses</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo $course_filter == $course['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Search:</label>
                    <input type="text" name="search" class="form-control" placeholder="Student name, email, or course..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
                <a href="enrollments.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            </form>

            <!-- Bulk Actions -->
            <div id="bulkActions" class="bulk-actions">
                <form method="POST" id="bulkForm">
                    <input type="hidden" name="action" value="bulk_action">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <span><span id="selectedCount">0</span> enrollments selected</span>
                        <select name="bulk_action" class="form-control" style="width: auto;">
                            <option value="">Choose action...</option>
                            <option value="activate">Activate</option>
                            <option value="suspend">Suspend</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <button type="button" class="btn btn-secondary" onclick="clearSelection()">Cancel</button>
                    </div>
                </form>
            </div>

            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            </th>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Instructor</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Enrolled</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="enrollment-checkbox" value="<?php echo $enrollment['id']; ?>" onchange="updateSelection()">
                                </td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?php if ($enrollment['student_image']): ?>
                                                <img src="<?php echo htmlspecialchars($enrollment['student_image']); ?>" alt="Student" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                            <?php else: ?>
                                                <?php echo strtoupper(substr($enrollment['student_name'], 0, 1)); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($enrollment['student_name']); ?></strong>
                                            <br><small style="color: #666;"><?php echo htmlspecialchars($enrollment['student_email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($enrollment['course_title']); ?></strong>
                                    <br>
                                    <?php if ($enrollment['course_price'] > 0): ?>
                                        <span class="badge badge-warning">$<?php echo number_format($enrollment['course_price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Free</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($enrollment['instructor_name']): ?>
                                        <?php echo htmlspecialchars($enrollment['instructor_name']); ?>
                                    <?php else: ?>
                                        <span style="color: #999;">No instructor</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status_class = 'badge-secondary';
                                    switch ($enrollment['status']) {
                                        case 'active': $status_class = 'badge-info'; break;
                                        case 'completed': $status_class = 'badge-success'; break;
                                        case 'suspended': $status_class = 'badge-danger'; break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars(ucfirst($enrollment['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $enrollment['progress']; ?>%"></div>
                                    </div>
                                    <small><?php echo number_format($enrollment['progress'], 1); ?>%</small>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline-block; margin-right: 5px;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                        <select name="status" class="form-control" style="width: auto; display: inline;" onchange="this.form.submit()">
                                            <option value="active" <?php echo $enrollment['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                            <option value="completed" <?php echo $enrollment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="suspended" <?php echo $enrollment['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                        </select>
                                    </form>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this enrollment?')">
                                        <input type="hidden" name="action" value="delete_enrollment">
                                        <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.25rem 0.5rem;">
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

    <script>
        function toggleAll(checkbox) {
            const checkboxes = document.querySelectorAll('.enrollment-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelection();
        }

        function updateSelection() {
            const checkboxes = document.querySelectorAll('.enrollment-checkbox:checked');
            const count = checkboxes.length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            const bulkForm = document.getElementById('bulkForm');
            
            selectedCount.textContent = count;
            
            if (count > 0) {
                bulkActions.classList.add('active');
                // Clear existing hidden inputs
                const existingInputs = bulkForm.querySelectorAll('input[name="enrollment_ids[]"]');
                existingInputs.forEach(input => input.remove());
                
                // Add selected IDs as hidden inputs
                checkboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'enrollment_ids[]';
                    input.value = checkbox.value;
                    bulkForm.appendChild(input);
                });
            } else {
                bulkActions.classList.remove('active');
            }
            
            document.getElementById('selectAll').checked = count === document.querySelectorAll('.enrollment-checkbox').length;
        }

        function clearSelection() {
            document.querySelectorAll('.enrollment-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateSelection();
        }
    </script>
</body>
</html>