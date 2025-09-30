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
                    
                    <form class="verification-form" id="verificationForm">
                        <div class="form-group">
                            <label for="certificateId">Certificate ID</label>
                            <input type="text" id="certificateId" placeholder="Enter your certificate ID (e.g., CERT-JS30-2024-001)" required>
                            <small class="form-help">Try: CERT-JS30-2024-001, CERT-FSWD-2024-002</small>
                        </div>
                        
                        <button type="button" id="verifyBtn" class="verify-btn">
                            <i class="fas fa-search"></i> Verify Certificate
                        </button>
                    </form>
                    
                    <div id="verificationResult" class="verification-result" style="display: none;">
                        <!-- Results will be displayed here -->
                    </div>
                </div>
                
                <!-- Inline Debug Script -->
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const verifyBtn = document.getElementById('verifyBtn');
                    const certificateInput = document.getElementById('certificateId');
                    const resultDiv = document.getElementById('verificationResult');
                    
                    if (verifyBtn) {
                        verifyBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            
                            const certificateId = certificateInput ? certificateInput.value.trim() : '';
                            
                            if (!certificateId) {
                                alert('Please enter a certificate ID');
                                return;
                            }
                            
                            // Show loading state
                            const originalText = verifyBtn.innerHTML;
                            verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                            verifyBtn.disabled = true;
                            
                            if (resultDiv) {
                                resultDiv.style.display = 'block';
                                resultDiv.innerHTML = '<div style="background: #2a2a2a; color: white; padding: 20px; border-radius: 8px; margin: 20px 0;"><h3>🔍 Verifying Certificate...</h3><p>Please wait...</p></div>';
                            }
                            
                            // Make API call
                            const apiUrl = `../backend/api/verify_certificate.php?id=${encodeURIComponent(certificateId)}`;
                            
                            fetch(apiUrl)
                                .then(response => response.text())
                                .then(text => {
                                    try {
                                        const data = JSON.parse(text);
                                        
                                        if (data.success && data.verified) {
                                            if (resultDiv) {
                                                resultDiv.innerHTML = `
                                                    <div style="background: linear-gradient(135deg, rgba(34, 139, 34, 0.15) 0%, rgba(46, 204, 113, 0.1) 100%); 
                                                         border: 2px solid rgba(46, 204, 113, 0.4); color: white; padding: 30px; border-radius: 12px; margin: 20px 0;
                                                         box-shadow: 0 8px 32px rgba(46, 204, 113, 0.2);">
                                                        <div style="text-align: center; margin-bottom: 25px;">
                                                            <i class="fas fa-check-circle" style="font-size: 4rem; color: #2ecc71; margin-bottom: 15px; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
                                                            <h3 style="color: #2ecc71; margin: 0; font-size: 1.8rem; font-weight: 700; text-shadow: 0 1px 2px rgba(0,0,0,0.2);">Certificate Verified Successfully!</h3>
                                                        </div>
                                                        <div style="background: rgba(255, 255, 255, 0.05); padding: 20px; border-radius: 8px; margin: 20px 0;">
                                                            <div style="display: grid; gap: 12px;">
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                                                    <span style="color: #ffffff; font-weight: 600;">Certificate ID:</span>
                                                                    <span style="color: #2ecc71; font-weight: 700; font-family: monospace; background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px;">${data.data.certificate_id}</span>
                                                                </div>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                                                    <span style="color: #ffffff; font-weight: 600;">Student Name:</span>
                                                                    <span style="color: #ffffff; font-weight: 700;">${data.data.student_name}</span>
                                                                </div>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                                                    <span style="color: #ffffff; font-weight: 600;">Course:</span>
                                                                    <span style="color: #ffffff; font-weight: 700;">${data.data.course_name}</span>
                                                                </div>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                                                    <span style="color: #ffffff; font-weight: 600;">Level:</span>
                                                                    <span style="color: #2ecc71; font-weight: 700; text-transform: capitalize;">${data.data.level || 'N/A'}</span>
                                                                </div>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">
                                                                    <span style="color: #ffffff; font-weight: 600;">Issue Date:</span>
                                                                    <span style="color: #ffffff; font-weight: 700;">${new Date(data.data.issue_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                                                </div>
                                                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0;">
                                                                    <span style="color: #ffffff; font-weight: 600;">Instructor:</span>
                                                                    <span style="color: #ffffff; font-weight: 700;">${data.data.instructor || 'N/A'}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 2px solid rgba(46, 204, 113, 0.3);">
                                                            <div style="background: rgba(46, 204, 113, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 10px;">
                                                                <p style="margin: 0; font-size: 1.1rem; color: #ffffff;"><i class="fas fa-shield-alt" style="color: #2ecc71; margin-right: 8px;"></i> This certificate is authentic and issued by Creators Space</p>
                                                            </div>
                                                            <p style="color: #ffffff; font-size: 0.95rem; margin: 0;">Verified on ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                                        </div>
                                                    </div>
                                                `;
                                            }
                                        } else {
                                            if (resultDiv) {
                                                resultDiv.innerHTML = `
                                                    <div style="background: rgba(244, 67, 54, 0.1); border: 1px solid rgba(244, 67, 54, 0.3); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                                        <div style="text-align: center; margin-bottom: 20px;">
                                                            <i class="fas fa-times-circle" style="font-size: 3rem; color: #f44336; margin-bottom: 10px; display: block;"></i>
                                                            <h3 style="color: #f44336; margin: 0;">Certificate Not Found</h3>
                                                        </div>
                                                        <p>The certificate ID you entered could not be verified. This could mean:</p>
                                                        <ul style="color: rgba(255, 255, 255, 0.8); margin: 10px 0; padding-left: 20px;">
                                                            <li>The certificate ID is incorrect or contains typos</li>
                                                            <li>The certificate has not been issued yet</li>
                                                            <li>The certificate has been revoked or expired</li>
                                                        </ul>
                                                        <p>Please check the certificate ID and try again.</p>
                                                    </div>
                                                `;
                                            }
                                        }
                                    } catch (parseError) {
                                        if (resultDiv) {
                                            resultDiv.innerHTML = `
                                                <div style="background: rgba(244, 67, 54, 0.1); border: 1px solid rgba(244, 67, 54, 0.3); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                                    <h3 style="color: #f44336; text-align: center;">❌ System Error</h3>
                                                    <p>Unable to process the server response. Please try again later.</p>
                                                </div>
                                            `;
                                        }
                                    }
                                })
                                .catch(error => {
                                    if (resultDiv) {
                                        resultDiv.innerHTML = `
                                            <div style="background: rgba(244, 67, 54, 0.1); border: 1px solid rgba(244, 67, 54, 0.3); color: white; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                                <h3 style="color: #f44336; text-align: center;">❌ Connection Error</h3>
                                                <p>Unable to connect to the verification service. Please check your internet connection and try again.</p>
                                            </div>
                                        `;
                                    }
                                })
                                .finally(() => {
                                    // Reset button state
                                    verifyBtn.innerHTML = originalText;
                                    verifyBtn.disabled = false;
                                });
                        });
                    }
                });
                </script>
                
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
