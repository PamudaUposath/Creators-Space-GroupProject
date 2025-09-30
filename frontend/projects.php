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

    <!-- Hero Section -->
    <section class="projects-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Practice Projects</h1>
                <p>Build your portfolio with hands-on projects designed to enhance your skills</p>
            </div>
        </div>
    </section>

    <!-- Projects Grid -->
    <section class="projects-section">
        <div class="container">
            <div class="projects-filters">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="web">Web Development</button>
                <button class="filter-btn" data-filter="mobile">Mobile Apps</button>
                <button class="filter-btn" data-filter="data">Data Science</button>
                <button class="filter-btn" data-filter="ai">AI/ML</button>
            </div>

            <div id="projectsGrid" class="projects-grid">
                <!-- Sample Projects -->
                <div class="project-card" data-category="web">
                    <div class="project-image">
                        <img src="./assets/images/projects/web-portfolio.jpg" alt="Portfolio Website" onerror="this.src='./assets/images/hero-img-center.png'">
                    </div>
                    <div class="project-content">
                        <h3>Personal Portfolio Website</h3>
                        <p>Create a responsive portfolio website showcasing your skills and projects.</p>
                        <div class="project-tags">
                            <span class="tag">HTML</span>
                            <span class="tag">CSS</span>
                            <span class="tag">JavaScript</span>
                        </div>
                        <div class="project-actions">
                            <button class="btn primary">Start Project</button>
                            <button class="btn secondary">View Details</button>
                        </div>
                    </div>
                </div>

                <div class="project-card" data-category="web">
                    <div class="project-image">
                        <img src="./assets/images/projects/ecommerce.jpg" alt="E-commerce Site" onerror="this.src='./assets/images/hero-img-center.png'">
                    </div>
                    <div class="project-content">
                        <h3>E-commerce Website</h3>
                        <p>Build a full-stack e-commerce platform with shopping cart and payment integration.</p>
                        <div class="project-tags">
                            <span class="tag">React</span>
                            <span class="tag">Node.js</span>
                            <span class="tag">MongoDB</span>
                        </div>
                        <div class="project-actions">
                            <button class="btn primary">Start Project</button>
                            <button class="btn secondary">View Details</button>
                        </div>
                    </div>
                </div>

                <!-- More project cards would be added here -->
            </div>
        </div>
    </section>

<?php
// Include footer
include './includes/footer.php';
?>
