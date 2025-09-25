<?php
/**
 * AI Agent Message Processor
 * Handles incoming chat messages and provides intelligent responses
 */

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/ai_knowledge_base.php';

// Set JSON response headers
header('Content-Type: application/json');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['message'])) {
        throw new Exception('Message is required');
    }
    
    $message = trim($input['message']);
    $userId = $input['user_id'] ?? null;
    $context = $input['context'] ?? [];
    
    if (empty($message)) {
        throw new Exception('Message cannot be empty');
    }
    
    // Initialize AI processor
    $aiProcessor = new AIMessageProcessor($pdo, $userId);
    
    // Process the message and get response
    $response = $aiProcessor->processMessage($message, $context);
    
    // Save conversation to database
    if ($userId) {
        $aiProcessor->saveConversation($message, $response['response'], $context);
    }
    
    // Return response
    echo json_encode([
        'success' => true,
        'response' => $response['response'],
        'message_type' => $response['message_type'] ?? 'normal',
        'additional_data' => $response['additional_data'] ?? null,
        'timestamp' => date('c')
    ]);

} catch (Exception $e) {
    error_log("AI Agent Error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Sorry, I encountered an error processing your message. Please try again.',
        'timestamp' => date('c')
    ]);
}

/**
 * Main AI Message Processor Class
 */
class AIMessageProcessor {
    private $db;
    private $userId;
    private $knowledgeBase;
    
    public function __construct($pdo, $userId = null) {
        $this->db = $pdo;
        $this->userId = $userId;
        $this->knowledgeBase = new AIKnowledgeBase($pdo);
    }
    
    /**
     * Process incoming message and generate response
     */
    public function processMessage($message, $context = []) {
        $message = strtolower($message);
        
        // Detect intent and generate appropriate response
        $intent = $this->detectIntent($message);
        
        switch ($intent) {
            case 'course_recommendation':
                return $this->handleCourseRecommendations($message);
                
            case 'learning_path':
                return $this->handleLearningPath($message);
                
            case 'project_help':
                return $this->handleProjectHelp($message);
                
            case 'faq':
                return $this->handleFAQ($message);
                
            case 'greeting':
                return $this->handleGreeting();
                
            case 'platform_info':
                return $this->handlePlatformInfo($message);
                
            case 'enrollment_help':
                return $this->handleEnrollmentHelp($message);
                
            default:
                return $this->handleGeneralQuery($message);
        }
    }
    
    /**
     * Detect user intent from message
     */
    private function detectIntent($message) {
        $patterns = [
            'course_recommendation' => ['recommend', 'course', 'suggest', 'learn', 'study', 'what should i', 'best course'],
            'learning_path' => ['path', 'roadmap', 'journey', 'sequence', 'order', 'step by step'],
            'project_help' => ['project', 'assignment', 'help with', 'stuck', 'problem', 'debug'],
            'faq' => ['faq', 'frequently asked', 'common question', 'help', 'how to'],
            'greeting' => ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening'],
            'platform_info' => ['platform', 'website', 'features', 'about', 'creators space'],
            'enrollment_help' => ['enroll', 'register', 'sign up', 'join course', 'how to start']
        ];
        
        foreach ($patterns as $intent => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    return $intent;
                }
            }
        }
        
        return 'general';
    }
    
    /**
     * Handle course recommendations
     */
    private function handleCourseRecommendations($message) {
        $userPreferences = $this->getUserPreferences();
        
        // Extract skill level and interests from message
        $skillLevel = $this->extractSkillLevel($message);
        $interests = $this->extractInterests($message);
        
        // Get recommended courses
        $courses = $this->knowledgeBase->getRecommendedCourses($skillLevel, $interests, $userPreferences);
        
        $response = "Based on your interests, I'd recommend these courses that could help advance your learning journey:";
        
        if (empty($courses)) {
            $response = "I'd love to recommend some courses for you! Could you tell me more about what you'd like to learn? For example, are you interested in web development, data science, mobile apps, or something else?";
        }
        
        return [
            'response' => $response,
            'message_type' => 'suggestion',
            'additional_data' => ['courses' => $courses]
        ];
    }
    
    /**
     * Handle learning path requests
     */
    private function handleLearningPath($message) {
        $interests = $this->extractInterests($message);
        $skillLevel = $this->extractSkillLevel($message);
        
        $learningPath = $this->knowledgeBase->generateLearningPath($interests, $skillLevel);
        
        $response = "Here's a personalized learning path I've created for you:";
        
        if (empty($learningPath['steps'])) {
            $response = "I'd be happy to create a learning path for you! What area would you like to focus on? For example: web development, data science, mobile development, or UI/UX design?";
        }
        
        return [
            'response' => $response,
            'message_type' => 'suggestion',
            'additional_data' => ['learning_path' => $learningPath]
        ];
    }
    
    /**
     * Handle project help requests
     */
    private function handleProjectHelp($message) {
        $projectType = $this->extractProjectType($message);
        $resources = $this->knowledgeBase->getProjectResources($projectType);
        
        $response = "I'm here to help with your project! Here are some resources and tips that might be useful:";
        
        return [
            'response' => $response,
            'message_type' => 'success',
            'additional_data' => ['project_resources' => $resources]
        ];
    }
    
    /**
     * Handle FAQ requests
     */
    private function handleFAQ($message) {
        $faqs = $this->knowledgeBase->getFAQs();
        
        // Try to match specific FAQ question
        $matchedFAQ = $this->findMatchingFAQ($message, $faqs);
        
        if ($matchedFAQ) {
            return [
                'response' => $matchedFAQ['answer'],
                'message_type' => 'normal'
            ];
        }
        
        // Return general FAQ help
        $response = "Here are some frequently asked questions I can help you with:\\n\\n";
        $response .= "• How do I enroll in a course?\\n";
        $response .= "• What payment methods are accepted?\\n";
        $response .= "• How do I track my progress?\\n";
        $response .= "• Can I get a certificate?\\n";
        $response .= "• How do I contact support?\\n\\n";
        $response .= "Feel free to ask me about any of these topics!";
        
        return [
            'response' => $response,
            'message_type' => 'normal'
        ];
    }
    
    /**
     * Handle greeting messages
     */
    private function handleGreeting() {
        $userName = $this->getUserName();
        $greetings = [
            "Hello" . ($userName ? " $userName" : "") . "! 👋 I'm your learning assistant. How can I help you today?",
            "Hi there" . ($userName ? " $userName" : "") . "! 😊 Ready to explore some amazing courses?",
            "Welcome" . ($userName ? " back $userName" : "") . "! 🌟 What would you like to learn about today?"
        ];
        
        return [
            'response' => $greetings[array_rand($greetings)],
            'message_type' => 'normal'
        ];
    }
    
    /**
     * Handle platform information requests
     */
    private function handlePlatformInfo($message) {
        $response = "**Welcome to Creators-Space!** 🚀\\n\\n";
        $response .= "We're a comprehensive e-learning platform offering:\\n\\n";
        $response .= "📚 **Diverse Courses** - Web Development, Data Science, Mobile Apps, UI/UX Design\\n";
        $response .= "🎓 **Expert Instructors** - Learn from industry professionals\\n";
        $response .= "💼 **Internship Opportunities** - Gain real-world experience\\n";
        $response .= "🏆 **Certificates** - Earn recognized certificates upon completion\\n";
        $response .= "📱 **Mobile-Friendly** - Learn anywhere, anytime\\n\\n";
        $response .= "Ready to start your learning journey?";
        
        return [
            'response' => $response,
            'message_type' => 'normal'
        ];
    }
    
    /**
     * Handle enrollment help
     */
    private function handleEnrollmentHelp($message) {
        $response = "**How to Enroll in Courses:** 📝\\n\\n";
        $response .= "1. **Browse Courses** - Visit our courses page to explore available options\\n";
        $response .= "2. **Create Account** - Sign up with your email if you haven't already\\n";
        $response .= "3. **Select Course** - Click on any course that interests you\\n";
        $response .= "4. **Enroll** - Click the 'Enroll Now' button\\n";
        $response .= "5. **Start Learning** - Access your courses from 'My Courses'\\n\\n";
        $response .= "Need help with a specific course? Just let me know!";
        
        return [
            'response' => $response,
            'message_type' => 'success'
        ];
    }
    
    /**
     * Handle general queries
     */
    private function handleGeneralQuery($message) {
        $responses = [
            "That's a great question! While I'm still learning, I can help you with course recommendations, learning paths, project assistance, and platform information. What would you like to explore?",
            "I'd love to help you with that! I specialize in helping with courses, learning guidance, and platform support. Is there a specific area you'd like assistance with?",
            "Thanks for asking! I'm here to make your learning journey easier. I can recommend courses, suggest learning paths, help with projects, or answer questions about our platform. How can I assist you today?"
        ];
        
        return [
            'response' => $responses[array_rand($responses)],
            'message_type' => 'normal'
        ];
    }
    
    /**
     * Save conversation to database
     */
    public function saveConversation($userMessage, $botResponse, $context = []) {
        if (!$this->userId) return;
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ai_conversations (user_id, user_message, bot_response, context, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $this->userId,
                $userMessage,
                $botResponse,
                json_encode($context)
            ]);
            
        } catch (PDOException $e) {
            error_log("Error saving AI conversation: " . $e->getMessage());
        }
    }
    
    /**
     * Get user preferences from database
     */
    private function getUserPreferences() {
        if (!$this->userId) return [];
        
        try {
            $stmt = $this->db->prepare("SELECT skills FROM users WHERE id = ?");
            $stmt->execute([$this->userId]);
            $user = $stmt->fetch();
            
            return $user ? ['skills' => $user['skills']] : [];
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get user name for personalization
     */
    private function getUserName() {
        if (!$this->userId) return null;
        
        try {
            $stmt = $this->db->prepare("SELECT first_name FROM users WHERE id = ?");
            $stmt->execute([$this->userId]);
            $user = $stmt->fetch();
            
            return $user ? $user['first_name'] : null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Extract skill level from message
     */
    private function extractSkillLevel($message) {
        if (strpos($message, 'beginner') !== false || strpos($message, 'new') !== false || strpos($message, 'start') !== false) {
            return 'beginner';
        } elseif (strpos($message, 'intermediate') !== false || strpos($message, 'some experience') !== false) {
            return 'intermediate';
        } elseif (strpos($message, 'advanced') !== false || strpos($message, 'expert') !== false) {
            return 'advanced';
        }
        
        return 'beginner'; // default
    }
    
    /**
     * Extract interests/topics from message
     */
    private function extractInterests($message) {
        $interests = [];
        $topics = [
            'web development' => ['web', 'html', 'css', 'javascript', 'frontend', 'backend'],
            'data science' => ['data', 'python', 'machine learning', 'ai', 'analytics'],
            'mobile development' => ['mobile', 'app', 'android', 'ios', 'react native'],
            'ui/ux design' => ['design', 'ui', 'ux', 'user interface', 'user experience']
        ];
        
        foreach ($topics as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $interests[] = $category;
                    break;
                }
            }
        }
        
        return array_unique($interests);
    }
    
    /**
     * Extract project type from message
     */
    private function extractProjectType($message) {
        if (strpos($message, 'web') !== false) return 'web';
        if (strpos($message, 'mobile') !== false || strpos($message, 'app') !== false) return 'mobile';
        if (strpos($message, 'data') !== false) return 'data';
        if (strpos($message, 'design') !== false) return 'design';
        
        return 'general';
    }
    
    /**
     * Find matching FAQ
     */
    private function findMatchingFAQ($message, $faqs) {
        foreach ($faqs as $faq) {
            $keywords = explode(' ', strtolower($faq['question']));
            $matches = 0;
            
            foreach ($keywords as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    $matches++;
                }
            }
            
            if ($matches >= 2) { // Match if at least 2 keywords match
                return $faq;
            }
        }
        
        return null;
    }
}
?>