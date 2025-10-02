<?php
// backend/admin/users.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/email_helper.php';

// Require admin authentication
requireAdmin();

// Handle user actions (activate/deactivate/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($action === 'add_instructor') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');

        // Generate temp password
        $temp_password = substr(bin2hex(random_bytes(4)), 0, 8);
        $password_hash = password_hash($temp_password, PASSWORD_DEFAULT);

        // Check if email or username exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        $exists = $stmt->fetchColumn();
        if ($exists) {
            $_SESSION['error'] = 'Email or username already exists.';
            header('Location: /Creators-Space-GroupProject/backend/admin/users.php');
            exit;
        }

        // Insert instructor
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, username, password_hash, role, is_active, created_at) VALUES (?, ?, ?, ?, ?, 'instructor', 1, NOW())");
        $stmt->execute([$first_name, $last_name, $email, $username, $password_hash]);
        $new_id = $pdo->lastInsertId();

        // Send email using PHPMailer
        if (sendInstructorWelcomeEmail($email, $first_name, $username, $temp_password)) {
            $_SESSION['message'] = 'Instructor added and email sent successfully.';
        } else {
            $_SESSION['message'] = 'Instructor added, but email could not be sent. Please check email configuration.';
        }
        logActivity($_SESSION['user_id'], 'admin_user_action', "Added instructor: $new_id");
        header('Location: /Creators-Space-GroupProject/backend/admin/users.php');
        exit;
    }

    if ($userId > 0) {
        try {
            switch ($action) {
                case 'toggle_status':
                    $stmt = $pdo->prepare("UPDATE users SET is_active = !is_active WHERE id = ? AND role != 'admin'");
                    $stmt->execute([$userId]);
                    $_SESSION['message'] = 'User status updated successfully.';
                    break;

                case 'delete':
                    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ? AND role != 'admin'");
                    $stmt->execute([$userId]);
                    $_SESSION['message'] = 'User deleted successfully.';
                    break;

                case 'make_instructor':
                    $stmt = $pdo->prepare("UPDATE users SET role = 'instructor' WHERE id = ? AND role = 'user'");
                    $stmt->execute([$userId]);
                    $_SESSION['message'] = 'User promoted to instructor.';
                    break;
            }

            // Log admin action
            logActivity($_SESSION['user_id'], 'admin_user_action', "Action: $action on user ID: $userId");
        } catch (PDOException $e) {
            error_log("User management error: " . $e->getMessage());
            $_SESSION['error'] = 'Action failed. Please try again.';
        }
    }

    header('Location: /Creators-Space-GroupProject/backend/admin/users.php');
    exit;
}

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Search functionality
$search = $_GET['search'] ?? '';
$roleFilter = $_GET['role'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if (!empty($search)) {
    $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (!empty($roleFilter)) {
    $whereConditions[] = "role = ?";
    $params[] = $roleFilter;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

try {
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
    $stmt = $pdo->prepare($countQuery);
    $stmt->execute($params);
    $totalUsers = $stmt->fetch()['total'];
    $totalPages = ceil($totalUsers / $limit);

    // Get users
    $query = "
        SELECT id, first_name, last_name, email, username, role, is_active, created_at,
               (SELECT COUNT(*) FROM enrollments WHERE user_id = users.id) as enrollment_count
        FROM users 
        $whereClause
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Users fetch error: " . $e->getMessage());
    $users = [];
    $totalUsers = 0;
    $totalPages = 0;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Creators-Space Admin</title>
    <link rel="shortcut icon" href="/frontend/favicon.ico" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #d7d8d8ff;
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
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .search-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            margin-bottom: 2rem;
        }

        .search-form {
            display: flex;
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            flex: 1;
            text-align: center;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .users-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }

        .user-avatar {
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

        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .user-email {
            color: #666;
            font-size: 0.9rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-admin {
            background: #d4edda;
            color: #155724;
        }

        .role-instructor {
            background: #fff3cd;
            color: #856404;
        }

        .role-user {
            background: #e2e3e5;
            color: #495057;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
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
            font-size: 0.8rem;
        }

        .btn:hover {
            background: #5a6fd8;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.7rem;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-warning {
            background: #ffc107;
            color: #000;
        }

        .btn-success {
            background: #28a745;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
            color: #667eea;
        }

        .pagination .current {
            background: #667eea;
            color: white;
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

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
                align-items: center;
            }

            .users-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            .form-group label {
                text-align: center;
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
                <a href="dashboard.php">Dashboard</a>
                <a href="users.php" class="active">Users</a>
                <a href="courses.php">Courses</a>
                <a href="course-requests.php">Course Requests</a>
                <a href="enrollments.php">Enrollments</a>
                <a href="student-reports.php">Student Reports</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="page-header">
            <h1>Users Management</h1>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <span>Total: <?php echo number_format($totalUsers); ?> users</span>
                <button type="button" class="btn btn-success" onclick="document.getElementById('addInstructorModal').style.display='block'">
                    Add an Instructor
                </button>
            </div>
        </div>

        <!-- Add Instructor Modal -->
        <div id="addInstructorModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
            <div style="background:white; padding:2rem; border-radius:10px; max-width:400px; margin:5vh auto; position:relative;">
                <h2 style="margin-bottom:1rem;">Add Instructor</h2>
                <form method="POST" id="addInstructorForm">
                    <input type="hidden" name="action" value="add_instructor">
                    <div style="margin-bottom:1rem;">
                        <label for="first_name">First Name *</label><br>
                        <input type="text" name="first_name" id="first_name" required style="width:100%; padding:0.5rem;">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="last_name">Last Name *</label><br>
                        <input type="text" name="last_name" id="last_name" required style="width:100%; padding:0.5rem;">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="email">Email *</label><br>
                        <input type="email" name="email" id="email" required style="width:100%; padding:0.5rem;">
                    </div>
                    <div style="margin-bottom:1rem;">
                        <label for="username">Username *</label><br>
                        <input type="text" name="username" id="username" required style="width:100%; padding:0.5rem;">
                    </div>
                    <button type="submit" class="btn btn-success" style="width:100%;">Add Instructor</button>
                </form>
                <button type="button" class="btn btn-danger" style="position:absolute; top:10px; right:10px;" onclick="document.getElementById('addInstructorModal').style.display='none'">&times;</button>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="search-section">
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label for="search">Search Users</label>
                    <input type="text" id="search" name="search"
                        value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search by name or email...">
                </div>
                <div class="form-group">
                    <label for="role">Role Filter</label>
                    <select id="role" name="role">
                        <option value="">All Roles</option>
                        <option value="user" <?php echo $roleFilter === 'user' ? 'selected' : ''; ?>>Users</option>
                        <option value="instructor" <?php echo $roleFilter === 'instructor' ? 'selected' : ''; ?>>Instructors</option>
                        <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Search</button>
                </div>
            </form>
        </div>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Enrollments</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">No users found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="user-info-cell">
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">
                                                <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                                            </div>
                                            <div class="user-email"><?php echo htmlspecialchars($user['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $user['is_active'] ? 'active' : 'inactive'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['enrollment_count']; ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['role'] !== 'admin' || $user['id'] !== $_SESSION['user_id']): ?>
                                        <div class="action-buttons">
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <button type="submit" class="btn btn-sm btn-warning"
                                                    onclick="return confirm('Toggle user status?')">
                                                    <?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>

                                            <?php if ($user['role'] === 'user'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <input type="hidden" name="action" value="make_instructor">
                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Promote to instructor?')">
                                                        Promote
                                                    </button>
                                                </form>
                                            <?php endif; ?>

                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this user? This action cannot be undone.')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #999;">Cannot modify</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($roleFilter); ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($roleFilter); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($roleFilter); ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
        // Auto-submit search form on role change
        document.getElementById('role').addEventListener('change', function() {
            this.form.submit();
        });

        // Close modal on outside click
        window.onclick = function(event) {
            var modal = document.getElementById('addInstructorModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>