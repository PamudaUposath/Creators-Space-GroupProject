<?php
// Set page-specific variables
$pageTitle = "Privacy Policy";
$pageDescription = "Learn how we collect, use, and protect your personal information on Creators-Space.";
$additionalCSS = ['./src/css/legal.css'];

// Include header
include './includes/header.php';
?>

<div class="page-container">
    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <h1><i class="fas fa-shield-alt"></i> Privacy Policy</h1>
                <p>Your privacy is important to us. This policy explains how we handle your information.</p>
                <div class="last-updated">
                    <i class="fas fa-calendar-alt"></i>
                    Last updated: October 5, 2025
                </div>
            </div>
        </div>

        <!-- Privacy Content -->
        <div class="legal-content">
            <div class="content-container">
                <!-- Table of Contents -->
                <div class="table-of-contents">
                    <h2>Table of Contents</h2>
                    <ul>
                        <li><a href="#overview">1. Privacy Overview</a></li>
                        <li><a href="#information">2. Information We Collect</a></li>
                        <li><a href="#usage">3. How We Use Information</a></li>
                        <li><a href="#sharing">4. Information Sharing</a></li>
                        <li><a href="#security">5. Data Security</a></li>
                        <li><a href="#cookies">6. Cookies and Tracking</a></li>
                        <li><a href="#rights">7. Your Rights</a></li>
                        <li><a href="#children">8. Children's Privacy</a></li>
                        <li><a href="#international">9. International Users</a></li>
                        <li><a href="#changes">10. Policy Changes</a></li>
                        <li><a href="#contact">11. Contact Us</a></li>
                    </ul>
                </div>

                <!-- Privacy Sections -->
                <div class="terms-sections">
                    <section id="overview" class="terms-section">
                        <h2>1. Privacy Overview</h2>
                        <p>At Creators-Space, we are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, share, and protect information about you when you use our online learning platform.</p>
                        <p>By using our services, you agree to the practices described in this Privacy Policy.</p>
                        
                        <div class="highlight-box">
                            <h3>Key Points:</h3>
                            <ul>
                                <li>We only collect information necessary to provide our services</li>
                                <li>We never sell your personal information to third parties</li>
                                <li>You can control your privacy settings and data</li>
                                <li>We use industry-standard security measures</li>
                            </ul>
                        </div>
                    </section>

                    <section id="information" class="terms-section">
                        <h2>2. Information We Collect</h2>
                        
                        <h3>Information You Provide</h3>
                        <ul>
                            <li><strong>Account Information:</strong> Name, email address, username, password</li>
                            <li><strong>Profile Information:</strong> Profile picture, bio, skills, contact details</li>
                            <li><strong>Payment Information:</strong> Billing address, payment method details</li>
                            <li><strong>Course Content:</strong> Assignments, projects, forum posts, messages</li>
                            <li><strong>Communication:</strong> Support requests, feedback, survey responses</li>
                        </ul>

                        <h3>Automatically Collected Information</h3>
                        <ul>
                            <li><strong>Usage Data:</strong> Course progress, time spent, features used</li>
                            <li><strong>Device Information:</strong> IP address, browser type, operating system</li>
                            <li><strong>Log Data:</strong> Access times, pages viewed, errors encountered</li>
                            <li><strong>Analytics Data:</strong> User interactions, performance metrics</li>
                        </ul>

                        <h3>Third-Party Information</h3>
                        <ul>
                            <li>Social media profile information (if you connect accounts)</li>
                            <li>Payment processor information</li>
                            <li>Marketing platform data</li>
                        </ul>
                    </section>

                    <section id="usage" class="terms-section">
                        <h2>3. How We Use Information</h2>
                        
                        <h3>Service Provision</h3>
                        <ul>
                            <li>Create and manage your account</li>
                            <li>Provide access to courses and learning materials</li>
                            <li>Track your progress and issue certificates</li>
                            <li>Process payments and manage billing</li>
                            <li>Provide customer support</li>
                        </ul>

                        <h3>Service Improvement</h3>
                        <ul>
                            <li>Analyze usage patterns to improve our platform</li>
                            <li>Develop new features and courses</li>
                            <li>Conduct research and analytics</li>
                            <li>Test and optimize user experience</li>
                        </ul>

                        <h3>Communication</h3>
                        <ul>
                            <li>Send course updates and announcements</li>
                            <li>Notify you of new features or courses</li>
                            <li>Send marketing communications (with consent)</li>
                            <li>Respond to your inquiries and support requests</li>
                        </ul>

                        <h3>Legal and Security</h3>
                        <ul>
                            <li>Comply with legal obligations</li>
                            <li>Prevent fraud and abuse</li>
                            <li>Enforce our terms of service</li>
                            <li>Protect our users and platform</li>
                        </ul>
                    </section>

                    <section id="sharing" class="terms-section">
                        <h2>4. Information Sharing</h2>
                        
                        <p><strong>We do not sell your personal information.</strong> We may share information in the following circumstances:</p>

                        <h3>Service Providers</h3>
                        <p>We work with trusted third-party service providers who help us operate our platform:</p>
                        <ul>
                            <li>Payment processors (for billing and transactions)</li>
                            <li>Cloud hosting providers (for data storage)</li>
                            <li>Email service providers (for communications)</li>
                            <li>Analytics providers (for usage insights)</li>
                            <li>Customer support tools</li>
                        </ul>

                        <h3>Legal Requirements</h3>
                        <p>We may disclose information when required by law or to:</p>
                        <ul>
                            <li>Comply with legal processes or government requests</li>
                            <li>Protect our rights, property, or safety</li>
                            <li>Protect our users' rights, property, or safety</li>
                            <li>Prevent fraud or abuse</li>
                        </ul>

                        <h3>Business Transfers</h3>
                        <p>If we are involved in a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction.</p>

                        <h3>With Your Consent</h3>
                        <p>We may share information with your explicit consent for specific purposes not covered above.</p>
                    </section>

                    <section id="security" class="terms-section">
                        <h2>5. Data Security</h2>
                        
                        <p>We implement comprehensive security measures to protect your information:</p>

                        <h3>Technical Safeguards</h3>
                        <ul>
                            <li>Encryption in transit and at rest</li>
                            <li>Secure Socket Layer (SSL) technology</li>
                            <li>Firewalls and intrusion detection systems</li>
                            <li>Regular security audits and testing</li>
                            <li>Access controls and authentication</li>
                        </ul>

                        <h3>Administrative Safeguards</h3>
                        <ul>
                            <li>Employee training on data protection</li>
                            <li>Limited access on a need-to-know basis</li>
                            <li>Background checks for staff with data access</li>
                            <li>Incident response procedures</li>
                        </ul>

                        <h3>Physical Safeguards</h3>
                        <ul>
                            <li>Secure data centers with controlled access</li>
                            <li>Environmental controls and monitoring</li>
                            <li>Backup and disaster recovery procedures</li>
                        </ul>
                    </section>

                    <section id="cookies" class="terms-section">
                        <h2>6. Cookies and Tracking</h2>
                        
                        <p>We use cookies and similar technologies to enhance your experience:</p>

                        <h3>Types of Cookies</h3>
                        <ul>
                            <li><strong>Essential Cookies:</strong> Required for basic platform functionality</li>
                            <li><strong>Performance Cookies:</strong> Help us understand how you use our platform</li>
                            <li><strong>Functionality Cookies:</strong> Remember your preferences and settings</li>
                            <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
                        </ul>

                        <h3>Managing Cookies</h3>
                        <p>You can control cookie preferences through:</p>
                        <ul>
                            <li>Your browser settings</li>
                            <li>Our cookie preference center</li>
                            <li>Third-party opt-out tools</li>
                        </ul>

                        <div class="highlight-box">
                            <p><strong>Note:</strong> Disabling certain cookies may affect platform functionality.</p>
                        </div>
                    </section>

                    <section id="rights" class="terms-section">
                        <h2>7. Your Rights</h2>
                        
                        <p>You have several rights regarding your personal information:</p>

                        <h3>Access and Portability</h3>
                        <ul>
                            <li>Request a copy of your personal information</li>
                            <li>Download your data in a portable format</li>
                            <li>Verify the accuracy of your information</li>
                        </ul>

                        <h3>Correction and Updates</h3>
                        <ul>
                            <li>Update your profile information</li>
                            <li>Correct inaccurate data</li>
                            <li>Complete incomplete information</li>
                        </ul>

                        <h3>Deletion and Restriction</h3>
                        <ul>
                            <li>Request deletion of your account and data</li>
                            <li>Restrict processing of your information</li>
                            <li>Object to certain uses of your data</li>
                        </ul>

                        <h3>Communication Preferences</h3>
                        <ul>
                            <li>Opt-out of marketing communications</li>
                            <li>Manage notification settings</li>
                            <li>Choose communication channels</li>
                        </ul>

                        <p>To exercise these rights, contact us at <a href="mailto:privacy@creators-space.com">privacy@creators-space.com</a></p>
                    </section>

                    <section id="children" class="terms-section">
                        <h2>8. Children's Privacy</h2>
                        
                        <p>Our platform is not intended for children under 13 years of age. We do not knowingly collect personal information from children under 13.</p>
                        
                        <p>If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately. We will take steps to remove such information from our systems.</p>

                        <h3>Teen Users (13-17)</h3>
                        <p>For users between 13-17 years old:</p>
                        <ul>
                            <li>We recommend parental supervision</li>
                            <li>Limited data collection practices apply</li>
                            <li>Enhanced privacy protections are in place</li>
                            <li>Parents can request account deletion</li>
                        </ul>
                    </section>

                    <section id="international" class="terms-section">
                        <h2>9. International Users</h2>
                        
                        <p>Our platform is operated from the United States. If you are accessing our services from outside the US, please be aware that your information may be transferred to, stored, and processed in the US.</p>

                        <h3>GDPR Compliance (EU Users)</h3>
                        <p>For users in the European Union, we comply with GDPR requirements:</p>
                        <ul>
                            <li>Lawful basis for processing personal data</li>
                            <li>Enhanced consent mechanisms</li>
                            <li>Data protection impact assessments</li>
                            <li>Right to lodge complaints with supervisory authorities</li>
                        </ul>

                        <h3>Other Jurisdictions</h3>
                        <p>We strive to comply with applicable privacy laws in all jurisdictions where we operate.</p>
                    </section>

                    <section id="changes" class="terms-section">
                        <h2>10. Policy Changes</h2>
                        
                        <p>We may update this Privacy Policy from time to time. When we make changes:</p>
                        <ul>
                            <li>We will post the updated policy on this page</li>
                            <li>We will update the "Last Updated" date</li>
                            <li>For material changes, we will provide additional notice</li>
                            <li>Your continued use constitutes acceptance of changes</li>
                        </ul>

                        <div class="highlight-box">
                            <p><strong>Stay Informed:</strong> We recommend reviewing this policy periodically to stay informed about how we protect your information.</p>
                        </div>
                    </section>

                    <section id="contact" class="terms-section">
                        <h2>11. Contact Us</h2>
                        
                        <p>If you have questions about this Privacy Policy or our privacy practices, please contact us:</p>

                        <div class="contact-info">
                            <div class="contact-method">
                                <h3>Privacy Officer</h3>
                                <p><strong>Email:</strong> privacy@creators-space.com</p>
                                <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                            </div>

                            <div class="contact-method">
                                <h3>Mailing Address</h3>
                                <p>Creators-Space Privacy Team<br>
                                123 Innovation Drive<br>
                                Tech City, TC 12345<br>
                                United States</p>
                            </div>

                            <div class="contact-method">
                                <h3>Response Time</h3>
                                <p>We will respond to privacy inquiries within 30 days of receipt.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Privacy Tools Section -->
                <div class="privacy-tools-section">
                    <div class="tools-box">
                        <h3>Privacy Management Tools</h3>
                        <p>Take control of your privacy with these tools:</p>
                        <div class="tools-actions">
                            <a href="profile.php" class="btn btn-primary">Manage Profile</a>
                            <a href="#" class="btn btn-outline">Download Data</a>
                            <a href="contact.php" class="btn btn-outline">Privacy Questions</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scrolling for table of contents links
document.addEventListener('DOMContentLoaded', function() {
    const tocLinks = document.querySelectorAll('.table-of-contents a');
    
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Highlight the section temporarily
                targetElement.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
                setTimeout(() => {
                    targetElement.style.backgroundColor = '';
                }, 2000);
            }
        });
    });
});
</script>

<?php include './includes/footer.php'; ?>