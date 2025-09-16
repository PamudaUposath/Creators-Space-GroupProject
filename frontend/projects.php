<?php
// Set page-specific variables
$pageTitle = "Projects";
$pageDescription = "Explore hands-on projects to practice and showcase your skills.";
$additionalCSS = ['./src/css/projects.css'];
$additionalJS = ['./src/js/projects.js'];

// Start session to check login status
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header('Location: login.php');
    exit;
}

$user = [
    'id' => $_SESSION['user_id'],
    'first_name' => $_SESSION['first_name'] ?? '',
    'last_name' => $_SESSION['last_name'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'role' => $_SESSION['role'] ?? 'user'
];

// Include header
include './includes/header.php';
?>

    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Practice Projects</h1>
            <p class="page-subtitle">Build your portfolio with hands-on projects designed to enhance your skills</p>
        </div>

        <!-- Projects Section -->
        <section class="section">
            <div style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <button class="btn active" data-filter="all">All Projects</button>
                <button class="btn" data-filter="web">Web Development</button>
                <button class="btn" data-filter="mobile">Mobile Apps</button>
                <button class="btn" data-filter="data">Data Science</button>
                <button class="btn" data-filter="ai">AI/ML</button>
            </div>

            <div id="projectsGrid" class="projects-grid">
                <!-- Sample Projects -->
            <div class="offerings-grid" id="projects-grid">
                <div class="card" data-category="web">
                    <div style="position: relative;">
                        <img src="./assets/images/projects/web-portfolio.jpg" alt="Portfolio Website" onerror="this.src='./assets/images/hero-img-center.png'" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">Personal Portfolio Website</h3>
                        <p style="color: #34495e; line-height: 1.6; margin-bottom: 1rem;">Create a responsive portfolio website showcasing your skills and projects.</p>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">HTML</span>
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">CSS</span>
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">JavaScript</span>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-top: auto;">
                            <button class="btn login" style="font-size: 0.9rem; padding: 0.6rem 1rem;">Start Project</button>
                            <button class="btn signup" style="font-size: 0.9rem; padding: 0.6rem 1rem;">View Details</button>
                        </div>
                    </div>
                </div>

                <div class="card" data-category="web">
                    <div style="position: relative;">
                        <img src="./assets/images/projects/ecommerce.jpg" alt="E-commerce Site" onerror="this.src='./assets/images/hero-img-center.png'" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">E-commerce Website</h3>
                        <p style="color: #34495e; line-height: 1.6; margin-bottom: 1rem;">Build a full-stack e-commerce platform with shopping cart and payment integration.</p>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">React</span>
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">Node.js</span>
                            <span style="background: rgba(102,126,234,0.1); color: #667eea; padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem;">MongoDB</span>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-top: auto;">
                            <button class="btn login" style="font-size: 0.9rem; padding: 0.6rem 1rem;">Start Project</button>
                            <button class="btn signup" style="font-size: 0.9rem; padding: 0.6rem 1rem;">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php
// Include footer
include './includes/footer.php';
?>
