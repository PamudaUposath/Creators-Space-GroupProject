<?php
// Set page-specific variables
$pageTitle = "Bookmarked Courses";
$pageDescription = "View your bookmarked courses and continue your learning journey.";
$additionalCSS = ['./src/css/bookmarked.css'];
$additionalJS = ['./src/js/bookmarked.js'];

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
    <section class="bookmarked-hero">
        <div class="container">
            <div class="hero-content">
                <h1>My Bookmarked Courses</h1>
                <p>Continue your learning journey with your saved courses</p>
            </div>
        </div>
    </section>

    <!-- Bookmarked Courses -->
    <section class="bookmarked-courses">
        <div class="container">
            <div id="bookmarkedGrid" class="courses-grid">
                <!-- Bookmarked courses will be loaded here -->
                <div class="empty-state">
                    <i class="fas fa-bookmark fa-3x"></i>
                    <h3>No Bookmarked Courses Yet</h3>
                    <p>Start exploring our courses and bookmark your favorites to see them here.</p>
                    <a href="courses.php" class="btn primary">Browse Courses</a>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer
include './includes/footer.php';
?>
