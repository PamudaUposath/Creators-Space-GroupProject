<?php
// frontend/index.php
// Set page-specific variables for header.php
$pageTitle = 'Home';
$pageDescription = 'Creators-Space - Welcome to the future of tech learning...';
$additionalCSS = ['./src/css/index.css']; // Include index-specific styles

// Get database connection and platform statistics
require_once __DIR__ . '/../backend/config/db_connect.php';
require_once __DIR__ . '/../backend/lib/helpers.php';

// Fetch real statistics from database
$platformStats = getPlatformStatistics($pdo);

// Fetch featured courses from database
$featuredCourses = getFeaturedCourses($pdo, 3);

// Include the header
include_once './includes/header.php';
?>

<!-- Session Message Display -->
<?php if (isset($_SESSION['message']) && $_SESSION['message']): ?>
  <div id="sessionMessage" style="background: #d4edda; color: #155724; padding: 10px; text-align: center; border-bottom: 1px solid #c3e6cb;">
    <?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
  </div>
  <script>
    // Auto-hide message after 5 seconds
    setTimeout(function() {
      const msg = document.getElementById('sessionMessage');
      if (msg) msg.style.display = 'none';
    }, 5000);
  </script>
<?php endif; ?>

<!-- Scroll Progress Indicator -->
<div class="scroll-indicator" id="scrollIndicator"></div>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="hero-content-centered">
      <h1 class="hero-title">Welcome to the Future of <span class="highlight">Tech Learning</span></h1>
      <p class="hero-description">
        Join thousands of learners on their journey to master cutting-edge technologies.
        From web development to data science, we provide hands-on courses designed by industry experts.
      </p>
      <div class="hero-actions">
        <?php if (!$isLoggedIn): ?>
          <a href="courses.php" class="hero-btn">
            <i class="fas fa-rocket"></i> Start Learning
          </a>
          <a href="signup.php" class="hero-btn secondary">
            <i class="fas fa-user-plus"></i> Join Now
          </a>
        <?php else: ?>
          <a href="courses.php" class="hero-btn">
            <i class="fas fa-book-open"></i> Browse Courses
          </a>
          <a href="projects.php" class="hero-btn secondary">
            <i class="fas fa-code"></i> Explore Projects
          </a>
        <?php endif; ?>
      </div>
      
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-item">
          <span class="stat-number"><?php echo htmlspecialchars($platformStats['students_display']); ?></span>
          <span class="stat-label">Active Learners</span>
        </div>
        <div class="stat-item">
          <span class="stat-number"><?php echo htmlspecialchars($platformStats['courses_display']); ?></span>
          <span class="stat-label">Expert Courses</span>
        </div>
        <div class="stat-item">
          <span class="stat-number"><?php echo htmlspecialchars($platformStats['success_rate_display']); ?></span>
          <span class="stat-label">Success Rate</span>
        </div>
        <!-- <div class="stat-item">
          <span class="stat-number">24/7</span>
          <span class="stat-label">Support</span>
        </div> -->
      </div>
    </div>
  </div>
</section>

<!-- Main Content Sections would go here -->
<!-- You can add more sections like Features, Testimonials, etc. -->

<!-- Featured Courses Section -->
<section class="featured-courses-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Featured Courses</h2>
      <p class="section-subtitle">Discover our most popular courses designed by industry experts</p>
    </div>
    <div class="courses-grid">
      <?php if (!empty($featuredCourses)): ?>
        <?php foreach ($featuredCourses as $course): ?>
          <div class="course-card" onclick="window.location.href='course-detail.php?id=<?php echo $course['id']; ?>'">
            <div class="course-image">
              <img src="<?php echo htmlspecialchars($course['image_url']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>">
              <div class="course-badge"><?php echo htmlspecialchars($course['badge']); ?></div>
            </div>
            <div class="course-content">
              <h3><?php echo htmlspecialchars($course['title']); ?></h3>
              <p><?php echo htmlspecialchars(substr($course['description'], 0, 120)) . (strlen($course['description']) > 120 ? '...' : ''); ?></p>
              <div class="course-meta">
                <span class="course-duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($course['duration'] ?: 'Self-paced'); ?></span>
                <span class="course-level"><i class="fas fa-signal"></i> <?php echo htmlspecialchars($course['level_display']); ?></span>
              </div>
              <div class="course-footer">
                <div class="course-rating">
                  <div class="stars">
                    <?php 
                    $rating = floatval($course['rating']);
                    $fullStars = floor($rating);
                    $halfStar = ($rating - $fullStars) >= 0.5;
                    
                    for ($i = 0; $i < $fullStars; $i++): ?>
                      <i class="fas fa-star"></i>
                    <?php endfor;
                    
                    if ($halfStar): ?>
                      <i class="fas fa-star-half-alt"></i>
                    <?php endif;
                    
                    for ($i = $fullStars + ($halfStar ? 1 : 0); $i < 5; $i++): ?>
                      <i class="far fa-star"></i>
                    <?php endfor; ?>
                  </div>
                  <span><?php echo $course['rating']; ?> (<?php echo number_format($course['review_count']); ?> reviews)</span>
                </div>
                <a href="course-detail.php?id=<?php echo $course['id']; ?>" class="course-btn" onclick="event.stopPropagation();">Enroll Now</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback content if no courses are available -->
        <div class="course-card">
          <div class="course-image">
            <img src="./assets/images/full-stack-web-developer.png" alt="Coming Soon">
            <div class="course-badge">Coming Soon</div>
          </div>
          <div class="course-content">
            <h3>Exciting Courses Coming Soon!</h3>
            <p>We're preparing amazing courses for you. Check back soon for updates.</p>
            <div class="course-meta">
              <span class="course-duration"><i class="fas fa-clock"></i> Coming Soon</span>
              <span class="course-level"><i class="fas fa-signal"></i> All Levels</span>
            </div>
            <div class="course-footer">
              <div class="course-rating">
                <div class="stars">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
                <span>5.0 (Soon)</span>
              </div>
              <a href="courses.php" class="course-btn">View All Courses</a>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="section-cta">
      <a href="courses.php" class="view-all-btn">View All Courses <i class="fas fa-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- Technologies Section -->
<section class="technologies-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Technologies We Teach</h2>
      <p class="section-subtitle">Master the most in-demand technologies in the industry</p>
    </div>
    <div class="tech-grid">
      <div class="tech-card" data-tech="react">
        <div class="tech-icon">
          <i class="fab fa-react"></i>
        </div>
        <h3>React</h3>
        <p>Build modern UIs</p>
      </div>
      <div class="tech-card" data-tech="python">
        <div class="tech-icon">
          <i class="fab fa-python"></i>
        </div>
        <h3>Python</h3>
        <p>Data Science & AI</p>
      </div>
      <div class="tech-card" data-tech="nodejs">
        <div class="tech-icon">
          <i class="fab fa-node-js"></i>
        </div>
        <h3>Node.js</h3>
        <p>Backend Development</p>
      </div>
      <div class="tech-card" data-tech="js">
        <div class="tech-icon">
          <i class="fab fa-js-square"></i>
        </div>
        <h3>JavaScript</h3>
        <p>Full Stack Development</p>
      </div>
      <div class="tech-card" data-tech="database">
        <div class="tech-icon">
          <i class="fas fa-database"></i>
        </div>
        <h3>Databases</h3>
        <p>MySQL, MongoDB</p>
      </div>
      <div class="tech-card" data-tech="cloud">
        <div class="tech-icon">
          <i class="fas fa-cloud"></i>
        </div>
        <h3>Cloud</h3>
        <p>AWS, Azure</p>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">What Our Students Say</h2>
      <p class="section-subtitle">Join thousands of successful learners who transformed their careers</p>
    </div>
    <div class="testimonials-carousel">
      <div class="testimonial-card active">
        <div class="testimonial-content">
          <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
          <p>"Creators-Space transformed my career! The practical approach and expert guidance helped me land my dream job as a Full Stack Developer."</p>
          <div class="testimonial-author">
            <img src="./assets/images/anurag-v.jpg" alt="Anurag Vishwakarma">
            <div class="author-info">
              <h4>Anurag Vishwakarma</h4>
              <span>Full Stack Developer at TechCorp</span>
            </div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-content">
          <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
          <p>"The Python Data Science course exceeded my expectations. Within 6 months, I transitioned from marketing to data analysis!"</p>
          <div class="testimonial-author">
            <img src="./assets/images/sumit-r.jpg" alt="Sumit Rajput">
            <div class="author-info">
              <h4>Sumit Rajput</h4>
              <span>Data Analyst at DataTech Solutions</span>
            </div>
          </div>
        </div>
      </div>
      <div class="testimonial-card">
        <div class="testimonial-content">
          <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
          <p>"Amazing community and support! The instructors are always available to help, and the project-based learning is incredibly effective."</p>
          <div class="testimonial-author">
            <img src="./assets/images/userIcon_Square.png" alt="Priya Sharma">
            <div class="author-info">
              <h4>Priya Sharma</h4>
              <span>UI/UX Designer at Creative Studios</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="testimonial-controls">
      <button class="testimonial-btn prev" onclick="changeTestimonial(-1)"><i class="fas fa-chevron-left"></i></button>
      <div class="testimonial-dots">
        <span class="dot active" onclick="currentTestimonial(1)"></span>
        <span class="dot" onclick="currentTestimonial(2)"></span>
        <span class="dot" onclick="currentTestimonial(3)"></span>
      </div>
      <button class="testimonial-btn next" onclick="changeTestimonial(1)"><i class="fas fa-chevron-right"></i></button>
    </div>
  </div>
</section>

<!-- Include Footer -->
<?php include_once './includes/footer.php'; ?>

<!-- Page-specific JavaScript -->
<script>
  // Scroll progress indicator
  window.addEventListener('scroll', function() {
    const scrollIndicator = document.getElementById('scrollIndicator');
    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercentage = (scrollTop / scrollHeight) * 100;
    scrollIndicator.style.width = scrollPercentage + '%';
  });
  
  // Add some interactive effects
  document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on scroll
    const observerOptions = {
      threshold: 0.5,
      rootMargin: '0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.transform = 'translateY(0)';
          entry.target.style.opacity = '1';
        }
      });
    }, observerOptions);
    
    // Observe stat items
    document.querySelectorAll('.stat-item').forEach(item => {
      item.style.transform = 'translateY(20px)';
      item.style.opacity = '0';
      item.style.transition = 'all 0.6s ease';
      observer.observe(item);
    });

    // Observe other animated elements
    document.querySelectorAll('.course-card, .tech-card, .testimonial-card').forEach(item => {
      item.style.transform = 'translateY(30px)';
      item.style.opacity = '0';
      item.style.transition = 'all 0.8s ease';
      observer.observe(item);
    });

    // Testimonials Carousel - Initialize after DOM is loaded
    let currentTestimonialIndex = 0;
    const testimonials = document.querySelectorAll('.testimonial-card');
    const dots = document.querySelectorAll('.dot');

    // Only initialize if testimonials exist
    if (testimonials.length > 0) {
      function showTestimonial(index) {
        testimonials.forEach((testimonial, i) => {
          testimonial.classList.toggle('active', i === index);
        });
        if (dots.length > 0) {
          dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
          });
        }
      }

      // Make functions global so they can be called from HTML
      window.changeTestimonial = function(direction) {
        currentTestimonialIndex += direction;
        if (currentTestimonialIndex >= testimonials.length) {
          currentTestimonialIndex = 0;
        } else if (currentTestimonialIndex < 0) {
          currentTestimonialIndex = testimonials.length - 1;
        }
        showTestimonial(currentTestimonialIndex);
      };

      window.currentTestimonial = function(index) {
        currentTestimonialIndex = index - 1;
        showTestimonial(currentTestimonialIndex);
      };

      // Auto-rotate testimonials every 6 seconds
      setInterval(() => {
        window.changeTestimonial(1);
      }, 6000);

      // Initialize first testimonial
      showTestimonial(0);
    }

    // Tech cards hover effects
    document.querySelectorAll('.tech-card').forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-10px) scale(1.05)';
      });
      
      card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
      });
    });
  });
</script>