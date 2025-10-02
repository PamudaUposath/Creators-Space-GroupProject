<?php
// Include database connection
require_once __DIR__ . '/../backend/config/db_connect.php';

// Set page-specific variables
$pageTitle = "Courses";
$pageDescription = "Explore our comprehensive courses in web development, programming, and technology.";
$additionalCSS = ['./src/css/courses.css'];
$additionalJS = ['./src/js/courses.js'];

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

if ($isLoggedIn) {
    $user = [
        'id' => $_SESSION['user_id'],
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'user'
    ];
}

// Function to fetch courses from database
function getCourseCategories($title, $description) {
    $categories = [];
    $content = strtolower($title . ' ' . $description);
    
    // Map course content to categories
    if (preg_match('/web\s+development|full\s+stack|frontend|backend|html|css|javascript|react|angular|vue/', $content)) {
        $categories[] = 'web-development';
    }
    if (preg_match('/ui\/ux|design|user\s+interface|user\s+experience|figma|photoshop/', $content)) {
        $categories[] = 'design';
    }
    if (preg_match('/python|java|javascript|programming|coding/', $content)) {
        $categories[] = 'programming';
    }
    if (preg_match('/data\s+science|machine\s+learning|analytics|data\s+analysis/', $content)) {
        $categories[] = 'data-science';
    }
    if (preg_match('/mobile|ios|android|react\s+native|flutter/', $content)) {
        $categories[] = 'mobile';
    }
    if (preg_match('/devops|docker|kubernetes|aws|cloud/', $content)) {
        $categories[] = 'devops';
    }
    
    return empty($categories) ? ['other'] : $categories;
}
// ! in Database you need to add as => ./assets/images/courses/full-stack-web-developer.png
// ! Otherwise you can use weburl => https://img.itch.zone/aW1hZ2UyL2phbS8zODY2NjcvMTU3MzY4MTcucG5n/original/tAnW%2BJ.png
function fetchCoursesFromDatabase($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.id,
                c.title,
                c.description,
                c.price,
                c.duration,
                c.level,
                c.image_url, 
                CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.is_active = 1
            ORDER BY c.created_at DESC
        ");
        
        $stmt->execute();
        $dbCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $courses = [];
        foreach ($dbCourses as $course) {
            // Get categories for this course
            $categories = getCourseCategories($course['title'], $course['description']);
            
            $courses[] = [
                'id' => $course['id'],
                'title' => $course['title'],
                'description' => $course['description'],
                'image' => $course['image_url'] ?: "https://creators-space-group-project.s3.ap-south-1.amazonaws.com/courses/full-stack-web-developer.png", // Default image
                'price' => (float)$course['price'],
                'duration' => $course['duration'],
                'level' => ucfirst($course['level']),
                'instructor' => $course['instructor_name'] ?: 'Raj Kumar',
                'category' => $categories[0] // Use first category for primary classification
            ];
        }
        
        return $courses;
        
    } catch (PDOException $e) {
        error_log("Error fetching courses: " . $e->getMessage());
        return []; // Return empty array on error
    }
}

// Fetch courses from database
$courses = fetchCoursesFromDatabase($pdo);

// If no courses found in database, add a fallback message
if (empty($courses)) {
    error_log("No courses found in database");
}

// Include header
include './includes/header.php';
?>
    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Explore Our Courses</h1>
            <p class="page-subtitle">Discover a wide range of technology courses designed to advance your career and skills</p>
            
            <!-- Enhanced Search Bar -->
            <div class="search-container">
                <div class="search-bar-wrapper">
                    <div class="search-input-group">
                        <input type="text" placeholder="Search courses..." id="courseSearch" class="search-input">
                        <button type="button" id="searchBtn" class="search-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="search-suggestions" id="searchSuggestions"></div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <section class="section">
            <div class="filters-container">
                <div class="filter-group">
                    <label class="filter-label">Category:</label>
                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        <option value="web-development">Web Development</option>
                        <option value="design">Design</option>
                        <option value="programming">Programming</option>
                        <option value="data-science">Data Science</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Level:</label>
                    <select id="levelFilter" class="filter-select">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Price:</label>
                    <select id="priceFilter" class="filter-select">
                        <option value="">All Prices</option>
                        <option value="free">Free</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Courses Grid -->
        <section class="section">
            <div id="coursesGrid" class="offerings-grid">
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <div class="card course-card" 
                             data-level="<?php echo strtolower($course['level']); ?>"
                             data-category="<?php echo htmlspecialchars($course['category']); ?>"
                             data-price="<?php echo $course['price'] > 0 ? 'paid' : 'free'; ?>"
                             data-price-value="<?php echo $course['price']; ?>">
                            <div style="position: relative;">
                                <a href="course-detail.php?id=<?php echo $course['id']; ?>" style="display: block; text-decoration: none;">
                                    <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem; transition: transform 0.3s ease;">
                                </a>
                                <?php if ($isLoggedIn): ?>
                                    <button onclick="toggleBookmark(<?php echo $course['id']; ?>)" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                        <i class="far fa-bookmark"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem; padding: 0.3rem 0.8rem; background: rgba(255,255,255,0.1); border-radius: 15px;"><?php echo $course['level']; ?></span>
                                <span style="color: #ffffff; font-size: 0.9rem;"><?php echo $course['duration']; ?></span>
                            </div>
                            <a href="course-detail.php?id=<?php echo $course['id']; ?>" style="text-decoration: none;">
      <h3 class="card-title" style="margin: 0.5rem 0; font-size: 1.3rem; transition: color 0.3s ease; color: white;">   <?php echo htmlspecialchars($course['title']); ?></h3>

                            </a>
                            <p class="card-description" style="line-height: 1.6; margin-bottom: 1rem;"><?php echo htmlspecialchars(substr($course['description'], 0, 120)); ?><?php echo strlen($course['description']) > 120 ? '...' : ''; ?></p>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; color: #7f8c8d; font-size: 0.9rem;">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                <div style="font-size: 1.2rem; font-weight: 700;">
                                    <?php if ($course['price'] > 0): ?>
                                        <span class="modern-gradient-text">$<?php echo number_format($course['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span style="color: #27ae60;">Free</span>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="btn login" style="font-size: 0.9rem; padding: 0.6rem 1.2rem; text-decoration: none;">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: rgba(255,255,255,0.8);">
                        <i class="fas fa-graduation-cap" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <h3 style="margin-bottom: 1rem; color: #ffffff;">No Courses Available</h3>
                        <p style="margin-bottom: 1.5rem;">There are currently no courses available. Please check back later or contact support.</p>
                        <a href="index.php" class="btn login">Return to Home</a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($courses)): ?>
            <!-- Load More Button -->
            <div style="text-align: center; margin-top: 3rem;">
                <button id="loadMoreBtn" class="hero-btn">Load More Courses</button>
            </div>
            <?php endif; ?>
        </section>

        <!-- CTA Section -->
        <section class="section cta-section">
            <div class="cta-container">
                <h2 class="section-title cta-title">Ready to Start Learning?</h2>
                <p class="cta-description">Join thousands of students and advance your career with our expert-led courses</p>
                <div class="hero-actions">
                    <?php if (!$isLoggedIn): ?>
                        <a href="signup.php" class="hero-btn">
                            <i class="fas fa-rocket"></i>
                            Get Started Free
                        </a>
                    <?php else: ?>
                        <a href="profile.php" class="hero-btn">
                            <i class="fas fa-user-graduate"></i>
                            View My Courses
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

<?php
// Set additional JS for this page
$additionalJS = ['./src/js/courses.js'];
$customJS = '
// User authentication state for JavaScript
window.userAuth = {
    isLoggedIn: ' . ($isLoggedIn ? 'true' : 'false') . ',
    user: ' . ($isLoggedIn ? json_encode($user) : 'null') . '
};

// Course interaction functions
function toggleBookmark(courseId) {
    if (!window.userAuth.isLoggedIn) {
        showToast("Please login to bookmark courses", "warning");
        return;
    }
    
    // TODO: Implement bookmark functionality with backend
    console.log("Bookmarking course:", courseId);
    showToast("Bookmark functionality will be implemented with backend integration", "info");
}

function enrollCourse(courseId) {
    if (!window.userAuth.isLoggedIn) {
        window.location.href = "login.php";
        return;
    }
    
    // TODO: Implement enrollment functionality with backend
    console.log("Enrolling in course:", courseId);
    showToast("Enrollment functionality will be implemented with backend integration", "info");
}

// Note: Search and filter functionality is handled by CourseSearch class in courses.js

// Load more functionality (placeholder)
document.getElementById("loadMoreBtn").addEventListener("click", function() {
    showToast("Load more functionality will be implemented with backend integration", "info");
});
';

// Include footer
include './includes/footer.php';
?>
