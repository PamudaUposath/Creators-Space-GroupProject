<?php
// frontend/student-messages.php
session_start();

// Check if user is logged in as student or admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'admin')) {
    header('Location: login.php');
    exit;
}

require_once '../backend/config/db_connect.php';

$student_id = $_SESSION['user_id'];
$page_title = 'Messages';
$page_description = 'Communicate with your instructors and get course support';
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

        /* Navigation Bar */
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .navbar h1 a {
            color: #fff;
            text-decoration: none;
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
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        #userSection span {
            color: #ffffff;
            font-weight: 500;
            margin-right: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            border: 1px solid transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-logout {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #ffffff;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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

        .instructor-search {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.8);
        }

        .search-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
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

        .instructor-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .instructor-item:hover {
            background: rgba(102, 126, 234, 0.05);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .conversation-avatar, .instructor-avatar {
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

        .conversation-info, .instructor-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name, .instructor-name {
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

        .instructor-role {
            color: #64748b;
            font-size: 0.85rem;
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
            color: #64748b;
            text-align: center;
            padding: 2rem;
        }

        .empty-conversation i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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

        /* Tab System */
        .tab-container {
            display: flex;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            background: rgba(255, 255, 255, 0.8);
        }

        .tab-button {
            flex: 1;
            padding: 1rem;
            border: none;
            background: transparent;
            color: #64748b;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .tab-button.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title">Messages</h2>
                <p class="sidebar-subtitle">Connect with your instructors</p>
            </div>
            
            <!-- Tabs -->
            <div class="tab-container">
                <button class="tab-button active" onclick="messaging.switchTab('conversations')">
                    Conversations
                </button>
            </div>
            
            <!-- Conversations Tab -->
            <div id="conversationsTab" class="tab-content active">
                <div class="conversation-list" id="conversationList">
                    <div class="loading">
                        <div class="spinner"></div>
                        Loading conversations...
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div id="chatContent">
                <div class="empty-conversation">
                    <i class="fas fa-comments"></i>
                    <h3>Select a conversation</h3>
                    <p>Choose an instructor to start messaging or browse your conversations</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        class StudentMessaging {
            constructor() {
                this.currentConversation = null;
                this.conversations = [];
                this.messages = [];
                this.currentTab = 'conversations';
                
                // Check for pre-selected instructor from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const preSelectedInstructorId = urlParams.get('instructor_id');
                const preSelectedInstructorName = urlParams.get('instructor_name');
                const preSelectedCourseId = urlParams.get('course_id');
                
                this.loadConversations().then(() => {
                    // If there's a pre-selected instructor, start conversation with them
                    if (preSelectedInstructorId) {
                        this.currentConversation = { userId: preSelectedInstructorId, courseId: preSelectedCourseId };
                        this.loadMessages(preSelectedInstructorId, preSelectedCourseId);
                    }
                });
                
                this.setupSearch();
            }

            switchTab(tab) {
                // Update tab buttons
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelector(`button[onclick="messaging.switchTab('${tab}')"]`).classList.add('active');
                
                // Update tab content
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                document.getElementById(tab + 'Tab').classList.add('active');
                
                this.currentTab = tab;
            }

            setupSearch() {
                // No search functionality needed since instructors tab is removed
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
                            <p>Start a conversation with an instructor</p>
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
                                    ${conv.other_user.role === 'instructor' ? 'Instructor' : conv.other_user.role}
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



            async startConversationWithInstructor(instructorId) {
                // Switch to conversations tab and load messages with this instructor
                this.switchTab('conversations');
                await this.loadMessages(instructorId);
            }

            async selectConversation(conversationId, userId, courseId = null) {
                // Update active conversation
                document.querySelectorAll('.conversation-item').forEach(item => {
                    item.classList.remove('active');
                });
                const conversationElement = document.querySelector(`[data-conversation-id="${conversationId}"]`);
                if (conversationElement) {
                    conversationElement.classList.add('active');
                }

                this.currentConversation = { id: conversationId, userId: userId, courseId: courseId };
                
                // Load messages with course context
                await this.loadMessages(userId, courseId);
            }

            async loadMessages(otherUserId, courseId = null) {
                try {
                    console.log('Student loading messages for user:', otherUserId, 'course:', courseId);
                    let url = `/Creators-Space-GroupProject/backend/communication/get_messages.php?other_user_id=${otherUserId}`;
                    if (courseId) url += `&course_id=${courseId}`;
                    
                    console.log('Student fetching from URL:', url);

                    const response = await fetch(url);
                    const data = await response.json();
                    
                    console.log('Student API Response:', data);
                    
                    if (data.success) {
                        this.messages = data.data.messages;
                        console.log('Student messages loaded:', this.messages.length, 'messages');
                        this.renderMessages(data.data.other_user);
                        
                        // Set current conversation if not already set
                        if (!this.currentConversation || this.currentConversation.userId !== otherUserId) {
                            this.currentConversation = { userId: otherUserId, courseId: courseId };
                        }
                    } else {
                        console.error('Student failed to load messages:', data.message);
                        this.showError('Failed to load messages: ' + (data.message || 'Unknown error'));
                    }
                } catch (error) {
                    console.error('Student error loading messages:', error);
                    this.showError('Network error while loading messages: ' + error.message);
                }
            }

            renderMessages(otherUser) {
                const chatContent = document.getElementById('chatContent');
                
                const chatHTML = `
                    <div class="chat-header">
                        <div class="chat-user-info">
                            <div class="chat-user-avatar">
                                ${otherUser.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="chat-user-details">
                                <h3>${otherUser.name}</h3>
                                <p>${otherUser.role === 'instructor' ? 'Instructor' : otherUser.role}</p>
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
                        ` : this.messages.map(msg => this.renderMessage(msg)).join('')}
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
        const messaging = new StudentMessaging();
    </script>
</body>
</html>