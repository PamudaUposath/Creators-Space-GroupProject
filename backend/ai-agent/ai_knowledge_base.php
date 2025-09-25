<?php
/**
 * AI Knowledge Base
 * Contains course data, FAQs, and learning resources for the AI agent
 */

class AIKnowledgeBase {
    private $db;
    
    public function __construct($pdo) {
        $this->db = $pdo;
    }
    
    /**
     * Get recommended courses based on user preferences
     */
    public function getRecommendedCourses($skillLevel = 'beginner', $interests = [], $userPreferences = []) {
        try {
            $sql = "SELECT id, title, description, level, duration, category, price, image_url 
                   FROM courses 
                   WHERE is_active = 1";
            
            $params = [];
            
            // Filter by skill level
            if ($skillLevel && $skillLevel !== 'all') {
                $sql .= " AND level = ?";
                $params[] = $skillLevel;
            }
            
            // Filter by interests/categories
            if (!empty($interests)) {
                $placeholders = str_repeat('?,', count($interests) - 1) . '?';
                $sql .= " AND category IN ($placeholders)";
                $params = array_merge($params, $interests);
            }
            
            $sql .= " ORDER BY featured DESC, created_at DESC LIMIT 5";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error fetching recommended courses: " . $e->getMessage());
            return $this->getDefaultCourseRecommendations();
        }
    }
    
    /**
     * Generate a learning path based on user interests
     */
    public function generateLearningPath($interests = [], $skillLevel = 'beginner') {
        $paths = [
            'web development' => [
                'beginner' => [
                    ['title' => 'HTML Fundamentals', 'duration' => '2 weeks', 'description' => 'Learn HTML structure and basic tags'],
                    ['title' => 'CSS Styling', 'duration' => '3 weeks', 'description' => 'Master CSS layouts and responsive design'],
                    ['title' => 'JavaScript Basics', 'duration' => '4 weeks', 'description' => 'Learn programming fundamentals with JavaScript'],
                    ['title' => 'Build Your First Website', 'duration' => '2 weeks', 'description' => 'Create a complete responsive website']
                ],
                'intermediate' => [
                    ['title' => 'Advanced JavaScript', 'duration' => '3 weeks', 'description' => 'ES6+, async programming, and APIs'],
                    ['title' => 'Frontend Framework', 'duration' => '4 weeks', 'description' => 'Learn React or Vue.js'],
                    ['title' => 'Backend Development', 'duration' => '4 weeks', 'description' => 'Node.js and database integration'],
                    ['title' => 'Full-Stack Project', 'duration' => '3 weeks', 'description' => 'Build a complete web application']
                ],
                'advanced' => [
                    ['title' => 'System Architecture', 'duration' => '3 weeks', 'description' => 'Scalable application design patterns'],
                    ['title' => 'DevOps & Deployment', 'duration' => '3 weeks', 'description' => 'CI/CD, Docker, and cloud deployment'],
                    ['title' => 'Performance Optimization', 'duration' => '2 weeks', 'description' => 'Web performance and monitoring'],
                    ['title' => 'Advanced Project', 'duration' => '4 weeks', 'description' => 'Build a production-ready application']
                ]
            ],
            'data science' => [
                'beginner' => [
                    ['title' => 'Python Programming', 'duration' => '3 weeks', 'description' => 'Learn Python syntax and basics'],
                    ['title' => 'Data Manipulation', 'duration' => '3 weeks', 'description' => 'Pandas and NumPy fundamentals'],
                    ['title' => 'Data Visualization', 'duration' => '2 weeks', 'description' => 'Create charts with Matplotlib and Seaborn'],
                    ['title' => 'First Data Project', 'duration' => '2 weeks', 'description' => 'Complete data analysis project']
                ],
                'intermediate' => [
                    ['title' => 'Statistics & Probability', 'duration' => '3 weeks', 'description' => 'Statistical analysis fundamentals'],
                    ['title' => 'Machine Learning Basics', 'duration' => '4 weeks', 'description' => 'Supervised and unsupervised learning'],
                    ['title' => 'SQL & Databases', 'duration' => '2 weeks', 'description' => 'Data querying and management'],
                    ['title' => 'ML Project', 'duration' => '3 weeks', 'description' => 'Build a predictive model']
                ],
                'advanced' => [
                    ['title' => 'Deep Learning', 'duration' => '4 weeks', 'description' => 'Neural networks and TensorFlow'],
                    ['title' => 'Big Data Tools', 'duration' => '3 weeks', 'description' => 'Spark and distributed computing'],
                    ['title' => 'MLOps', 'duration' => '3 weeks', 'description' => 'Model deployment and monitoring'],
                    ['title' => 'Capstone Project', 'duration' => '4 weeks', 'description' => 'End-to-end data science project']
                ]
            ],
            'mobile development' => [
                'beginner' => [
                    ['title' => 'Mobile Development Basics', 'duration' => '2 weeks', 'description' => 'Understanding mobile platforms'],
                    ['title' => 'React Native Fundamentals', 'duration' => '4 weeks', 'description' => 'Learn cross-platform development'],
                    ['title' => 'UI/UX for Mobile', 'duration' => '2 weeks', 'description' => 'Mobile design principles'],
                    ['title' => 'First Mobile App', 'duration' => '3 weeks', 'description' => 'Build and deploy your first app']
                ],
                'intermediate' => [
                    ['title' => 'Advanced React Native', 'duration' => '3 weeks', 'description' => 'Navigation, state management, APIs'],
                    ['title' => 'Native Features', 'duration' => '3 weeks', 'description' => 'Camera, GPS, push notifications'],
                    ['title' => 'App Store Deployment', 'duration' => '1 week', 'description' => 'Publishing to app stores'],
                    ['title' => 'Mobile App Project', 'duration' => '4 weeks', 'description' => 'Complete mobile application']
                ],
                'advanced' => [
                    ['title' => 'Performance Optimization', 'duration' => '2 weeks', 'description' => 'Mobile app performance tuning'],
                    ['title' => 'Advanced Architecture', 'duration' => '3 weeks', 'description' => 'Scalable mobile app architecture'],
                    ['title' => 'Testing & Analytics', 'duration' => '2 weeks', 'description' => 'App testing and user analytics'],
                    ['title' => 'Production App', 'duration' => '5 weeks', 'description' => 'Build a market-ready mobile app']
                ]
            ]
        ];
        
        // Find the best matching path
        $selectedPath = null;
        foreach ($interests as $interest) {
            if (isset($paths[$interest][$skillLevel])) {
                $selectedPath = $paths[$interest][$skillLevel];
                break;
            }
        }
        
        // Default path if no match
        if (!$selectedPath) {
            $selectedPath = $paths['web development'][$skillLevel];
        }
        
        return [
            'title' => ucfirst($skillLevel) . ' Learning Path',
            'description' => 'A structured learning journey tailored to your skill level',
            'steps' => $selectedPath,
            'total_duration' => $this->calculateTotalDuration($selectedPath)
        ];
    }
    
    /**
     * Get project resources and help
     */
    public function getProjectResources($projectType = 'general') {
        $resources = [
            'web' => [
                ['title' => 'Frontend Checklist', 'description' => 'Essential items for web development projects'],
                ['title' => 'Responsive Design Guide', 'description' => 'Tips for mobile-friendly websites'],
                ['title' => 'JavaScript Debugging', 'description' => 'Common debugging techniques and tools'],
                ['title' => 'Performance Optimization', 'description' => 'Speed up your web applications']
            ],
            'mobile' => [
                ['title' => 'Mobile UI Guidelines', 'description' => 'Best practices for mobile app design'],
                ['title' => 'App Store Guidelines', 'description' => 'Requirements for app store submission'],
                ['title' => 'Testing on Devices', 'description' => 'How to test your app on real devices'],
                ['title' => 'Push Notifications Setup', 'description' => 'Implementing push notifications']
            ],
            'data' => [
                ['title' => 'Data Cleaning Techniques', 'description' => 'Handle missing and messy data'],
                ['title' => 'Visualization Best Practices', 'description' => 'Create effective data visualizations'],
                ['title' => 'Model Validation Methods', 'description' => 'Validate your machine learning models'],
                ['title' => 'Data Ethics Guidelines', 'description' => 'Responsible data science practices']
            ],
            'design' => [
                ['title' => 'Design System Creation', 'description' => 'Build consistent design systems'],
                ['title' => 'User Research Methods', 'description' => 'Understand your users better'],
                ['title' => 'Prototyping Tools', 'description' => 'Create interactive prototypes'],
                ['title' => 'Accessibility Guidelines', 'description' => 'Design for all users']
            ],
            'general' => [
                ['title' => 'Project Planning', 'description' => 'Structure your project for success'],
                ['title' => 'Version Control', 'description' => 'Git best practices for projects'],
                ['title' => 'Code Documentation', 'description' => 'Write clear and helpful documentation'],
                ['title' => 'Testing Strategies', 'description' => 'Ensure your project works correctly']
            ]
        ];
        
        return $resources[$projectType] ?? $resources['general'];
    }
    
    /**
     * Get frequently asked questions
     */
    public function getFAQs() {
        return [
            [
                'question' => 'How do I enroll in a course?',
                'answer' => 'To enroll in a course, simply browse our course catalog, click on the course you\'re interested in, and click the "Enroll Now" button. You\'ll need to create an account if you haven\'t already.'
            ],
            [
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept all major credit cards, PayPal, and bank transfers. Some courses are also available for free!'
            ],
            [
                'question' => 'Can I get a certificate after completing a course?',
                'answer' => 'Yes! Upon successful completion of a course, you\'ll receive a digital certificate that you can share on your resume and LinkedIn profile.'
            ],
            [
                'question' => 'How do I track my progress?',
                'answer' => 'You can track your progress by visiting the "My Courses" section in your dashboard. You\'ll see completion percentages and your current lesson for each enrolled course.'
            ],
            [
                'question' => 'Can I access courses on mobile devices?',
                'answer' => 'Absolutely! Our platform is fully responsive and works great on smartphones and tablets. You can learn on-the-go wherever you are.'
            ],
            [
                'question' => 'How do I contact support?',
                'answer' => 'You can reach our support team through the contact form on our website, or you can ask me here in this chat for immediate assistance!'
            ],
            [
                'question' => 'Are there any prerequisites for courses?',
                'answer' => 'Prerequisites vary by course. Check the course description page where any required prior knowledge or skills will be clearly listed.'
            ],
            [
                'question' => 'Can I get a refund if I\'m not satisfied?',
                'answer' => 'Yes, we offer a 30-day money-back guarantee for all paid courses. If you\'re not satisfied, contact support for a full refund.'
            ]
        ];
    }
    
    /**
     * Get course categories and their descriptions
     */
    public function getCourseCategories() {
        return [
            'web-development' => [
                'name' => 'Web Development',
                'description' => 'Learn HTML, CSS, JavaScript, and popular frameworks',
                'icon' => '🌐'
            ],
            'data-science' => [
                'name' => 'Data Science',
                'description' => 'Python, machine learning, data analysis and visualization',
                'icon' => '📊'
            ],
            'mobile-development' => [
                'name' => 'Mobile Development',
                'description' => 'Build iOS and Android apps with React Native and Flutter',
                'icon' => '📱'
            ],
            'ui-ux-design' => [
                'name' => 'UI/UX Design',
                'description' => 'User interface design, user experience, and prototyping',
                'icon' => '🎨'
            ],
            'programming' => [
                'name' => 'Programming',
                'description' => 'Programming fundamentals, algorithms, and problem solving',
                'icon' => '💻'
            ],
            'devops' => [
                'name' => 'DevOps',
                'description' => 'Deployment, CI/CD, Docker, Kubernetes, and cloud platforms',
                'icon' => '⚙️'
            ]
        ];
    }
    
    /**
     * Get default course recommendations when database query fails
     */
    private function getDefaultCourseRecommendations() {
        return [
            [
                'id' => 1,
                'title' => 'Complete Web Development Bootcamp',
                'description' => 'Learn HTML, CSS, JavaScript, and build real-world projects',
                'level' => 'beginner',
                'duration' => '12 weeks',
                'category' => 'web-development',
                'price' => 99.99
            ],
            [
                'id' => 2,
                'title' => 'Python for Data Science',
                'description' => 'Master Python, pandas, and machine learning fundamentals',
                'level' => 'beginner',
                'duration' => '10 weeks',
                'category' => 'data-science',
                'price' => 89.99
            ],
            [
                'id' => 3,
                'title' => 'Mobile App Development with React Native',
                'description' => 'Build cross-platform mobile apps for iOS and Android',
                'level' => 'intermediate',
                'duration' => '8 weeks',
                'category' => 'mobile-development',
                'price' => 79.99
            ]
        ];
    }
    
    /**
     * Calculate total duration for learning path
     */
    private function calculateTotalDuration($steps) {
        $totalWeeks = 0;
        foreach ($steps as $step) {
            if (preg_match('/(\d+)\s*weeks?/', $step['duration'], $matches)) {
                $totalWeeks += intval($matches[1]);
            }
        }
        return $totalWeeks . ' weeks total';
    }
    
    /**
     * Get learning tips based on category
     */
    public function getLearningTips($category = 'general') {
        $tips = [
            'web-development' => [
                'Practice coding daily, even if it\'s just 30 minutes',
                'Build projects to apply what you learn',
                'Use browser developer tools to debug and learn',
                'Join web development communities for support'
            ],
            'data-science' => [
                'Start with statistics and probability basics',
                'Work with real datasets to gain practical experience',
                'Visualize your data to understand patterns',
                'Learn SQL for data manipulation'
            ],
            'mobile-development' => [
                'Test your apps on real devices, not just simulators',
                'Follow platform-specific design guidelines',
                'Start with cross-platform frameworks for efficiency',
                'Keep up with mobile platform updates'
            ],
            'general' => [
                'Set specific learning goals and track progress',
                'Take breaks and practice spaced repetition',
                'Teach others what you learn to reinforce knowledge',
                'Build a portfolio of your work'
            ]
        ];
        
        return $tips[$category] ?? $tips['general'];
    }
}
?>