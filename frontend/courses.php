<?php
// Set page-specific variables
$pageTitle = "Courses";
$pageDescription = "Explore our comprehensive courses in web development, programming, and technology.";
$additionalCSS = ['./src/css/courses.css'];

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

// In a real implementation, you would fetch courses from the database
// For now, we'll use static data similar to the original
$courses = [
    [
        'id' => 1,
        'title' => 'Full Stack Web Development',
        'description' => 'Learn complete web development from frontend to backend with modern technologies',
        'image' => './assets/images/full-stack-web-developer.png',
        'price' => 99.99,
        'duration' => '12 weeks',
        'level' => 'Intermediate',
        'instructor' => 'John Instructor'
    ],
    [
        'id' => 2,
        'title' => 'UI/UX Design Fundamentals',
        'description' => 'Master the fundamentals of user interface and user experience design',
        'image' => './assets/images/blogpage/uiux.jpeg',
        'price' => 79.99,
        'duration' => '8 weeks',
        'level' => 'Beginner',
        'instructor' => 'Design Expert'
    ],
    [
        'id' => 3,
        'title' => 'JavaScript in 30 Days',
        'description' => 'Master JavaScript programming in 30 days with practical projects',
        'image' => './assets/images/blogpage/jsin30days.png',
        'price' => 49.99,
        'duration' => '4 weeks',
        'level' => 'Beginner',
        'instructor' => 'JS Master'
    ]
];

// Include header
include './includes/header.php';
?>
    <!-- Hero Section -->
    <section class="courses-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Explore Our Courses</h1>
                <p>Discover a wide range of technology courses designed to advance your career and skills</p>
                <div class="search-bar">
                    <input type="text" placeholder="Search courses..." id="courseSearch">
                    <button type="button" class="btn"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <div class="filters">
                <div class="filter-group">
                    <label>Category:</label>
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="web-development">Web Development</option>
                        <option value="design">Design</option>
                        <option value="programming">Programming</option>
                        <option value="data-science">Data Science</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Level:</label>
                    <select id="levelFilter">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Price:</label>
                    <select id="priceFilter">
                        <option value="">All Prices</option>
                        <option value="free">Free</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Grid -->
    <section class="courses-grid-section">
        <div class="container">
            <div id="coursesGrid" class="courses-grid">
                <?php foreach ($courses as $course): ?>
                    <div class="course-card" data-level="<?php echo strtolower($course['level']); ?>">
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
                            <?php if ($isLoggedIn): ?>
                                <button class="bookmark-btn" onclick="toggleBookmark(<?php echo $course['id']; ?>)">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="course-content">
                            <div class="course-meta">
                                <span class="level level-<?php echo strtolower($course['level']); ?>"><?php echo $course['level']; ?></span>
                                <span class="duration"><?php echo $course['duration']; ?></span>
                            </div>
                            <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                            <p class="course-description"><?php echo htmlspecialchars($course['description']); ?></p>
                            <div class="course-instructor">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($course['instructor']); ?></span>
                            </div>
                            <div class="course-footer">
                                <div class="course-price">
                                    <?php if ($course['price'] > 0): ?>
                                        <span class="price">$<?php echo number_format($course['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="free">Free</span>
                                    <?php endif; ?>
                                </div>
                                <div class="course-actions">
                                    <?php if ($isLoggedIn): ?>
                                        <button class="btn enroll-btn" onclick="enrollCourse(<?php echo $course['id']; ?>)">
                                            Enroll Now
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php" class="btn">Login to Enroll</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div class="load-more-section">
                <button id="loadMoreBtn" class="btn secondary">Load More Courses</button>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Learning?</h2>
                <p>Join thousands of students and advance your career with our expert-led courses</p>
                <?php if (!$isLoggedIn): ?>
                    <a href="signup.php" class="btn primary">Get Started Free</a>
                <?php else: ?>
                    <a href="profile.php" class="btn primary">View My Courses</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

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
        showToast("Please login to bookmark courses", 'warning');
        return;
    }
    
    // TODO: Implement bookmark functionality with backend
    console.log("Bookmarking course:", courseId);
    showToast("Bookmark functionality will be implemented with backend integration", 'info');
}

function enrollCourse(courseId) {
    if (!window.userAuth.isLoggedIn) {
        window.location.href = "login.php";
        return;
    }
    
    // TODO: Implement enrollment functionality with backend
    console.log("Enrolling in course:", courseId);
    showToast("Enrollment functionality will be implemented with backend integration", 'info');
}

// Search and filter functionality
document.getElementById("courseSearch").addEventListener("input", function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filterCourses();
});

document.getElementById("levelFilter").addEventListener("change", filterCourses);
document.getElementById("categoryFilter").addEventListener("change", filterCourses);
document.getElementById("priceFilter").addEventListener("change", filterCourses);

function filterCourses() {
    const searchTerm = document.getElementById("courseSearch").value.toLowerCase();
    const levelFilter = document.getElementById("levelFilter").value;
    const courses = document.querySelectorAll(".course-card");

    courses.forEach(course => {
        const title = course.querySelector(".course-title").textContent.toLowerCase();
        const description = course.querySelector(".course-description").textContent.toLowerCase();
        const level = course.dataset.level;

        const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
        const matchesLevel = !levelFilter || level === levelFilter;

        if (matchesSearch && matchesLevel) {
            course.style.display = "block";
        } else {
            course.style.display = "none";
        }
    });
}

// Load more functionality (placeholder)
document.getElementById("loadMoreBtn").addEventListener("click", function() {
    showToast("Load more functionality will be implemented with backend integration", 'info');
});
';

// Include footer
include './includes/footer.php';
?>
