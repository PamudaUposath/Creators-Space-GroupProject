<?php
// Set page-specific variables
$pageTitle = "Blog";
$pageDescription = "Read our latest articles and insights about technology, learning, and innovation.";
$additionalCSS = ['./src/css/blog.css'];
$bodyClass = "overflow-x-hidden";

// Include header
include './includes/header.php';
?>
    <!-- Hero Section with Animation -->
    <div class="main">
        <h1>Tech Insights & Tutorials</h1>
        <p>
            Stay ahead of the curve with our latest articles on web development, programming tutorials, career advice,
            and industry insights. Learn from experts and join the conversation that's shaping the future of technology.
        </p>
    </div>

    <main class="margin-top-3 padding-inline-lg">
        <!-- Blog Posts Section -->
        <section class="blog-section">
            <div class="content-container">
                <div class="blog-filters">
                    <button class="filter-btn active" data-category="all">All Posts</button>
                    <button class="filter-btn" data-category="tutorials">Tutorials</button>
                    <button class="filter-btn" data-category="career">Career</button>
                    <button class="filter-btn" data-category="tech">Tech News</button>
                    <button class="filter-btn" data-category="projects">Projects</button>
                </div>

                <div class="blog-grid" id="blogGrid">
                    <!-- Featured Blog Post -->
                    <article class="blog-card featured" data-category="tutorials">
                        <div class="blog-image">
                            <img src="./assets/images/blogpage/jsin30days.png" alt="JavaScript in 30 Days">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Tutorial</span>
                            <h2>Master JavaScript in 30 Days</h2>
                            <p>A comprehensive guide to learning JavaScript from basics to advanced concepts. Perfect for beginners and intermediate developers looking to strengthen their skills.</p>
                            <div class="blog-meta">
                                <span class="author">By John Doe</span>
                                <span class="date">Dec 15, 2024</span>
                                <span class="read-time">8 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>

                    <!-- Regular Blog Posts -->
                    <article class="blog-card" data-category="career">
                        <div class="blog-image">
                            <img src="./assets/images/blogpage/ui-ux.jpeg" alt="UI/UX Career">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Career</span>
                            <h3>Breaking into UI/UX Design</h3>
                            <p>Essential tips and resources for starting your career in UI/UX design, including portfolio building and skill development.</p>
                            <div class="blog-meta">
                                <span class="author">By Sarah Johnson</span>
                                <span class="date">Dec 12, 2024</span>
                                <span class="read-time">5 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>

                    <article class="blog-card" data-category="tech">
                        <div class="blog-image">
                            <img src="./assets/images/blogpage/techstartup.jpeg" alt="Tech Startup">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Tech News</span>
                            <h3>The Future of Tech Startups</h3>
                            <p>Exploring emerging trends and technologies that are shaping the startup ecosystem in 2024 and beyond.</p>
                            <div class="blog-meta">
                                <span class="author">By Mike Chen</span>
                                <span class="date">Dec 10, 2024</span>
                                <span class="read-time">6 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>

                    <article class="blog-card" data-category="projects">
                        <div class="blog-image">
                            <img src="./assets/images/full-stack-web-developer.png" alt="Full Stack Project">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Projects</span>
                            <h3>Building a Full-Stack E-commerce App</h3>
                            <p>Step-by-step guide to creating a complete e-commerce application using React, Node.js, and MongoDB.</p>
                            <div class="blog-meta">
                                <span class="author">By Alex Rivera</span>
                                <span class="date">Dec 8, 2024</span>
                                <span class="read-time">12 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>

                    <article class="blog-card" data-category="tutorials">
                        <div class="blog-image">
                            <img src="./assets/images/webdev.png" alt="Web Development">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Tutorial</span>
                            <h3>Modern CSS Grid and Flexbox</h3>
                            <p>Master responsive layouts with CSS Grid and Flexbox. Learn when to use each and how to combine them effectively.</p>
                            <div class="blog-meta">
                                <span class="author">By Emma Wilson</span>
                                <span class="date">Dec 5, 2024</span>
                                <span class="read-time">7 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>

                    <article class="blog-card" data-category="career">
                        <div class="blog-image">
                            <img src="./assets/images/blogpage/uiux.jpeg" alt="Tech Interview">
                        </div>
                        <div class="blog-content">
                            <span class="blog-category">Career</span>
                            <h3>Acing Your Tech Interview</h3>
                            <p>Comprehensive preparation guide for technical interviews, including coding challenges and behavioral questions.</p>
                            <div class="blog-meta">
                                <span class="author">By David Park</span>
                                <span class="date">Dec 3, 2024</span>
                                <span class="read-time">9 min read</span>
                            </div>
                            <a href="#" class="read-more-btn">Read More</a>
                        </div>
                    </article>
                </div>

                <!-- Load More Section -->
                <div class="load-more-section">
                    <button class="btn btn-secondary" id="loadMoreBtn">Load More Posts</button>
                </div>
            </div>
        </section>
    </main>

<?php
// Set custom JS for this page
$customJS = '
// Blog filtering functionality
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll(".filter-btn");
    const blogCards = document.querySelectorAll(".blog-card");
    const loadMoreBtn = document.getElementById("loadMoreBtn");

    // Filter functionality
    filterBtns.forEach(btn => {
        btn.addEventListener("click", function() {
            const category = this.getAttribute("data-category");
            
            // Update active filter button
            filterBtns.forEach(b => b.classList.remove("active"));
            this.classList.add("active");
            
            // Filter blog cards
            blogCards.forEach(card => {
                if (category === "all" || card.getAttribute("data-category") === category) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    });

    // Load more functionality (placeholder)
    loadMoreBtn.addEventListener("click", function() {
        this.textContent = "Loading...";
        setTimeout(() => {
            this.textContent = "No more posts to load";
            this.disabled = true;
        }, 1000);
    });
});
';

// Include footer
include './includes/footer.php';
?>
