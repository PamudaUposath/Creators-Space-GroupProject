<?php
// backend/admin/student-reports.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Require admin authentication
requireAdmin();

$message = '';
$message_type = '';

// Handle report actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'update_status':
                $report_id = intval($_POST['report_id']);
                $status = $_POST['status'];
                $admin_notes = trim($_POST['admin_notes'] ?? '');

                $stmt = $pdo->prepare("
                    UPDATE student_reports 
                    SET status = ?, admin_notes = ?, reviewed_by = ?, reviewed_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$status, $admin_notes, $_SESSION['user_id'], $report_id]);

                $message = "Report status updated successfully!";
                $message_type = "success";
                break;

            case 'add_resolution':
                $report_id = intval($_POST['report_id']);
                $resolution_action = trim($_POST['resolution_action']);

                $stmt = $pdo->prepare("
                    UPDATE student_reports 
                    SET resolution_action = ?, status = 'resolved', reviewed_by = ?, reviewed_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$resolution_action, $_SESSION['user_id'], $report_id]);

                $message = "Resolution added and report marked as resolved!";
                $message_type = "success";
                break;
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$severity_filter = $_GET['severity'] ?? '';
$type_filter = $_GET['type'] ?? '';

// Build the query for reports
$where_clauses = [];
$params = [];

if ($status_filter) {
    $where_clauses[] = "sr.status = ?";
    $params[] = $status_filter;
}

if ($severity_filter) {
    $where_clauses[] = "sr.severity = ?";
    $params[] = $severity_filter;
}

if ($type_filter) {
    $where_clauses[] = "sr.report_type = ?";
    $params[] = $type_filter;
}

$where_sql = $where_clauses ? ' WHERE ' . implode(' AND ', $where_clauses) : '';

// Get student reports with details
try {
    $stmt = $pdo->prepare("
        SELECT sr.*, 
               u_instructor.first_name as instructor_first_name,
               u_instructor.last_name as instructor_last_name,
               u_instructor.email as instructor_email,
               u_student.first_name as student_first_name,
               u_student.last_name as student_last_name,
               u_student.email as student_email,
               c.title as course_title,
               u_admin.first_name as admin_first_name,
               u_admin.last_name as admin_last_name
        FROM student_reports sr
        JOIN users u_instructor ON sr.instructor_id = u_instructor.id
        JOIN users u_student ON sr.student_id = u_student.id
        LEFT JOIN courses c ON sr.course_id = c.id
        LEFT JOIN users u_admin ON sr.reviewed_by = u_admin.id
        {$where_sql}
        ORDER BY sr.submitted_at DESC
    ");
    $stmt->execute($params);
    $reports = $stmt->fetchAll();

    // Get summary statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_reports,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review_count,
            SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_count,
            SUM(CASE WHEN status = 'dismissed' THEN 1 ELSE 0 END) as dismissed_count,
            SUM(CASE WHEN severity = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
            SUM(CASE WHEN severity = 'high' THEN 1 ELSE 0 END) as high_count
        FROM student_reports
    ");
    $stats = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Error fetching reports: " . $e->getMessage());
    $reports = [];
    $stats = ['total_reports' => 0, 'pending_count' => 0, 'under_review_count' => 0, 'resolved_count' => 0, 'dismissed_count' => 0, 'urgent_count' => 0, 'high_count' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Reports - Admin Panel</title>
    <link rel="icon" type="image/svg+xml" href="assets/admin-favicon.svg">
    <link rel="shortcut icon" href="assets/admin-favicon.svg" type="image/svg+xml">
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
            background: #d7d8d8ff;
            min-height: 100vh;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Standard Admin Header */
        .header {
            background: linear-gradient(135deg, #5a73e5 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Standard Admin Navigation */
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

        /* Main Content */
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
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-left: 4px solid;
        }

        .stat-card.total {
            border-left-color: #3b82f6;
        }

        .stat-card.pending {
            border-left-color: #f59e0b;
        }

        .stat-card.under-review {
            border-left-color: #8b5cf6;
        }

        .stat-card.resolved {
            border-left-color: #10b981;
        }

        .stat-card.urgent {
            border-left-color: #ef4444;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .filters h3 {
            margin-bottom: 1rem;
            color: #374151;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }

        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
        }

        .reports-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
        }

        .reports-table {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .severity-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .severity-low {
            background: #dcfce7;
            color: #166534;
        }

        .severity-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .severity-high {
            background: #fee2e2;
            color: #dc2626;
        }

        .severity-urgent {
            background: #fecaca;
            color: #991b1b;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-under_review {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-resolved {
            background: #dcfce7;
            color: #166534;
        }

        .status-dismissed {
            background: #f3f4f6;
            color: #374151;
        }

        .type-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            background: #f3f4f6;
            color: #374151;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-small {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .message {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 8px;
            border-left: 4px solid;
        }

        .message.success {
            background: #dcfce7;
            border-left-color: #10b981;
            color: #166534;
        }

        .message.error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #dc2626;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .report-details {
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .reports-table {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 0.5rem;
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
                <a href="users.php">Users</a>
                <a href="courses.php">Courses</a>
                <a href="course-requests.php">Course Requests</a>
                <a href="enrollments.php">Enrollments</a>
                <a href="student-reports.php" class="active">Student Reports</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <h1 style="margin-bottom: 2rem;">Student Reports Management</h1>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card total">
                <div class="stat-number"><?php echo $stats['total_reports']; ?></div>
                <div class="stat-label">Total Reports</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?php echo $stats['pending_count']; ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card under-review">
                <div class="stat-number"><?php echo $stats['under_review_count']; ?></div>
                <div class="stat-label">Under Review</div>
            </div>
            <div class="stat-card resolved">
                <div class="stat-number"><?php echo $stats['resolved_count']; ?></div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-card urgent">
                <div class="stat-number"><?php echo $stats['urgent_count'] + $stats['high_count']; ?></div>
                <div class="stat-label">High Priority</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <h3><i class="fas fa-filter"></i> Filter Reports</h3>
            <form method="GET">
                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="under_review" <?php echo $status_filter === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                            <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                            <option value="dismissed" <?php echo $status_filter === 'dismissed' ? 'selected' : ''; ?>>Dismissed</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="severity">Severity:</label>
                        <select name="severity" id="severity" onchange="this.form.submit()">
                            <option value="">All Severities</option>
                            <option value="low" <?php echo $severity_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo $severity_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo $severity_filter === 'high' ? 'selected' : ''; ?>>High</option>
                            <option value="urgent" <?php echo $severity_filter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="type">Report Type:</label>
                        <select name="type" id="type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="academic_concern" <?php echo $type_filter === 'academic_concern' ? 'selected' : ''; ?>>Academic Concern</option>
                            <option value="behavior_issue" <?php echo $type_filter === 'behavior_issue' ? 'selected' : ''; ?>>Behavior Issue</option>
                            <option value="attendance_problem" <?php echo $type_filter === 'attendance_problem' ? 'selected' : ''; ?>>Attendance Problem</option>
                            <option value="inappropriate_conduct" <?php echo $type_filter === 'inappropriate_conduct' ? 'selected' : ''; ?>>Inappropriate Conduct</option>
                            <option value="plagiarism" <?php echo $type_filter === 'plagiarism' ? 'selected' : ''; ?>>Plagiarism</option>
                            <option value="other" <?php echo $type_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Reports Table -->
        <div class="reports-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="fas fa-list"></i> Student Reports
                    <span style="color: #64748b; font-weight: normal; font-size: 1rem;">
                        (<?php echo count($reports); ?> reports)
                    </span>
                </h2>
            </div>

            <div class="reports-table">
                <?php if (empty($reports)): ?>
                    <div style="padding: 3rem; text-align: center; color: #64748b;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>No reports found matching the selected criteria.</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Report Details</th>
                                <th>Instructor</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Type & Severity</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td>
                                        <div class="report-details">
                                            <strong><?php echo htmlspecialchars($report['subject']); ?></strong>
                                            <div style="color: #64748b; font-size: 0.8rem; margin-top: 0.25rem;">
                                                ID: #<?php echo $report['id']; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($report['instructor_first_name'], 0, 1) . substr($report['instructor_last_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($report['instructor_first_name'] . ' ' . $report['instructor_last_name']); ?></strong>
                                                <div style="color: #64748b; font-size: 0.8rem;">
                                                    <?php echo htmlspecialchars($report['instructor_email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <?php echo strtoupper(substr($report['student_first_name'], 0, 1) . substr($report['student_last_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars($report['student_first_name'] . ' ' . $report['student_last_name']); ?></strong>
                                                <div style="color: #64748b; font-size: 0.8rem;">
                                                    <?php echo htmlspecialchars($report['student_email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($report['course_title']): ?>
                                            <span><?php echo htmlspecialchars($report['course_title']); ?></span>
                                        <?php else: ?>
                                            <span style="color: #64748b; font-style: italic;">General Report</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                            <span class="type-badge">
                                                <?php echo ucwords(str_replace('_', ' ', $report['report_type'])); ?>
                                            </span>
                                            <span class="severity-badge severity-<?php echo $report['severity']; ?>">
                                                <?php echo ucfirst($report['severity']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $report['status']; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $report['status'])); ?>
                                        </span>
                                        <?php if ($report['admin_first_name']): ?>
                                            <div style="color: #64748b; font-size: 0.75rem; margin-top: 0.25rem;">
                                                by <?php echo htmlspecialchars($report['admin_first_name'] . ' ' . $report['admin_last_name']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            <?php echo date('M j, Y', strtotime($report['submitted_at'])); ?>
                                        </div>
                                        <div style="color: #64748b; font-size: 0.8rem;">
                                            <?php echo date('g:i A', strtotime($report['submitted_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.25rem; flex-direction: column;">
                                            <button onclick="viewReport(<?php echo $report['id']; ?>)" class="btn btn-primary btn-small">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <?php if ($report['status'] !== 'resolved'): ?>
                                                <button onclick="updateStatus(<?php echo $report['id']; ?>)" class="btn btn-warning btn-small">
                                                    <i class="fas fa-edit"></i> Update
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- View Report Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('viewModal')">&times;</span>
            <div id="reportDetails"></div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('updateModal')">&times;</span>
            <h3>Update Report Status</h3>
            <form method="POST" id="updateForm">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="report_id" id="updateReportId">

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="updateStatus" required>
                        <option value="pending">Pending</option>
                        <option value="under_review">Under Review</option>
                        <option value="resolved">Resolved</option>
                        <option value="dismissed">Dismissed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="admin_notes">Admin Notes:</label>
                    <textarea name="admin_notes" id="adminNotes" rows="3" placeholder="Add your notes about this report..."></textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" onclick="closeModal('updateModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Store all reports data for JavaScript access
        const allReports = <?php echo json_encode($reports); ?>;

        function viewReport(reportId) {
            const report = allReports.find(r => r.id == reportId);
            if (!report) return;

            const details = `
                <h3><i class="fas fa-flag"></i> Report Details</h3>
                <div style="margin: 1.5rem 0;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div>
                            <strong>Report ID:</strong> #${report.id}<br>
                            <strong>Type:</strong> ${report.report_type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}<br>
                            <strong>Severity:</strong> <span class="severity-badge severity-${report.severity}">${report.severity.charAt(0).toUpperCase() + report.severity.slice(1)}</span><br>
                            <strong>Status:</strong> <span class="status-badge status-${report.status}">${report.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
                        </div>
                        <div>
                            <strong>Submitted:</strong> ${new Date(report.submitted_at).toLocaleDateString()}<br>
                            <strong>Instructor:</strong> ${report.instructor_first_name} ${report.instructor_last_name}<br>
                            <strong>Student:</strong> ${report.student_first_name} ${report.student_last_name}<br>
                            <strong>Course:</strong> ${report.course_title || 'General Report'}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <strong>Subject:</strong><br>
                        <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-top: 0.5rem;">
                            ${report.subject}
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <strong>Description:</strong><br>
                        <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin-top: 0.5rem; white-space: pre-wrap;">
                            ${report.description}
                        </div>
                    </div>
                    
                    ${report.admin_notes ? `
                        <div style="margin-bottom: 1.5rem;">
                            <strong>Admin Notes:</strong><br>
                            <div style="background: #fef3c7; padding: 1rem; border-radius: 8px; margin-top: 0.5rem;">
                                ${report.admin_notes}
                            </div>
                        </div>
                    ` : ''}
                    
                    ${report.resolution_action ? `
                        <div>
                            <strong>Resolution Action:</strong><br>
                            <div style="background: #dcfce7; padding: 1rem; border-radius: 8px; margin-top: 0.5rem;">
                                ${report.resolution_action}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;

            document.getElementById('reportDetails').innerHTML = details;
            document.getElementById('viewModal').style.display = 'block';
        }

        function updateStatus(reportId) {
            const report = allReports.find(r => r.id == reportId);
            if (!report) return;

            document.getElementById('updateReportId').value = reportId;
            document.getElementById('updateStatus').value = report.status;
            document.getElementById('adminNotes').value = report.admin_notes || '';
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }

        // Auto-hide messages
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            });
        }, 5000);
    </script>
</body>

</html>