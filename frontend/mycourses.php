<?php
// Set page-specific variables
$pageTitle = "My Courses";
$pageDescription = "View your enrolled courses and continue your learning journey.";
$additionalCSS = ['./src/css/mycourses.css', './src/css/modal.css'];
$additionalJS = ['./src/js/modal-utility.js', './src/js/mycourses.js'];

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
            <h1 class="page-title">My Courses</h1>
            <p class="page-subtitle">View your enrolled courses and continue your learning journey.</p>
        </div>
    <section class="courses-hero">
        <div class="container">
            <div class="hero-content">
                <h1>My Enrolled Courses</h1>
                <p>Continue your learning journey with your enrolled courses</p>
            </div>
        </div>
    </section>

    <!-- My Courses Section -->
    <section class="my-courses">
        <div class="container">
            <div id="coursesGrid" class="courses-grid">
                <!-- Enrolled courses will be loaded here -->
                <div class="empty-state">
                    <i class="fas fa-graduation-cap fa-3x"></i>
                    <h3>Courses are empty</h3>
                    <p>You haven't enrolled in any courses yet. Start exploring our courses and enroll in your favorites.</p>
                    <a href="courses.php" class="btn primary">Browse Courses</a>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
include './includes/footer.php';
?>