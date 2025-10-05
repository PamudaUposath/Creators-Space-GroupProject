<?php
// Set page-specific variables
$pageTitle = "Help Center";
$pageDescription = "Find answers to common questions and get support for your learning journey.";
$additionalCSS = ['./src/css/help-center.css'];

// Include header
include './includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-question-circle"></i> Help Center</h1>
                <p>Find answers to common questions and get the support you need</p>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-section">
            <div class="search-container">
                <input type="text" id="helpSearch" placeholder="Search for help topics..." class="search-input">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
        </div>

        <!-- Quick Help Cards -->
        <div class="quick-help-section">
            <h2>Quick Help</h2>
            <div class="help-cards">
                <div class="help-card">
                    <div class="card-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Getting Started</h3>
                    <p>Learn how to create your account and start your learning journey</p>
                    <a href="#getting-started" class="card-link">Learn More</a>
                </div>
                <div class="help-card">
                    <div class="card-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Course Access</h3>
                    <p>How to enroll in courses and access your learning materials</p>
                    <a href="#course-access" class="card-link">Learn More</a>
                </div>
                <div class="help-card">
                    <div class="card-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Certificates</h3>
                    <p>Information about course completion and certificate downloads</p>
                    <a href="#certificates" class="card-link">Learn More</a>
                </div>
                <div class="help-card">
                    <div class="card-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>Payment & Billing</h3>
                    <p>Payment methods, refunds, and billing questions</p>
                    <a href="#payment" class="card-link">Learn More</a>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>Frequently Asked Questions</h2>
            
            <div class="faq-category" id="getting-started">
                <h3><i class="fas fa-graduation-cap"></i> Getting Started</h3>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I create an account?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To create an account:</p>
                            <ol>
                                <li>Click the "Sign Up" button in the top navigation</li>
                                <li>Fill in your personal information (name, email, username)</li>
                                <li>Choose a strong password</li>
                                <li>Verify your email address</li>
                                <li>Complete your profile setup</li>
                            </ol>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I reset my password?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>If you've forgotten your password:</p>
                            <ol>
                                <li>Go to the login page</li>
                                <li>Click "Forgot Password?"</li>
                                <li>Enter your email address</li>
                                <li>Check your email for reset instructions</li>
                                <li>Follow the link to create a new password</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="course-access">
                <h3><i class="fas fa-video"></i> Course Access</h3>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I enroll in a course?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To enroll in a course:</p>
                            <ol>
                                <li>Browse our course catalog</li>
                                <li>Click on the course you're interested in</li>
                                <li>Review the course details and curriculum</li>
                                <li>Click "Enroll Now" or "Add to Cart"</li>
                                <li>Complete the payment process</li>
                                <li>Access your course from "My Courses"</li>
                            </ol>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Can I access courses on mobile devices?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! Our platform is fully responsive and works on all devices including smartphones and tablets. You can access your courses anywhere, anytime.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="certificates">
                <h3><i class="fas fa-certificate"></i> Certificates</h3>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>How do I get my course certificate?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To earn your certificate:</p>
                            <ol>
                                <li>Complete all course modules</li>
                                <li>Pass any required assessments</li>
                                <li>Maintain the minimum progress requirement</li>
                                <li>Your certificate will be automatically generated</li>
                                <li>Download it from your profile or course page</li>
                            </ol>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>Are the certificates recognized?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Our certificates are industry-recognized and can be added to your professional portfolio, LinkedIn profile, or resume to showcase your skills and knowledge.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="faq-category" id="payment">
                <h3><i class="fas fa-credit-card"></i> Payment & Billing</h3>
                <div class="faq-list">
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What payment methods do you accept?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept various payment methods including:</p>
                            <ul>
                                <li>Credit/Debit Cards (Visa, MasterCard, American Express)</li>
                                <li>PayPal</li>
                                <li>Bank transfers</li>
                                <li>Digital wallets</li>
                            </ul>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">
                            <span>What is your refund policy?</span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We offer a 30-day money-back guarantee. If you're not satisfied with a course, you can request a full refund within 30 days of purchase, provided you haven't completed more than 30% of the course content.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Support Section -->
        <div class="support-section">
            <div class="support-card">
                <h2><i class="fas fa-headset"></i> Still Need Help?</h2>
                <p>Can't find what you're looking for? Our support team is here to help you succeed.</p>
                <div class="support-options">
                    <a href="contact.php" class="support-btn primary">
                        <i class="fas fa-envelope"></i>
                        Contact Support
                    </a>
                    <a href="#" class="support-btn secondary">
                        <i class="fas fa-comments"></i>
                        Live Chat
                    </a>
                </div>
                <div class="response-time">
                    <p><i class="fas fa-clock"></i> Average response time: 2-4 hours</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// FAQ Accordion functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const icon = question.querySelector('i');
        
        question.addEventListener('click', function() {
            const isOpen = item.classList.contains('active');
            
            // Close all FAQ items
            faqItems.forEach(faq => {
                faq.classList.remove('active');
                faq.querySelector('.faq-answer').style.maxHeight = '0';
                faq.querySelector('.faq-question i').style.transform = 'rotate(0deg)';
            });
            
            // Open clicked item if it wasn't already open
            if (!isOpen) {
                item.classList.add('active');
                answer.style.maxHeight = answer.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('helpSearch');
    const searchBtn = document.querySelector('.search-btn');
    
    function performSearch() {
        const searchTerm = searchInput.value.toLowerCase();
        const faqItems = document.querySelectorAll('.faq-item');
        const helpCards = document.querySelectorAll('.help-card');
        
        // Search in FAQ items
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = searchTerm ? 'none' : 'block';
            }
        });
        
        // Search in help cards
        helpCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const content = card.querySelector('p').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || content.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = searchTerm ? 'none' : 'block';
            }
        });
    }
    
    searchBtn.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    // Clear search when input is empty
    searchInput.addEventListener('input', function() {
        if (this.value === '') {
            performSearch();
        }
    });
});
</script>

<?php include './includes/footer.php'; ?>