<?php
// frontend/instructor-messages.php
session_start();

// Check if user is logged in as instructor or admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'instructor' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit;
}

require_once '../backend/config/db_connect.php';

$instructor_id = $_SESSION['user_id'];
$page_title = 'Messages';
$page_description = 'Communicate with your students and manage course discussions';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Creators Space</title>
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
            gap: 0.5rem;
        }

        .theme-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .theme-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Main Content */
        .main-content {
            margin-top: 80px;
            min-height: calc(100vh - 80px);
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 350px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-right: 1px solid rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.8);
        }

        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .sidebar-subtitle {
            color: #64748b;
            font-size: 0.9rem;
        }

        .conversation-list {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            border: 1px solid transparent;
        }

        .conversation-item:hover {
            background: rgba(102, 126, 234, 0.05);
            border-color: rgba(102, 126, 234, 0.1);
        }

        .conversation-item.active {
            background: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .conversation-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }

        .conversation-last-message {
            color: #64748b;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-meta {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.25rem;
        }

        .conversation-time {
            font-size: 0.8rem;
            color: #64748b;
        }

        .unread-badge {
            background: #667eea;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Chat Area */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
        }

        #chatContent {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .chat-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.8);
        }

        .chat-user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .chat-user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1rem;
        }

        .chat-user-details h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .chat-user-details p {
            color: #64748b;
            font-size: 0.9rem;
        }

        .messages-container {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message {
            display: flex;
            max-width: 70%;
        }

        .message.sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }

        .message.received {
            align-self: flex-start;
        }

        .message-bubble {
            padding: 0.75rem 1rem;
            border-radius: 16px;
            position: relative;
            word-wrap: break-word;
        }

        .message.sent .message-bubble {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.received .message-bubble {
            background: rgba(0,0,0,0.05);
            color: #2d3748;
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.25rem;
        }

        .message-subject {
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        /* Message Input */
        .message-input-container {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.8);
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .message-input-form {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }

        .message-input {
            flex: 1;
            resize: vertical;
            min-height: 40px;
            max-height: 120px;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 0.9rem;
            background: white;
            transition: border-color 0.3s ease;
        }

        .message-input:focus {
            outline: none;
            border-color: #667eea;
        }

        .send-button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .send-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Empty States */
        .empty-conversation {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 400px;
            color: #64748b;
            text-align: center;
            padding: 2rem;
            flex: 1;
        }

        .empty-conversation i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
            color: #667eea;
        }

        .empty-conversation h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #2d3748;
        }

        .empty-conversation p {
            font-size: 1rem;
            opacity: 0.8;
            max-width: 300px;
            line-height: 1.5;
        }

        .empty-messages {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            color: #64748b;
            text-align: center;
        }

        .empty-messages i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Loading States */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #64748b;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Course Badge */
        .course-badge {
            display: inline-block;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: 50vh;
            }
            
            .navbar-container {
                padding: 0 1rem;
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
                    <a href="instructor-messages.php">Messages</a>
                    
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

    <!-- Main Content -->
    <div class="main-content">
        <!-- Conversations Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Messages</h2>
                <p class="sidebar-subtitle">Your conversations with students</p>
            </div>
            
            <div class="conversation-list" id="conversationList">
                <div class="loading">
                    <div class="spinner"></div>
                    Loading conversations...
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div id="chatContent">
                <div class="empty-conversation">
                    <i class="fas fa-comments"></i>
                    <h3>Select a conversation</h3>
                    <p>Choose a conversation from the sidebar to start messaging</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        class InstructorMessaging {
            constructor() {
                this.currentConversation = null;
                this.conversations = [];
                this.messages = [];
                
                // Check for pre-selected student from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const preSelectedStudentId = urlParams.get('student_id');
                const preSelectedStudentName = urlParams.get('student_name');
                const preSelectedCourseId = urlParams.get('course_id');
                
                this.loadConversations().then(() => {
                    // If there's a pre-selected student, start conversation with them
                    if (preSelectedStudentId) {
                        this.currentConversation = { userId: preSelectedStudentId, courseId: preSelectedCourseId };
                        this.loadMessages(preSelectedStudentId, preSelectedCourseId);
                    }
                });
            }

            async loadConversations() {
                try {
                    const response = await fetch('/Creators-Space-GroupProject/backend/communication/get_conversations.php');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.conversations = data.data.conversations;
                        this.renderConversations();
                    } else {
                        console.error('Failed to load conversations:', data.message);
                        this.showError('Failed to load conversations');
                    }
                } catch (error) {
                    console.error('Error loading conversations:', error);
                    this.showError('Network error while loading conversations');
                }
            }

            renderConversations() {
                const container = document.getElementById('conversationList');
                
                if (this.conversations.length === 0) {
                    container.innerHTML = `
                        <div class="empty-conversation">
                            <i class="fas fa-inbox"></i>
                            <h4>No conversations yet</h4>
                            <p>Your student messages will appear here</p>
                        </div>
                    `;
                    return;
                }

                const conversationsHTML = this.conversations.map(conv => `
                    <div class="conversation-item" 
                         data-conversation-id="${conv.conversation_id}" 
                         data-user-id="${conv.other_user.id}"
                         data-course-id="${conv.course ? conv.course.id : ''}">
                        <div class="conversation-avatar">
                            ${conv.other_user.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-name">
                                ${conv.other_user.name}
                                <span class="course-badge" style="margin-left: 0.5rem;">
                                    ${conv.other_user.role === 'user' ? 'Student' : conv.other_user.role}
                                </span>
                            </div>
                            ${conv.course ? `<div class="course-badge">${conv.course.title}</div>` : ''}
                            <div class="conversation-last-message">
                                ${conv.last_message.is_from_me ? 'You: ' : ''}${conv.last_message.content || 'No messages yet'}
                            </div>
                        </div>
                        <div class="conversation-meta">
                            <div class="conversation-time">
                                ${this.formatTime(conv.last_message.created_at)}
                            </div>
                            ${conv.unread_count > 0 ? `<div class="unread-badge">${conv.unread_count}</div>` : ''}
                        </div>
                    </div>
                `).join('');

                container.innerHTML = conversationsHTML;

                // Add click handlers
                document.querySelectorAll('.conversation-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const conversationId = item.dataset.conversationId;
                        const userId = item.dataset.userId;
                        const courseId = item.dataset.courseId || null;
                        this.selectConversation(conversationId, userId, courseId);
                    });
                });
            }

            async selectConversation(conversationId, userId, courseId = null) {
                // Update active conversation
                document.querySelectorAll('.conversation-item').forEach(item => {
                    item.classList.remove('active');
                });
                document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');

                this.currentConversation = { id: conversationId, userId: userId, courseId: courseId };
                
                // Load messages with course context
                await this.loadMessages(userId, courseId);
            }

            async loadMessages(otherUserId, courseId = null) {
                try {
                    console.log('=== loadMessages DEBUG ===');
                    console.log('Current session user ID:', '<?php echo $_SESSION["user_id"] ?? "NOT SET"; ?>');
                    console.log('Loading messages for OTHER user ID:', otherUserId);
                    console.log('Course ID:', courseId);
                    console.log('Type of otherUserId:', typeof otherUserId);
                    
                    let url = `/Creators-Space-GroupProject/backend/communication/get_messages.php?other_user_id=${otherUserId}`;
                    if (courseId) url += `&course_id=${courseId}`;
                    
                    console.log('Fetching from URL:', url);

                    const response = await fetch(url);
                    console.log('Response status:', response.status);
                    console.log('Response ok:', response.ok);
                    
                    const data = await response.json();
                    
                    console.log('Full API Response:', data);
                    
                    if (data.success) {
                        this.messages = data.data.messages;
                        console.log('Messages loaded:', this.messages.length, 'messages');
                        console.log('Messages array:', this.messages);
                        this.renderMessages(data.data.other_user);
                        
                        // Set current conversation if not already set
                        if (!this.currentConversation || this.currentConversation.userId !== otherUserId) {
                            this.currentConversation = { userId: otherUserId, courseId: courseId };
                        }
                    } else {
                        console.error('Failed to load messages:', data.message);
                        this.showError('Failed to load messages: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Error loading messages:', error);
                    this.showError('Network error while loading messages: ' + error.message);
                }
            }

            renderMessages(otherUser) {
                console.log('renderMessages called with:', otherUser);
                console.log('this.messages:', this.messages);
                console.log('messages length:', this.messages.length);
                
                const chatContent = document.getElementById('chatContent');
                
                const chatHTML = `
                    <div class="chat-header">
                        <div class="chat-user-info">
                            <div class="chat-user-avatar">
                                ${otherUser.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="chat-user-details">
                                <h3>${otherUser.name}</h3>
                                <p>${otherUser.role === 'user' ? 'Student' : otherUser.role}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="messages-container" id="messagesContainer">
                        ${this.messages.length === 0 ? `
                            <div class="empty-messages">
                                <i class="fas fa-comment-dots"></i>
                                <h4>No messages yet</h4>
                                <p>Start a conversation with ${otherUser.name}</p>
                            </div>
                        ` : this.messages.map(msg => {
                            console.log('Rendering message:', msg);
                            return this.renderMessage(msg);
                        }).join('')}
                    </div>
                    
                    <div class="message-input-container">
                        <form class="message-input-form" onsubmit="messaging.sendMessage(event)">
                            <textarea 
                                class="message-input" 
                                id="messageInput"
                                placeholder="Type your message..."
                                rows="1"
                                required
                            ></textarea>
                            <button type="submit" class="send-button" id="sendButton">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                `;

                chatContent.innerHTML = chatHTML;
                
                // Auto-resize textarea
                const textarea = document.getElementById('messageInput');
                textarea.addEventListener('input', () => {
                    textarea.style.height = 'auto';
                    textarea.style.height = textarea.scrollHeight + 'px';
                });

                // Scroll to bottom
                this.scrollToBottom();
            }

            renderMessage(message) {
                return `
                    <div class="message ${message.is_from_me ? 'sent' : 'received'}">
                        <div class="message-bubble">
                            ${message.subject ? `<div class="message-subject">${message.subject}</div>` : ''}
                            <div class="message-content">${this.escapeHtml(message.message)}</div>
                            <div class="message-time">
                                ${this.formatTime(message.created_at)}
                                ${message.is_from_me && message.is_read ? '<i class="fas fa-check-double" style="margin-left: 0.5rem; opacity: 0.7;"></i>' : ''}
                            </div>
                        </div>
                    </div>
                `;
            }

            async sendMessage(event) {
                event.preventDefault();
                
                if (!this.currentConversation) {
                    this.showError('Please select a conversation first');
                    return;
                }

                const messageInput = document.getElementById('messageInput');
                const sendButton = document.getElementById('sendButton');
                const message = messageInput.value.trim();

                if (!message) return;

                // Disable form
                messageInput.disabled = true;
                sendButton.disabled = true;
                sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                try {
                    const formData = new FormData();
                    formData.append('receiver_id', this.currentConversation.userId);
                    formData.append('message', message);

                    const response = await fetch('/Creators-Space-GroupProject/backend/communication/send_message.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Clear input
                        messageInput.value = '';
                        messageInput.style.height = 'auto';
                        
                        // Reload messages
                        await this.loadMessages(this.currentConversation.userId);
                        
                        // Update conversations list
                        await this.loadConversations();
                    } else {
                        this.showError(data.message || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    this.showError('Network error while sending message');
                } finally {
                    // Re-enable form
                    messageInput.disabled = false;
                    sendButton.disabled = false;
                    sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                    messageInput.focus();
                }
            }

            formatTime(timestamp) {
                if (!timestamp) return '';
                const date = new Date(timestamp);
                const now = new Date();
                const diffTime = now - date;
                const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                if (diffDays === 0) {
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                } else if (diffDays === 1) {
                    return 'Yesterday';
                } else if (diffDays < 7) {
                    return date.toLocaleDateString([], { weekday: 'short' });
                } else {
                    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
                }
            }

            escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, m => map[m]);
            }

            scrollToBottom() {
                setTimeout(() => {
                    const container = document.getElementById('messagesContainer');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            }

            showError(message) {
                console.error(message);
                // You could implement a toast notification system here
                alert(message);
            }
        }

        // Initialize messaging system
        const messaging = new InstructorMessaging();

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
    </script>
</body>
</html>