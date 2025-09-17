<?php
// Set page-specific variables
$pageTitle = "Blog";
$pageDescription = "Read our latest articles and insights about technology, learning, and innovation.";
$additionalCSS = ['./src/css/blog.css'];
$bodyClass = "overflow-x-hidden";

// Include header
include './includes/header.php';
?>

    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Tech Insights & Tutorials</h1>
            <p class="page-subtitle">Stay ahead of the curve with our latest articles on web development, programming tutorials, career advice, and industry insights. Learn from experts and join the conversation that's shaping the future of technology.</p>
        </div>

        <!-- Blog Posts Section -->
        <section class="section">
            <div class="blog-filters" style="display: flex; justify-content: center; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
                <button class="btn active" data-category="all">All Posts</button>
                <button class="btn" data-category="tutorials">Tutorials</button>
                <button class="btn" data-category="career">Career</button>
                <button class="btn" data-category="tech">Tech News</button>
                <button class="btn" data-category="projects">Projects</button>
            </div>

            <div class="blog-grid" id="blogGrid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem;">
                <!-- Featured Blog Post -->
                <article class="card featured" data-category="tutorials" style="grid-column: 1 / -1;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
                        <div class="blog-image">
                            <img src="./assets/images/blogpage/jsin30days.png" alt="JavaScript in 30 Days" style="width: 100%; height: 250px; object-fit: cover; border-radius: 15px;">
                        </div>
                        <div class="blog-content">
                            <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Tutorial</span>
                            <h2 style="color: #2c3e50; margin: 1rem 0;">Master JavaScript in 30 Days</h2>
                            <p style="color: #34495e; line-height: 1.6;">A comprehensive guide to learning JavaScript from basics to advanced concepts. Perfect for beginners and intermediate developers looking to strengthen their skills.</p>
                            <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.9rem; color: #7f8c8d;">
                                <span>By John Doe</span>
                                <span>Dec 15, 2024</span>
                                <span>8 min read</span>
                            </div>
                            <a href="#" class="hero-btn" style="display: inline-block; margin-top: 1rem;">Read More</a>
                        </div>
                    </div>
                </article>

                <!-- Regular Blog Posts -->
                <article class="card" data-category="career">
                    <div class="blog-image">
                        <img src="./assets/images/blogpage/ui-ux.jpeg" alt="UI/UX Career" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div class="blog-content">
                        <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Career</span>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">Breaking into UI/UX Design</h3>
                        <p style="color: #34495e; line-height: 1.6;">Essential tips and resources for starting your career in UI/UX design, including portfolio building and skill development.</p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.8rem; color: #7f8c8d;">
                            <span>By Sarah Johnson</span>
                            <span>Dec 12, 2024</span>
                            <span>5 min read</span>
                        </div>
                        <a href="#" class="btn login" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Read More</a>
                    </div>
                </article>

                <article class="card" data-category="tech">
                    <div class="blog-image">
                        <img src="./assets/images/blogpage/techstartup.jpeg" alt="Tech Startup" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div class="blog-content">
                        <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Tech News</span>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">The Future of Tech Startups</h3>
                        <p style="color: #34495e; line-height: 1.6;">Exploring emerging trends and technologies that are shaping the startup ecosystem in 2024 and beyond.</p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.8rem; color: #7f8c8d;">
                            <span>By Mike Chen</span>
                            <span>Dec 10, 2024</span>
                            <span>6 min read</span>
                        </div>
                        <a href="#" class="btn login" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Read More</a>
                    </div>
                </article>

                <article class="card" data-category="projects">
                    <div class="blog-image">
                        <img src="./assets/images/full-stack-web-developer.png" alt="Full Stack Project" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div class="blog-content">
                        <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Projects</span>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">Building a Full-Stack E-commerce App</h3>
                        <p style="color: #34495e; line-height: 1.6;">Step-by-step guide to creating a complete e-commerce application using React, Node.js, and MongoDB.</p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.8rem; color: #7f8c8d;">
                            <span>By Alex Rivera</span>
                            <span>Dec 8, 2024</span>
                            <span>12 min read</span>
                        </div>
                        <a href="#" class="btn login" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Read More</a>
                    </div>
                </article>

                <article class="card" data-category="tutorials">
                    <div class="blog-image">
                        <img src="./assets/images/webdev.png" alt="Web Development" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div class="blog-content">
                        <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Tutorial</span>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">Modern CSS Grid and Flexbox</h3>
                        <p style="color: #34495e; line-height: 1.6;">Master responsive layouts with CSS Grid and Flexbox. Learn when to use each and how to combine them effectively.</p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.8rem; color: #7f8c8d;">
                            <span>By Emma Wilson</span>
                            <span>Dec 5, 2024</span>
                            <span>7 min read</span>
                        </div>
                        <a href="#" class="btn login" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Read More</a>
                    </div>
                </article>

                <article class="card" data-category="career">
                    <div class="blog-image">
                        <img src="./assets/images/blogpage/uiux.jpeg" alt="Tech Interview" style="width: 100%; height: 200px; object-fit: cover; border-radius: 15px; margin-bottom: 1rem;">
                    </div>
                    <div class="blog-content">
                        <span class="modern-gradient-text" style="font-weight: 600; font-size: 0.9rem;">Career</span>
                        <h3 style="color: #2c3e50; margin: 0.5rem 0;">Acing Your Tech Interview</h3>
                        <p style="color: #34495e; line-height: 1.6;">Comprehensive preparation guide for technical interviews, including coding challenges and behavioral questions.</p>
                        <div style="display: flex; gap: 1rem; margin: 1rem 0; font-size: 0.8rem; color: #7f8c8d;">
                            <span>By David Park</span>
                            <span>Dec 3, 2024</span>
                            <span>9 min read</span>
                        </div>
                        <a href="#" class="btn login" style="font-size: 0.8rem; padding: 0.5rem 1rem;">Read More</a>
                    </div>
                </article>
            </div>

            <!-- Load More Section -->
            <div style="text-align: center; margin-top: 3rem;">
                <button class="hero-btn" id="loadMoreBtn">Load More Posts</button>
            </div>
        </section>
    </div>

<?php
// Set custom JS for this page
$customJS = '
// Blog filtering functionality
document.addEventListener("DOMContentLoaded", function() {
    const filterBtns = document.querySelectorAll(".blog-filters .btn");
    const blogCards = document.querySelectorAll("[data-category]");
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
