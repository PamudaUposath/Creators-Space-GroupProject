<?php
// Include database connection
require_once __DIR__ . '/../backend/config/db_connect.php';

// Set page-specific variables
$pageTitle = "Course Details";
$pageDescription = "Learn more about this course and enroll today.";
$additionalCSS = ['./src/css/courses.css', './src/css/course-detail.css'];
$additionalJS = ['./src/js/course-detail.js'];

// Get course ID from URL
$courseId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$courseId) {
    header('Location: courses.php');
    exit;
}

// Function to fetch course details
function getCourseDetails($pdo, $courseId) {
    try {
        $stmt = $pdo->prepare("
            SELECT 
                c.*,
                CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
                u.bio as instructor_bio,
                u.profile_image as instructor_image,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id AND status = 'active') as enrolled_students
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.id = ? AND c.is_active = 1
        ");
        
        $stmt->execute([$courseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching course details: " . $e->getMessage());
        return null;
    }
}

// Function to fetch course lessons
function getCourseLessons($pdo, $courseId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, title, duration, is_free, position
            FROM lessons 
            WHERE course_id = ? AND is_published = 1 
            ORDER BY position ASC
        ");
        
        $stmt->execute([$courseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Error fetching lessons: " . $e->getMessage());
        return [];
    }
}

// Function to check if user is enrolled
function isUserEnrolled($pdo, $userId, $courseId) {
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM enrollments 
            WHERE user_id = ? AND course_id = ? AND status = 'active'
        ");
        
        $stmt->execute([$userId, $courseId]);
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        return false;
    }
}

// Fetch course details
$course = getCourseDetails($pdo, $courseId);

if (!$course) {
    header('Location: courses.php');
    exit;
}

// Update page title
$pageTitle = $course['title'];
$pageDescription = substr($course['description'], 0, 160);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isEnrolled = false;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $isEnrolled = isUserEnrolled($pdo, $userId, $courseId);
}

// Fetch lessons
$lessons = getCourseLessons($pdo, $courseId);

// Include header
include './includes/header.php';
?>

<div class="course-detail-container">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <div class="container">
            <nav>
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="courses.php">Courses</a>
                <i class="fas fa-chevron-right"></i>
                <span><?php echo htmlspecialchars($course['title']); ?></span>
            </nav>
        </div>
    </div>

    <!-- Course Hero Section -->
    <div class="course-hero">
        <div class="container">
            <div class="course-hero-content">
                <div class="course-image-section">
                    <div class="course-main-image">
                        <img src="<?php echo htmlspecialchars($course['image_url'] ?: './assets/images/full-stack-web-developer.png'); ?>" 
                             alt="<?php echo htmlspecialchars($course['title']); ?>">
                    </div>
                </div>
                
                <div class="course-info-section">
                    <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-user"></i>
                            <span>Instructor: <?php echo htmlspecialchars($course['instructor_name']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Duration: <?php echo htmlspecialchars($course['duration']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-signal"></i>
                            <span>Level: <?php echo ucfirst($course['level']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo number_format($course['enrolled_students']); ?> students enrolled</span>
                        </div>
                    </div>
                    
                    <div class="course-price">
                        <?php if ($course['price'] > 0): ?>
                            <span class="price">$<?php echo number_format($course['price'], 2); ?></span>
                        <?php else: ?>
                            <span class="price free">FREE</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-actions">
                        <?php if ($isLoggedIn): ?>
                            <?php if ($isEnrolled): ?>
                                <a href="mycourses.php" class="btn btn-success btn-large">
                                    <i class="fas fa-play"></i> Continue Learning
                                </a>
                            <?php else: ?>
                                <button class="btn btn-primary btn-large add-to-cart-btn" 
                                        id="addToCartBtn"
                                        data-course-id="<?php echo $course['id']; ?>"
                                        data-course-name="<?php echo htmlspecialchars($course['title']); ?>"
                                        data-course-price="<?php echo $course['price']; ?>"
                                        data-course-image="<?php echo htmlspecialchars($course['image_url']); ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                <?php if ($course['price'] == 0): ?>
                                <button class="btn btn-success btn-large enroll-btn" 
                                        id="enrollBtn"
                                        data-course-id="<?php echo $course['id']; ?>">
                                    <i class="fas fa-graduation-cap"></i> Enroll for Free
                                </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-large">
                                <i class="fas fa-sign-in-alt"></i> Login to Enroll
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="course-features">
                        <h4>What's Included:</h4>
                        <ul>
                            <li><i class="fas fa-video"></i> <?php echo count($lessons); ?> video lessons</li>
                            <li><i class="fas fa-clock"></i> <?php echo $course['duration']; ?> of content</li>
                            <li><i class="fas fa-certificate"></i> Certificate of completion</li>
                            <li><i class="fas fa-mobile-alt"></i> Access on mobile and desktop</li>
                            <li><i class="fas fa-infinity"></i> Lifetime access</li>
                            <li><i class="fas fa-comments"></i> Community support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Content Tabs -->
    <div class="course-content">
        <div class="container">
            <!-- Navigation Tabs -->
            <div class="course-tabs">
                <button class="tab-btn active" data-tab="overview">Overview</button>
                <button class="tab-btn" data-tab="curriculum">Curriculum</button>
                <button class="tab-btn" data-tab="instructor">Instructor</button>
                <button class="tab-btn" data-tab="reviews">Reviews</button>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Overview Tab -->
                <div class="tab-pane active" id="overview">
                    <div class="overview-content">
                        <h3>Course Description</h3>
                        <p><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                        
                        <?php if ($course['prerequisites']): ?>
                        <h3>Prerequisites</h3>
                        <p><?php echo nl2br(htmlspecialchars($course['prerequisites'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($course['learning_objectives']): ?>
                        <h3>What You'll Learn</h3>
                        <div class="learning-objectives">
                            <?php 
                            $objectives = explode("\n", $course['learning_objectives']);
                            foreach ($objectives as $objective): 
                                if (trim($objective)): ?>
                                <div class="objective-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span><?php echo htmlspecialchars(trim($objective)); ?></span>
                                </div>
                            <?php 
                                endif;
                            endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Curriculum Tab -->
                <div class="tab-pane" id="curriculum">
                    <div class="curriculum-content">
                        <h3>Course Curriculum</h3>
                        <div class="curriculum-stats">
                            <span><i class="fas fa-play-circle"></i> <?php echo count($lessons); ?> lessons</span>
                            <span><i class="fas fa-clock"></i> <?php echo $course['duration']; ?> total</span>
                        </div>
                        
                        <?php if (!empty($lessons)): ?>
                        <div class="lessons-list">
                            <?php foreach ($lessons as $index => $lesson): ?>
                            <div class="lesson-item">
                                <div class="lesson-number"><?php echo $index + 1; ?></div>
                                <div class="lesson-content">
                                    <h4><?php echo htmlspecialchars($lesson['title']); ?></h4>
                                    <div class="lesson-meta">
                                        <span class="duration">
                                            <i class="fas fa-clock"></i> <?php echo $lesson['duration'] ?: '5 min'; ?>
                                        </span>
                                        <?php if ($lesson['is_free']): ?>
                                        <span class="free-preview">
                                            <i class="fas fa-unlock"></i> Free Preview
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="lesson-action">
                                    <?php if ($lesson['is_free'] || $isEnrolled): ?>
                                    <button class="play-btn" onclick="playLesson(<?php echo $lesson['id']; ?>)">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <?php else: ?>
                                    <i class="fas fa-lock"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p class="no-lessons">Curriculum details will be available soon.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Instructor Tab -->
                <div class="tab-pane" id="instructor">
                    <div class="instructor-content">
                        <div class="instructor-profile">
                            <div class="instructor-avatar">
                                <?php if ($course['instructor_image']): ?>
                                <img src="<?php echo htmlspecialchars($course['instructor_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($course['instructor_name']); ?>">
                                <?php else: ?>
                                <div class="avatar-placeholder">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="instructor-info">
                                <h3><?php echo htmlspecialchars($course['instructor_name']); ?></h3>
                                <p class="instructor-title">Course Instructor</p>
                                <?php if ($course['instructor_bio']): ?>
                                <p class="instructor-bio"><?php echo nl2br(htmlspecialchars($course['instructor_bio'])); ?></p>
                                <?php else: ?>
                                <p class="instructor-bio">Experienced instructor passionate about sharing knowledge and helping students succeed in their learning journey.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Reviews Tab -->
                <div class="tab-pane" id="reviews">
                    <div class="reviews-content">
                        <h3>Student Reviews</h3>
                        <div class="reviews-placeholder">
                            <i class="fas fa-star-o"></i>
                            <p>No reviews yet. Be the first to review this course after enrollment!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Notification -->
<div id="notification" class="notification" style="display: none;">
    <div class="notification-content">
        <i class="fas fa-check-circle"></i>
        <span class="notification-text">Course added to cart successfully!</span>
    </div>
</div>

<!-- JavaScript Debug Test -->
<script>
console.log('=== JAVASCRIPT DEBUG TEST ===');
console.log('Page loaded successfully');
console.log('Testing if Add to Cart button exists...');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    const addToCartBtn = document.getElementById('addToCartBtn');
    console.log('Add to Cart button found:', addToCartBtn ? 'YES' : 'NO');
    
    if (addToCartBtn) {
        console.log('Button data attributes:', {
            courseId: addToCartBtn.getAttribute('data-course-id'),
            courseName: addToCartBtn.getAttribute('data-course-name'),
            coursePrice: addToCartBtn.getAttribute('data-course-price')
        });
        
        // Add the actual cart functionality directly
        addToCartBtn.addEventListener('click', function() {
            console.log('=== BUTTON CLICKED ===');
            const courseId = this.getAttribute('data-course-id');
            console.log('Course ID:', courseId);
            addToCart(courseId);
        });
    }
});

// Add the actual addToCart function
async function addToCart(courseId) {
    console.log('addToCart function called with courseId:', courseId);
    
    if (!courseId) {
        console.error('No course ID provided');
        alert('Error: Course ID not found');
        return;
    }

    // Show loading state
    const addToCartBtn = document.getElementById('addToCartBtn');
    if (!addToCartBtn) {
        console.error('Add to cart button not found');
        return;
    }
    
    const originalText = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    addToCartBtn.disabled = true;

    try {
        console.log('Making API request to add course to cart...');
        const response = await fetch('../backend/api/cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                course_id: parseInt(courseId),
                quantity: 1
            })
        });

        console.log('Response received:', response.status, response.statusText);

        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);
        
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Non-JSON response received:', textResponse);
            throw new Error('Invalid response from server');
        }

        const data = await response.json();
        console.log('Response data:', data);

        if (response.status === 401) {
            alert('Please log in to add courses to your cart');
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
            return;
        }

        if (data.success) {
            alert('SUCCESS: ' + data.message);
            
            // Update cart counter in navbar
            if (typeof window.updateCartCounter === 'function') {
                window.updateCartCounter();
            }
            
            // Optionally update button state
            if (data.action === 'added') {
                addToCartBtn.innerHTML = '<i class="fas fa-check"></i> Added to Cart';
                addToCartBtn.classList.add('added');
            }
        } else {
            alert('ERROR: ' + data.message);
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Network error. Please try again.');
    } finally {
        // Reset button state after delay
        setTimeout(() => {
            addToCartBtn.innerHTML = originalText;
            addToCartBtn.disabled = false;
            addToCartBtn.classList.remove('added');
        }, 2000);
    }
}
</script>

<?php include './includes/footer.php'; ?>