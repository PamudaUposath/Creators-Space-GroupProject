/**
 * AI Learning Assistant for Creators-Space
 * Provides intelligent course recommendations, learning assistance, and interactive support
 */

class AILearningAssistant {
    constructor() {
        console.log('AI Agent: Constructor called');
        
        this.isOpen = false;
        this.messages = [];
        this.isTyping = false;
        this.userId = null;
        this.userName = null;
        
        // Initialize user info from session if available
        this.initializeUser();
        
        // Initialize the chat interface
        this.init();
        
        // Bind methods
        this.handleSendMessage = this.handleSendMessage.bind(this);
        this.handleQuickAction = this.handleQuickAction.bind(this);
        
        console.log('AI Agent: Constructor completed successfully');
    }
    
    initializeUser() {
        // Try to get user info from session storage or make an API call
        const userData = localStorage.getItem('loggedInUser');
        if (userData) {
            try {
                const user = JSON.parse(userData);
                this.userId = user.id;
                this.userName = user.first_name || user.username || 'there';
            } catch (e) {
                console.log('Could not parse user data');
            }
        }
    }
    
    init() {
        this.createChatInterface();
        this.attachEventListeners();
        this.loadWelcomeMessage();
    }
    
    createChatInterface() {
        const chatHTML = `
            <!-- AI Chat Button -->
            <button class="ai-chat-button" id="aiChatToggle">
                <i class="fas fa-robot"></i>
            </button>
            
            <!-- AI Chat Window -->
            <div class="ai-chat-window" id="aiChatWindow">
                <div class="ai-chat-header">
                    <div class="ai-agent-info">
                        <div class="ai-agent-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="ai-agent-details">
                            <h4>Learning Assistant</h4>
                            <span>🟢 Online</span>
                        </div>
                    </div>
                    <button class="ai-chat-close" id="aiChatClose">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="ai-chat-messages" id="aiChatMessages">
                    <div class="ai-welcome-message">
                        <h3>👋 Welcome to your Learning Assistant!</h3>
                        <p>I'm here to help you with courses, projects, and learning guidance. What would you like to know?</p>
                    </div>
                </div>
                
                <div class="ai-quick-actions">
                    <div class="quick-actions-title">Quick Actions</div>
                    <div class="quick-actions-grid">
                        <button class="quick-action-btn" data-action="recommend-courses">📚 Course Recommendations</button>
                        <button class="quick-action-btn" data-action="learning-path">🗺️ Learning Path</button>
                        <button class="quick-action-btn" data-action="help-with-project">🛠️ Project Help</button>
                        <button class="quick-action-btn" data-action="faq">❓ FAQ</button>
                    </div>
                </div>
                
                <div class="ai-chat-input-container">
                    <textarea 
                        class="ai-chat-input" 
                        id="aiChatInput" 
                        placeholder="Ask me anything about learning..."
                        rows="1"
                    ></textarea>
                    <button class="ai-send-button" id="aiSendButton">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', chatHTML);
    }
    
    attachEventListeners() {
        // Toggle chat window
        document.getElementById('aiChatToggle').addEventListener('click', () => {
            this.toggleChat();
        });
        
        // Close chat window
        document.getElementById('aiChatClose').addEventListener('click', () => {
            this.closeChat();
        });
        
        // Send message
        document.getElementById('aiSendButton').addEventListener('click', this.handleSendMessage);
        
        // Handle Enter key in input
        document.getElementById('aiChatInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.handleSendMessage();
            }
        });
        
        // Auto-resize textarea
        document.getElementById('aiChatInput').addEventListener('input', (e) => {
            e.target.style.height = 'auto';
            e.target.style.height = Math.min(e.target.scrollHeight, 80) + 'px';
        });
        
        // Quick action buttons
        document.querySelectorAll('.quick-action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.handleQuickAction(e.target.dataset.action);
            });
        });
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (this.isOpen && 
                !document.getElementById('aiChatWindow').contains(e.target) && 
                !document.getElementById('aiChatToggle').contains(e.target)) {
                this.closeChat();
            }
        });
    }
    
    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }
    
    openChat() {
        const chatWindow = document.getElementById('aiChatWindow');
        chatWindow.classList.add('show');
        this.isOpen = true;
        
        // Focus input
        setTimeout(() => {
            document.getElementById('aiChatInput').focus();
        }, 300);
    }
    
    closeChat() {
        const chatWindow = document.getElementById('aiChatWindow');
        chatWindow.classList.remove('show');
        this.isOpen = false;
    }
    
    loadWelcomeMessage() {
        // Show personalized welcome if user is logged in
        if (this.userName && this.userName !== 'there') {
            setTimeout(() => {
                this.addMessage(`Hello ${this.userName}! 👋 I'm your personal learning assistant. I can help you find the perfect courses, create learning paths, assist with projects, and answer questions about the platform. What would you like to explore today?`, 'bot');
            }, 1000);
        }
    }
    
    handleSendMessage() {
        const input = document.getElementById('aiChatInput');
        const message = input.value.trim();
        
        if (!message || this.isTyping) return;
        
        // Add user message
        this.addMessage(message, 'user');
        
        // Clear input
        input.value = '';
        input.style.height = 'auto';
        
        // Process message
        this.processMessage(message);
    }
    
    handleQuickAction(action) {
        const actions = {
            'recommend-courses': 'Can you recommend some courses for me?',
            'learning-path': 'What would be a good learning path for me?',
            'help-with-project': 'I need help with a project',
            'faq': 'Show me frequently asked questions'
        };
        
        if (actions[action]) {
            // Add the question as a user message
            this.addMessage(actions[action], 'user');
            this.processMessage(actions[action]);
        }
    }
    
    async processMessage(message) {
        // Show typing indicator
        this.showTyping();
        
        try {
            // Send message to backend
            const response = await fetch(window.apiUrl('/backend/ai-agent/process_message.php'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    user_id: this.userId,
                    context: {
                        current_page: window.location.pathname,
                        timestamp: new Date().toISOString()
                    }
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Hide typing and show response
                this.hideTyping();
                
                // Add bot response with appropriate styling
                const messageType = data.message_type || 'normal';
                this.addMessage(data.response, 'bot', messageType);
                
                // Handle any additional data (course recommendations, etc.)
                if (data.additional_data) {
                    this.handleAdditionalData(data.additional_data);
                }
                
            } else {
                this.hideTyping();
                this.addMessage('Sorry, I encountered an error. Please try again.', 'bot', 'warning');
            }
            
        } catch (error) {
            console.error('AI Agent Error:', error);
            this.hideTyping();
            this.addMessage("I'm having trouble connecting right now. Please try again in a moment.", 'bot', 'warning');
        }
    }
    
    addMessage(text, sender, type = 'normal') {
        const messagesContainer = document.getElementById('aiChatMessages');
        const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        const messageClass = type !== 'normal' ? `message-bubble ${type}` : 'message-bubble';
        
        const messageHTML = `
            <div class="ai-message ${sender}">
                <div class="${messageClass}">
                    ${this.formatMessage(text)}
                    <div class="message-time">${time}</div>
                </div>
            </div>
        `;
        
        // Remove welcome message if it exists
        const welcomeMessage = messagesContainer.querySelector('.ai-welcome-message');
        if (welcomeMessage && sender === 'user') {
            welcomeMessage.remove();
        }
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        this.scrollToBottom();
        
        // Store message
        this.messages.push({
            text: text,
            sender: sender,
            type: type,
            timestamp: Date.now()
        });
    }
    
    formatMessage(text) {
        // Convert markdown-style formatting
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')  // Bold
            .replace(/\*(.*?)\*/g, '<em>$1</em>')                // Italic
            .replace(/`(.*?)`/g, '<code>$1</code>')               // Code
            .replace(/\n/g, '<br>')                              // Line breaks
            .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>'); // Links
    }
    
    showTyping() {
        if (this.isTyping) return;
        
        this.isTyping = true;
        const messagesContainer = document.getElementById('aiChatMessages');
        
        const typingHTML = `
            <div class="ai-typing show">
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', typingHTML);
        this.scrollToBottom();
    }
    
    hideTyping() {
        this.isTyping = false;
        const typingIndicator = document.querySelector('.ai-typing');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    scrollToBottom() {
        const messagesContainer = document.getElementById('aiChatMessages');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    handleAdditionalData(data) {
        // Handle course recommendations
        if (data.courses) {
            const coursesHTML = this.generateCoursesHTML(data.courses);
            this.addMessage(coursesHTML, 'bot', 'suggestion');
        }
        
        // Handle learning paths
        if (data.learning_path) {
            const pathHTML = this.generateLearningPathHTML(data.learning_path);
            this.addMessage(pathHTML, 'bot', 'suggestion');
        }
        
        // Handle project resources
        if (data.project_resources) {
            const resourcesHTML = this.generateResourcesHTML(data.project_resources);
            this.addMessage(resourcesHTML, 'bot', 'success');
        }
    }
    
    generateCoursesHTML(courses) {
        let html = '<strong>📚 Recommended Courses:</strong><br><br>';
        courses.forEach(course => {
            html += `
                <div style="margin: 8px 0; padding: 8px; border-left: 3px solid #667eea;">
                    <strong>${course.title}</strong><br>
                    <small style="opacity: 0.8;">${course.level} • ${course.duration}</small><br>
                    <em>${course.description?.substring(0, 80)}...</em>
                </div>
            `;
        });
        return html;
    }
    
    generateLearningPathHTML(path) {
        let html = '<strong>🗺️ Your Learning Path:</strong><br><br>';
        path.steps?.forEach((step, index) => {
            html += `
                <div style="margin: 6px 0; padding: 6px; background: rgba(255,255,255,0.1); border-radius: 6px;">
                    <strong>Step ${index + 1}:</strong> ${step.title}<br>
                    <small style="opacity: 0.8;">${step.duration} • ${step.description}</small>
                </div>
            `;
        });
        return html;
    }
    
    generateResourcesHTML(resources) {
        let html = '<strong>🛠️ Project Resources:</strong><br><br>';
        resources.forEach(resource => {
            html += `
                <div style="margin: 6px 0;">
                    <strong>${resource.title}:</strong><br>
                    <small style="opacity: 0.8;">${resource.description}</small>
                </div>
            `;
        });
        return html;
    }
}

// Initialize AI Learning Assistant when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Check if we should load the AI agent (you can add conditions here)
    console.log('AI Agent: DOMContentLoaded - Initializing...', window.location.pathname);
    
    try {
        // Initialize AI agent on most pages (exclude login/signup)
        const currentPath = window.location.pathname;
        const shouldLoadAgent = !currentPath.includes('login.php') && !currentPath.includes('signup.php');
        
        if (shouldLoadAgent) {
            console.log('AI Agent: Conditions met, creating AILearningAssistant...');
            window.aiAssistant = new AILearningAssistant();
            console.log('AI Agent: Successfully initialized!');
        } else {
            console.log('AI Agent: Skipped on login/signup page');
        }
    } catch (error) {
        console.error('AI Agent: Error during initialization:', error);
    }
});

// Utility functions for AI agent features
window.AIAgentUtils = {
    // Get current user's learning preferences
    getUserPreferences: async () => {
        try {
            const response = await fetch(window.apiUrl('/backend/ai-agent/get_user_preferences.php'));
            return await response.json();
        } catch (error) {
            console.error('Error fetching user preferences:', error);
            return null;
        }
    },
    
    // Get course catalog for recommendations
    getCourses: async (filters = {}) => {
        try {
            const queryParams = new URLSearchParams(filters).toString();
            const response = await fetch(window.apiUrl(`/backend/ai-agent/get_courses.php?${queryParams}`));
            return await response.json();
        } catch (error) {
            console.error('Error fetching courses:', error);
            return null;
        }
    },
    
    // Save conversation
    saveConversation: async (messages) => {
        try {
            const response = await fetch(window.apiUrl('/backend/ai-agent/save_conversation.php'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ messages })
            });
            return await response.json();
        } catch (error) {
            console.error('Error saving conversation:', error);
            return null;
        }
    }
};