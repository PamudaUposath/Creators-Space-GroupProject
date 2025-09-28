<?php
// frontend/instructor-courses.php
session_start();

// Check if user is logged in as instructor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

require_once '../backend/config/db_connect.php';

$instructor_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'request_course':
                try {
                    $title = trim($_POST['title']);
                    $description = trim($_POST['description']);
                    $price = floatval($_POST['price']);
                    $duration = trim($_POST['duration']);
                    $level = $_POST['level'];
                    $category = trim($_POST['category']);
                    
                    $stmt = $pdo->prepare("
                        INSERT INTO course_requests (instructor_id, title, description, price, duration, level, category, status, requested_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
                    ");
                    $stmt->execute([$instructor_id, $title, $description, $price, $duration, $level, $category]);
                    
                    $message = "Course request submitted successfully! Admin will review your request.";
                    $message_type = "success";
                } catch (PDOException $e) {
                    $message = "Error submitting course request: " . $e->getMessage();
                    $message_type = "error";
                }
                break;
                
            case 'toggle_course':
                try {
                    $course_id = intval($_POST['course_id']);
                    $is_active = intval($_POST['is_active']);
                    
                    $stmt = $pdo->prepare("
                        UPDATE courses SET is_active = ? WHERE id = ? AND instructor_id = ?
                    ");
                    $stmt->execute([$is_active, $course_id, $instructor_id]);
                    
                    $status = $is_active ? "activated" : "deactivated";
                    $message = "Course $status successfully!";
                    $message_type = "success";
                } catch (PDOException $e) {
                    $message = "Error updating course: " . $e->getMessage();
                    $message_type = "error";
                }
                break;
        }
    }
}

// Get instructor's courses with stats
try {
    $stmt = $pdo->prepare("
        SELECT c.*, 
               COUNT(DISTINCT e.id) as enrolled_students,
               COUNT(DISTINCT cert.id) as certificates_issued,
               AVG(e.progress) as avg_progress,
               COUNT(DISTINCT l.id) as total_lessons
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        LEFT JOIN certificates cert ON c.id = cert.course_id
        LEFT JOIN lessons l ON c.id = l.course_id
        WHERE c.instructor_id = ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$instructor_id]);
    $courses = $stmt->fetchAll();
    
    // Get instructor's course requests
    $stmt = $pdo->prepare("
        SELECT cr.*, 
               CASE 
                   WHEN cr.reviewed_by IS NOT NULL THEN u.first_name 
                   ELSE NULL 
               END as reviewed_by_name
        FROM course_requests cr
        LEFT JOIN users u ON cr.reviewed_by = u.id
        WHERE cr.instructor_id = ?
        ORDER BY cr.requested_at DESC
    ");
    $stmt->execute([$instructor_id]);
    $course_requests = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    $courses = [];
    $course_requests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - Creators Space</title>
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

        .nav-links a:hover, .nav-links a.active {
            color: #667eea;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }

        .action-bar {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
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
            font-size: 0.95rem;
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

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .course-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.2s ease;
        }

        .course-card:hover {
            transform: translateY(-2px);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .course-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .course-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(72, 187, 120, 0.2);
            color: #38a169;
        }

        .status-inactive {
            background: rgba(245, 101, 101, 0.2);
            color: #e53e3e;
        }

        .course-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1rem;
        }

        .course-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3748;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
        }

        .course-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
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
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
        }

        .close {
            color: #64748b;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close:hover {
            color: #e53e3e;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        .form-input, .form-textarea, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }

        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-textarea {
            min-height: 100px;
            resize: vertical;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .message-success {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
            border: 1px solid rgba(72, 187, 120, 0.2);
        }

        .message-error {
            background: rgba(245, 101, 101, 0.1);
            color: #e53e3e;
            border: 1px solid rgba(245, 101, 101, 0.2);
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

        /* Course Requests Styles */
        .requests-section {
            margin: 2rem 0;
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
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .request-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .request-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            line-height: 1.3;
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

        .request-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .request-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .request-level,
        .request-price,
        .request-category {
            padding: 0.25rem 0.6rem;
            background: #f1f5f9;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #475569;
        }

        .request-price {
            background: #ecfdf5;
            color: #065f46;
            font-weight: 600;
        }

        .request-footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 1rem;
        }

        .request-date,
        .request-reviewed {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .admin-notes {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }

        .admin-notes strong {
            font-size: 0.8rem;
            color: #2d3748;
        }

        .admin-notes p {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.25rem 0 0 0;
            line-height: 1.4;
        }

        body.dark-mode .requests-section .section-title {
            color: #ffffff;
        }

        body.dark-mode .request-card {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.15);
        }

        body.dark-mode .request-title {
            color: #ffffff;
        }

        body.dark-mode .request-description {
            color: #e2e8f0;
        }

        body.dark-mode .request-level,
        body.dark-mode .request-category {
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        body.dark-mode .admin-notes {
            background: rgba(255, 255, 255, 0.05);
            border-left-color: #667eea;
        }

        body.dark-mode .admin-notes strong {
            color: #ffffff;
        }

        body.dark-mode .admin-notes p {
            color: #e2e8f0;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header-content {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
            }

            .course-stats {
                grid-template-columns: repeat(4, 1fr);
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
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-book"></i> Course Management
            </h1>
            <p class="page-subtitle">
                Request new courses, manage approved courses, and track student progress
            </p>
        </div>

        <?php if ($message): ?>
            <div class="message message-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="action-bar">
            <button class="btn btn-primary" onclick="openCreateModal()">
                <i class="fas fa-paper-plane"></i> Request New Course
            </button>
        </div>

        <!-- Course Requests Section -->
        <?php if (!empty($course_requests)): ?>
            <div class="requests-section">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i> Your Course Requests
                </h2>
                <div class="requests-grid">
                    <?php foreach ($course_requests as $request): ?>
                        <div class="request-card">
                            <div class="request-header">
                                <h3 class="request-title"><?php echo htmlspecialchars($request['title']); ?></h3>
                                <span class="request-status status-<?php echo $request['status']; ?>">
                                    <?php echo ucfirst($request['status']); ?>
                                </span>
                            </div>
                            
                            <div class="request-details">
                                <p class="request-description"><?php echo htmlspecialchars(substr($request['description'], 0, 150)); ?>...</p>
                                <div class="request-meta">
                                    <span class="request-level"><?php echo $request['level']; ?></span>
                                    <span class="request-price">$<?php echo number_format($request['price'], 2); ?></span>
                                    <span class="request-category"><?php echo htmlspecialchars($request['category']); ?></span>
                                </div>
                            </div>
                            
                            <div class="request-footer">
                                <small class="request-date">
                                    Requested: <?php echo date('M d, Y', strtotime($request['requested_at'])); ?>
                                </small>
                                <?php if ($request['reviewed_at']): ?>
                                    <small class="request-reviewed">
                                        Reviewed: <?php echo date('M d, Y', strtotime($request['reviewed_at'])); ?>
                                        <?php if ($request['reviewed_by_name']): ?>
                                            by <?php echo htmlspecialchars($request['reviewed_by_name']); ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                                <?php if ($request['admin_notes']): ?>
                                    <div class="admin-notes">
                                        <strong>Admin Notes:</strong>
                                        <p><?php echo htmlspecialchars($request['admin_notes']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($courses)): ?>
            <div class="empty-state">
                <i class="fas fa-paper-plane"></i>
                <h3>No courses yet</h3>
                <p>Request your first course to start sharing your knowledge with students. Admin will review and approve your requests.</p>
                <button class="btn btn-primary" onclick="openCreateModal()" style="margin-top: 1rem;">
                    <i class="fas fa-paper-plane"></i> Request Your First Course
                </button>
            </div>
        <?php else: ?>
            <div class="courses-grid">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div>
                                <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                                <div class="course-status <?php echo $course['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo $course['is_active'] ? 'Active' : 'Inactive'; ?>
                                </div>
                            </div>
                        </div>

                        <div class="course-meta">
                            <span><i class="fas fa-layer-group"></i> <?php echo ucfirst($course['level']); ?></span>
                            <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['duration']); ?></span>
                            <span><i class="fas fa-dollar-sign"></i> $<?php echo number_format($course['price'], 2); ?></span>
                        </div>

                        <div class="course-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $course['enrolled_students']; ?></div>
                                <div class="stat-label">Students</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $course['total_lessons']; ?></div>
                                <div class="stat-label">Lessons</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $course['certificates_issued']; ?></div>
                                <div class="stat-label">Certificates</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo number_format($course['avg_progress'] ?? 0, 0); ?>%</div>
                                <div class="stat-label">Avg Progress</div>
                            </div>
                        </div>

                        <div class="course-actions">
                            <a href="#" class="btn btn-secondary btn-small">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" class="btn btn-secondary btn-small">
                                <i class="fas fa-list"></i> Lessons
                            </a>
                            <form style="display: inline;" method="POST">
                                <input type="hidden" name="action" value="toggle_course">
                                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                                <input type="hidden" name="is_active" value="<?php echo $course['is_active'] ? 0 : 1; ?>">
                                <button type="submit" class="btn <?php echo $course['is_active'] ? 'btn-danger' : 'btn-success'; ?> btn-small">
                                    <i class="fas fa-<?php echo $course['is_active'] ? 'pause' : 'play'; ?>"></i>
                                    <?php echo $course['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

        <!-- Request Course Modal -->
        <div id="createCourseModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Request New Course</h2>
                    <span class="close" onclick="closeCreateModal()">&times;</span>
                </div>

                <form method="POST">
                    <input type="hidden" name="action" value="request_course">                <div class="form-group">
                    <label class="form-label">Course Title *</label>
                    <input type="text" name="title" class="form-input" required placeholder="e.g., Advanced React Development">
                </div>

                <div class="form-group">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-textarea" required placeholder="Describe what students will learn in this course..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Price ($) *</label>
                    <input type="number" name="price" class="form-input" required min="0" step="0.01" placeholder="99.99">
                </div>

                <div class="form-group">
                    <label class="form-label">Duration *</label>
                    <input type="text" name="duration" class="form-input" required placeholder="e.g., 8 weeks, 40 hours">
                </div>

                <div class="form-group">
                    <label class="form-label">Level *</label>
                    <select name="level" class="form-select" required>
                        <option value="">Select Level</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Category *</label>
                    <input type="text" name="category" class="form-input" required placeholder="e.g., Web Development, Data Science">
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeCreateModal()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createCourseModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createCourseModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('createCourseModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

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

        console.log('Course management page loaded successfully');
    </script>
</body>
</html>