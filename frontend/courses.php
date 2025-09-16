<?php
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
                <?php foreach ($courses as $course): ?>
                    <div class="card course-card" data-level="<?php echo strtolower($course['level']); ?>">
                        <div style="position: relative;">
                            <img src="<?php echo htmlspecialchars($course['image']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                            <?php if ($isLoggedIn): ?>
                                <button onclick="toggleBookmark(<?php echo $course['id']; ?>)" style="position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.9); border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                    <i class="far fa-bookmark"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem; padding: 0.3rem 0.8rem; background: rgba(255,255,255,0.1); border-radius: 15px;"><?php echo $course['level']; ?></span>
                            <span style="color: #7f8c8d; font-size: 0.9rem;"><?php echo $course['duration']; ?></span>
                        </div>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0; font-size: 1.3rem;"><?php echo htmlspecialchars($course['title']); ?></h3>
                        <p style="color: #34495e; line-height: 1.6; margin-bottom: 1rem;"><?php echo htmlspecialchars($course['description']); ?></p>
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
                            <div>
                                <?php if ($isLoggedIn): ?>
                                    <button class="btn login" onclick="enrollCourse(<?php echo $course['id']; ?>)" style="font-size: 0.9rem; padding: 0.6rem 1.2rem;">
                                        Enroll Now
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="btn login" style="font-size: 0.9rem; padding: 0.6rem 1.2rem;">Login to Enroll</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div style="text-align: center; margin-top: 3rem;">
                <button id="loadMoreBtn" class="hero-btn">Load More Courses</button>
            </div>
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
        const title = course.querySelector("h3").textContent.toLowerCase();
        const description = course.querySelector("p").textContent.toLowerCase();
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
    showToast("Load more functionality will be implemented with backend integration", "info");
});
';

// Include footer
include './includes/footer.php';
?>
