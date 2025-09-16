<?php
// Set page-specific variables
$pageTitle = "About Us";
$pageDescription = "Learn more about Creators-Space and our mission to empower the next generation of tech innovators.";
$additionalCSS = ['./src/css/about.css'];
$additionalJS = ['./src/js/about.js', './src/js/newsletter.js', './src/js/scrollToTop.js'];

// Get database connection and platform statistics
require_once __DIR__ . '/../backend/config/db_connect.php';
require_once __DIR__ . '/../backend/lib/helpers.php';

// Fetch real statistics from database
$platformStats = getPlatformStatistics($pdo);

// Include header
include './includes/header.php';
?>

    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">About Creators-Space</h1>
            <p class="page-subtitle">Empowering the next generation of tech innovators through quality education and hands-on learning</p>
        </div>

        <!-- Mission & Vision Section -->
        <section class="section">
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
        </section>

        <!-- What We Offer Section -->
        <section class="section">
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
        </section>

        <!-- Stats Section -->
        <section class="section">
            <div class="stats-container">
                <h2 class="section-title">Our Impact</h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars($platformStats['students_display']); ?></div>
                        <div class="stat-label">Students Enrolled</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars($platformStats['instructors_display']); ?></div>
                        <div class="stat-label">Expert Instructors</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars($platformStats['courses_display']); ?></div>
                        <div class="stat-label">Courses Available</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo htmlspecialchars($platformStats['success_rate_display']); ?></div>
                        <div class="stat-label">Success Rate</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="section">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="content-grid">
                <div class="card">
                    <div class="member-photo">
                        <img src="./assets/images/anurag-v.jpg" alt="Anurag Vishwakarma" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;" />
                    </div>
                    <h3>Anurag Vishwakarma</h3>
                    <p class="member-role"><strong>Founder & CEO</strong></p>
                    <p class="member-bio">Passionate about democratizing tech education and empowering the next generation of innovators.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="card">
                    <div class="member-photo">
                        <img src="./assets/images/sumit-r.jpg" alt="Sumit Ranjan" style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px;" />
                    </div>
                    <h3>Sumit Ranjan</h3>
                    <p class="member-role"><strong>CTO & Lead Instructor</strong></p>
                    <p class="member-bio">Expert in full-stack development with years of industry experience in building scalable applications.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="section">
            <h2 class="section-title">Our Values</h2>
            <div class="offerings-grid">
                <div class="card">
                    <div class="offering-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality Education</h3>
                    <p>We maintain the highest standards in our curriculum and teaching methods to ensure effective learning outcomes.</p>
                </div>
                <div class="card">
                    <div class="offering-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We continuously update our content and methods to stay ahead of industry trends and technological advances.</p>
                </div>
                <div class="card">
                    <div class="offering-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Accessibility</h3>
                    <p>We believe quality education should be accessible to everyone, regardless of background or location.</p>
                </div>
                <div class="card">
                    <div class="offering-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h3>Community</h3>
                    <p>We foster a supportive learning environment where students and instructors collaborate and grow together.</p>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="section">
            <div style="text-align: center; padding: 2rem;">
                <h2 class="section-title">Ready to Start Your Learning Journey?</h2>
                <p style="color: rgba(255,255,255,0.9); font-size: 1.2rem; margin-bottom: 2rem;">Join thousands of students who have transformed their careers with Creators-Space</p>
                <div class="hero-actions">
                    <?php if (!$isLoggedIn): ?>
                        <a href="signup.php" class="hero-btn">Get Started Free</a>
                        <a href="courses.php" class="hero-btn">Browse Courses</a>
                    <?php else: ?>
                        <a href="courses.php" class="hero-btn">Explore Courses</a>
                        <a href="profile.php" class="hero-btn">My Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

<?php
// Include footer
include './includes/footer.php';
?>
