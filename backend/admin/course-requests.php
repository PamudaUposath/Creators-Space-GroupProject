<?php
// backend/admin/course-requests.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Require admin authentication
requireAdmin();

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve_request':
                try {
                    $request_id = intval($_POST['request_id']);
                    $admin_notes = trim($_POST['admin_notes'] ?? '');
                    
                    // Get the request details
                    $stmt = $pdo->prepare("SELECT * FROM course_requests WHERE id = ? AND status = 'pending'");
                    $stmt->execute([$request_id]);
                    $request = $stmt->fetch();
                    
                    if ($request) {
                        // Start transaction
                        $pdo->beginTransaction();
                        
                        // Create the course
                        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $request['title']));
                        $stmt = $pdo->prepare("
                            INSERT INTO courses (title, slug, description, instructor_id, price, duration, level, category, is_active, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())
                        ");
                        $stmt->execute([
                            $request['title'],
                            $slug,
                            $request['description'],
                            $request['instructor_id'],
                            $request['price'],
                            $request['duration'],
                            $request['level'],
                            $request['category']
                        ]);
                        
                        $course_id = $pdo->lastInsertId();
                        
                        // Update the request status
                        $stmt = $pdo->prepare("
                            UPDATE course_requests 
                            SET status = 'approved', 
                                reviewed_at = NOW(), 
                                reviewed_by = ?, 
                                admin_notes = ?,
                                course_id = ?
                            WHERE id = ?
                        ");
                        $stmt->execute([$_SESSION['user_id'], $admin_notes, $course_id, $request_id]);
                        
                        $pdo->commit();
                        
                        $message = "Course request approved successfully! Course has been created.";
                        $message_type = "success";
                    } else {
                        $message = "Course request not found or already processed.";
                        $message_type = "error";
                    }
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $message = "Error approving course request: " . $e->getMessage();
                    $message_type = "error";
                }
                break;
                
            case 'reject_request':
                try {
                    $request_id = intval($_POST['request_id']);
                    $admin_notes = trim($_POST['admin_notes'] ?? '');
                    
                    $stmt = $pdo->prepare("
                        UPDATE course_requests 
                        SET status = 'rejected', 
                            reviewed_at = NOW(), 
                            reviewed_by = ?, 
                            admin_notes = ?
                        WHERE id = ? AND status = 'pending'
                    ");
                    $stmt->execute([$_SESSION['user_id'], $admin_notes, $request_id]);
                    
                    if ($stmt->rowCount() > 0) {
                        $message = "Course request rejected.";
                        $message_type = "success";
                    } else {
                        $message = "Course request not found or already processed.";
                        $message_type = "error";
                    }
                } catch (PDOException $e) {
                    $message = "Error rejecting course request: " . $e->getMessage();
                    $message_type = "error";
                }
                break;
        }
    }
}

// Get course requests with instructor info
try {
    $stmt = $pdo->query("
        SELECT cr.*, 
               u.first_name, 
               u.last_name, 
               u.email,
               reviewer.first_name as reviewer_first_name,
               reviewer.last_name as reviewer_last_name
        FROM course_requests cr
        JOIN users u ON cr.instructor_id = u.id
        LEFT JOIN users reviewer ON cr.reviewed_by = reviewer.id
        ORDER BY 
            CASE cr.status 
                WHEN 'pending' THEN 1 
                WHEN 'approved' THEN 2 
                WHEN 'rejected' THEN 3 
            END,
            cr.requested_at DESC
    ");
    $course_requests = $stmt->fetchAll();
    
    // Get statistics
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests
        FROM course_requests
    ");
    $stats = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Error fetching course requests: " . $e->getMessage());
    $course_requests = [];
    $stats = ['total_requests' => 0, 'pending_requests' => 0, 'approved_requests' => 0, 'rejected_requests' => 0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Requests - Admin Panel</title>
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            margin: -2rem -2rem 2rem -2rem;
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #667eea;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            color: white;
        }

        .stat-icon.requests { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.pending { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.approved { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.rejected { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .message {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .message.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .requests-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .requests-grid {
            display: grid;
            gap: 1.5rem;
        }

        .request-card {
            background: #f8fafc;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .request-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .request-info h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .instructor-info {
            font-size: 0.9rem;
            color: #64748b;
        }

        .request-status {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .request-details {
            margin-bottom: 1.5rem;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            background: white;
            padding: 0.75rem;
            border-radius: 6px;
            text-align: center;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-weight: 600;
            color: #2d3748;
        }

        .description {
            background: white;
            padding: 1rem;
            border-radius: 6px;
            border-left: 3px solid #667eea;
            margin: 1rem 0;
        }

        .description h4 {
            font-size: 0.9rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .description p {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .request-actions {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .action-form {
            flex: 1;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }

        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-approve {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-approve:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        }

        .btn-reject {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-reject:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .reviewed-info {
            background: #f1f5f9;
            padding: 1rem;
            border-radius: 6px;
            border-left: 3px solid #64748b;
        }

        .reviewed-info h4 {
            font-size: 0.9rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .reviewed-info p {
            font-size: 0.8rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .admin-notes {
            background: white;
            padding: 0.75rem;
            border-radius: 4px;
            margin-top: 0.5rem;
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header {
                margin: -1rem -1rem 2rem -1rem;
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .nav-links {
                gap: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .request-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .request-actions {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header class="header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-clipboard-list"></i> Course Requests
                </h1>
                <nav class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="users.php">Users</a>
                    <a href="courses.php">Courses</a>
                    <a href="course-requests.php" class="active">Course Requests</a>
                    <a href="enrollments.php">Enrollments</a>
                </nav>
            </div>
        </header>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon requests">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['total_requests']); ?></div>
                <div class="stat-label">Total Requests</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['pending_requests']); ?></div>
                <div class="stat-label">Pending</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon approved">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['approved_requests']); ?></div>
                <div class="stat-label">Approved</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon rejected">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stat-number"><?php echo number_format($stats['rejected_requests']); ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="requests-section">
            <h2 class="section-title">
                <i class="fas fa-list"></i> Course Requests
            </h2>

            <?php if (empty($course_requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard"></i>
                    <h3>No course requests</h3>
                    <p>No course requests have been submitted yet.</p>
                </div>
            <?php else: ?>
                <div class="requests-grid">
                    <?php foreach ($course_requests as $request): ?>
                        <div class="request-card">
                            <div class="request-header">
                                <div class="request-info">
                                    <h3><?php echo htmlspecialchars($request['title']); ?></h3>
                                    <div class="instructor-info">
                                        <i class="fas fa-user"></i>
                                        <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                        (<?php echo htmlspecialchars($request['email']); ?>)
                                    </div>
                                </div>
                                <span class="request-status status-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </div>

                            <div class="request-details">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <div class="detail-label">Level</div>
                                        <div class="detail-value"><?php echo $request['level']; ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Price</div>
                                        <div class="detail-value">$<?php echo number_format($request['price'], 2); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Duration</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($request['duration']); ?></div>
                                    </div>
                                    <div class="detail-item">
                                        <div class="detail-label">Category</div>
                                        <div class="detail-value"><?php echo htmlspecialchars($request['category']); ?></div>
                                    </div>
                                </div>

                                <div class="description">
                                    <h4>Course Description</h4>
                                    <p><?php echo nl2br(htmlspecialchars($request['description'])); ?></p>
                                </div>

                                <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.5rem;">
                                    <i class="fas fa-calendar"></i> Requested: <?php echo date('M d, Y g:i A', strtotime($request['requested_at'])); ?>
                                </p>
                            </div>

                            <?php if ($request['status'] === 'pending'): ?>
                                <div class="request-actions">
                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <input type="hidden" name="action" value="approve_request">
                                        <div class="form-group">
                                            <label class="form-label">Admin Notes (Optional)</label>
                                            <textarea name="admin_notes" class="form-textarea" placeholder="Add any notes for the instructor..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-approve">
                                            <i class="fas fa-check"></i> Approve & Create Course
                                        </button>
                                    </form>

                                    <form method="POST" class="action-form">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <input type="hidden" name="action" value="reject_request">
                                        <div class="form-group">
                                            <label class="form-label">Rejection Reason</label>
                                            <textarea name="admin_notes" class="form-textarea" placeholder="Please provide a reason for rejection..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-reject">
                                            <i class="fas fa-times"></i> Reject Request
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="reviewed-info">
                                    <h4>Review Information</h4>
                                    <p><strong>Status:</strong> <?php echo ucfirst($request['status']); ?></p>
                                    <p><strong>Reviewed:</strong> <?php echo date('M d, Y g:i A', strtotime($request['reviewed_at'])); ?></p>
                                    <?php if ($request['reviewer_first_name']): ?>
                                        <p><strong>Reviewed by:</strong> <?php echo htmlspecialchars($request['reviewer_first_name'] . ' ' . $request['reviewer_last_name']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($request['admin_notes']): ?>
                                        <div class="admin-notes">
                                            <strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($request['admin_notes'])); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($request['status'] === 'approved' && $request['course_id']): ?>
                                        <p style="margin-top: 0.5rem;">
                                            <a href="courses.php" style="color: #10b981;">
                                                <i class="fas fa-external-link-alt"></i> View Created Course
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
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