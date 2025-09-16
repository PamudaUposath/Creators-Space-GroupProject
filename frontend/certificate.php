<?php
// Set page-specific variables
$pageTitle = "Certificates";
$pageDescription = "Verify your learning achievements and showcase your skills with Creators-Space certificates.";
$additionalCSS = ['./src/css/certificates.css'];
$additionalJS = ['./src/js/certificates.js'];

// Include header
include './includes/header.php';
?>

    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Certificate Verification</h1>
            <p class="page-subtitle">Verify your learning achievements and showcase your skills with our official certificates</p>
        </div>

        <!-- Certificate Verification Section -->
        <section class="section">
            <div class="certificate-container">
                <div class="verification-card">
                    <div class="card-header">
                        <h2>Verify Certificate</h2>
                        <p>Enter your certificate ID to verify its authenticity</p>
                    </div>
                    
                    <div class="verification-form">
                        <div class="form-group">
                            <label for="certificateId">Certificate ID</label>
                            <input type="text" id="certificateId" placeholder="Enter your certificate ID" required>
                        </div>
                        
                        <button type="button" id="verifyBtn" class="verify-btn">
                            <i class="fas fa-search"></i> Verify Certificate
                        </button>
                    </div>
                    
                    <div id="verificationResult" class="verification-result" style="display: none;">
                        <!-- Results will be displayed here -->
                    </div>
                </div>
                
                <!-- Sample Certificates Display -->
                <div class="sample-certificates">
                    <h3>Available Certificates</h3>
                    <div class="certificates-grid">
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fas fa-code"></i>
                            </div>
                            <h4>Web Development Fundamentals</h4>
                            <p>Complete course on HTML, CSS, and JavaScript basics</p>
                            <div class="certificate-badge">
                                <span class="badge">Beginner</span>
                            </div>
                        </div>
                        
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fab fa-react"></i>
                            </div>
                            <h4>React.js Mastery</h4>
                            <p>Advanced React development with hooks and context</p>
                            <div class="certificate-badge">
                                <span class="badge intermediate">Intermediate</span>
                            </div>
                        </div>
                        
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <h4>Database Design</h4>
                            <p>Master SQL and database optimization techniques</p>
                            <div class="certificate-badge">
                                <span class="badge advanced">Advanced</span>
                            </div>
                        </div>
                        
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fab fa-python"></i>
                            </div>
                            <h4>Python Programming</h4>
                            <p>Complete Python course from basics to advanced</p>
                            <div class="certificate-badge">
                                <span class="badge">Beginner</span>
                            </div>
                        </div>
                        
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h4>Mobile App Development</h4>
                            <p>Build native and cross-platform mobile applications</p>
                            <div class="certificate-badge">
                                <span class="badge intermediate">Intermediate</span>
                            </div>
                        </div>
                        
                        <div class="certificate-card">
                            <div class="certificate-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4>Data Science Essentials</h4>
                            <p>Learn data analysis, visualization, and machine learning</p>
                            <div class="certificate-badge">
                                <span class="badge advanced">Advanced</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- How to Get Certificate Section -->
                <div class="how-to-section">
                    <h3>How to Get Your Certificate</h3>
                    <div class="steps-container">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Complete Course</h4>
                                <p>Finish all lessons and assignments in your chosen course</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Pass Assessment</h4>
                                <p>Successfully complete the final assessment with minimum 70% score</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Get Verified</h4>
                                <p>Receive your official certificate with a unique verification ID</p>
                            </div>
                        </div>
                        
                        <div class="step">
                            <div class="step-number">4</div>
                            <div class="step-content">
                                <h4>Share & Showcase</h4>
                                <p>Add to your resume, LinkedIn profile, and professional portfolio</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- CTA Section -->
                <div class="cta-section">
                    <h3>Ready to Earn Your Certificate?</h3>
                    <p>Start learning today and earn industry-recognized certificates</p>
                    <div class="cta-buttons">
                        <?php if (!$isLoggedIn): ?>
                            <a href="courses.php" class="cta-btn primary">
                                <i class="fas fa-graduation-cap"></i> Browse Courses
                            </a>
                            <a href="signup.php" class="cta-btn secondary">
                                <i class="fas fa-user-plus"></i> Sign Up Now
                            </a>
                        <?php else: ?>
                            <a href="courses.php" class="cta-btn primary">
                                <i class="fas fa-book-open"></i> Continue Learning
                            </a>
                            <a href="profile.php" class="cta-btn secondary">
                                <i class="fas fa-user"></i> View My Progress
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
// Include footer
include './includes/footer.php';
?>
