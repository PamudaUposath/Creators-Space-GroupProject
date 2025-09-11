<?php
// Set page-specific variables
$pageTitle = "About Us";
$pageDescription = "Learn more about Creators-Space and our mission to empower the next generation of tech innovators.";
$additionalCSS = ['./src/css/about.css'];
$additionalJS = ['./src/js/about.js', './src/js/newsletter.js', './src/js/scrollToTop.js'];

// Include header
include './includes/header.php';
?>

    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">About Creators-Space</h1>
                <p class="hero-subtitle">Empowering the next generation of tech innovators through quality education and hands-on learning</p>
            </div>
        </div>
    </section>

    <!-- Mission & Vision Section -->
    <section class="mission-vision">
        <div class="container">
            <div class="content-grid">
                <div class="mission-card">
                    <div class="card-icon">
                        <img src="./assets/images/aboutpage/mission.gif" alt="Mission" />
                    </div>
                    <h2>Our Mission</h2>
                    <p>To democratize technology education by providing accessible, high-quality learning experiences that prepare students for successful careers in the digital age. We believe everyone should have the opportunity to learn, grow, and innovate in the tech industry.</p>
                </div>
                <div class="vision-card">
                    <div class="card-icon">
                        <img src="./assets/images/aboutpage/vision.webp" alt="Vision" />
                    </div>
                    <h2>Our Vision</h2>
                    <p>To become the leading platform for tech education globally, fostering a community of lifelong learners who drive innovation and positive change in the world through technology and creative problem-solving.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer Section -->
    <section class="offerings">
        <div class="container">
            <h2 class="section-title">What We Offer</h2>
            <div class="offerings-grid">
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <h3>Comprehensive Courses</h3>
                    <p>From web development to data science, our courses cover the latest technologies and industry best practices with hands-on projects.</p>
                </div>
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <h3>Internship Opportunities</h3>
                    <p>Connect with leading companies and gain real-world experience through our extensive internship program.</p>
                </div>
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community Support</h3>
                    <p>Join a vibrant community of learners, instructors, and industry professionals who support each other's growth.</p>
                </div>
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Industry Certifications</h3>
                    <p>Earn recognized certificates that validate your skills and boost your career prospects in the tech industry.</p>
                </div>
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3>Real Projects</h3>
                    <p>Build a portfolio of real projects that demonstrate your abilities to potential employers and clients.</p>
                </div>
                <div class="offering-card">
                    <div class="offering-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Expert Instructors</h3>
                    <p>Learn from industry veterans and experienced professionals who bring real-world insights to the classroom.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">Students Enrolled</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Expert Instructors</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100+</div>
                    <div class="stat-label">Courses Available</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">Success Rate</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">
                        <img src="./assets/images/anurag-v.jpg" alt="Anurag Vishwakarma" />
                    </div>
                    <h3>Anurag Vishwakarma</h3>
                    <p class="member-role">Founder & CEO</p>
                    <p class="member-bio">Passionate about democratizing tech education and empowering the next generation of innovators.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="team-member">
                    <div class="member-photo">
                        <img src="./assets/images/sumit-r.jpg" alt="Sumit Ranjan" />
                    </div>
                    <h3>Sumit Ranjan</h3>
                    <p class="member-role">CTO & Lead Instructor</p>
                    <p class="member-bio">Expert in full-stack development with years of industry experience in building scalable applications.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="container">
            <h2 class="section-title">Our Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <h3>Quality Education</h3>
                    <p>We maintain the highest standards in our curriculum and teaching methods to ensure effective learning outcomes.</p>
                </div>
                <div class="value-item">
                    <h3>Innovation</h3>
                    <p>We continuously update our content and methods to stay ahead of industry trends and technological advances.</p>
                </div>
                <div class="value-item">
                    <h3>Accessibility</h3>
                    <p>We believe quality education should be accessible to everyone, regardless of background or location.</p>
                </div>
                <div class="value-item">
                    <h3>Community</h3>
                    <p>We foster a supportive learning environment where students and instructors collaborate and grow together.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Your Learning Journey?</h2>
                <p>Join thousands of students who have transformed their careers with Creators-Space</p>
                <div class="cta-buttons">
                    <?php if (!$isLoggedIn): ?>
                        <a href="signup.php" class="btn primary">Get Started Free</a>
                        <a href="courses.php" class="btn secondary">Browse Courses</a>
                    <?php else: ?>
                        <a href="courses.php" class="btn primary">Explore Courses</a>
                        <a href="profile.php" class="btn secondary">My Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="newsletter-content">
                <h2>Stay Updated</h2>
                <p>Subscribe to our newsletter for the latest updates on courses, tech trends, and career opportunities</p>
                <form id="newsletterForm" class="newsletter-form">
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn">Subscribe</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Creators-Space</h3>
                    <p>Empowering the next generation of tech innovators through quality education and hands-on learning.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="blog.php">Blog</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Programs</h4>
                    <ul>
                        <li><a href="internship.php">Internships</a></li>
                        <li><a href="campus-ambassador.php">Campus Ambassador</a></li>
                        <li><a href="certificate/">Certificates</a></li>
                        <li><a href="projects.php">Projects</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="tandc.php">Terms & Conditions</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Creators-Space. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="./src/js/navbar.js"></script>
<?php
// Include footer
include './includes/footer.php';
?>
