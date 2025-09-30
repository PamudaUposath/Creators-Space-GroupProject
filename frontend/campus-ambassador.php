<?php
session_start();
include './includes/header.php';
?>
    <!-- Dark Mode Toggle -->
    <div class="toggle-switch">
        <label class="switch-label">
            <input onclick="toggleDarkMode()" type="checkbox" class="checkbox">
            <span class="slider"></span>
        </label>
    </div>

    <!-- Hero Section with Animation -->
    <div class="main">
        <h1>Campus Ambassador Program</h1>
        <p>
            Join the Creators-Space Campus Ambassador Program and become a bridge between innovation and education at
            your institution. As an ambassador, you'll lead initiatives, inspire peers, and shape the future of tech
            learning in your community.
        </p>
    </div>

    <main class="margin-top-3 padding-inline-lg">
        <!-- Benefits Grid Section -->
        <section class="campus-ambassador-section">
            <div class="content-container">
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-icon">📚</div>
                        <h3>Leadership Development</h3>
                        <p>Develop essential leadership skills through real-world experience and mentorship</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🌟</div>
                        <h3>Event Organization</h3>
                        <p>Plan and execute tech events, workshops, and community meetups at your campus</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">💼</div>
                        <h3>Career Opportunities</h3>
                        <p>Build your resume with valuable experience and connect with potential employers</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🚀</div>
                        <h3>Innovation Hub</h3>
                        <p>Create an innovation ecosystem at your institution and drive technological advancement</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🎓</div>
                        <h3>Educational Impact</h3>
                        <p>Help fellow students access quality tech education and career guidance</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🌐</div>
                        <h3>Network Building</h3>
                        <p>Connect with like-minded peers and industry professionals across the globe</p>
                    </div>
                </div>

                <div class="cta-section">
                    <h3>Ready to lead the change?</h3>
                    <p>Join thousands of student leaders who are already making an impact in their communities!</p>
                    <a href="signup.php" class="btn btn-primary">Apply Now</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <div class="footer">
        <div class="social">
            <h1>Creators-Space</h1>
            <div style="display: flex; gap: 10px;">
                <svg fill="currentColor" viewBox="0 0 24 24" width="40" height="40">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                </svg>
                <svg fill="currentColor" viewBox="0 0 24 24" width="40" height="40">
                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                </svg>
            </div>
        </div>

        <div class="contact">
            <h3>Contact Us</h3>
            <p><a href="mailto:21brac0401@polygwalior.ac.in"><i class="fa-solid fa-envelope" id="envelope-icon"></i>21brac0401@polygwalior.ac.in</a></p>
            <br>
            <p><a href="tel:+9188xxxxxx89"><i class="fa-solid fa-phone" id="call-icon"></i>+91 88xxxxxx89</a></p>
        </div>

        <div class="form">
            <h3>Newsletter</h3>
            <input type="email" placeholder="Your email">
            <textarea placeholder="Your message"></textarea>
            <button>Subscribe</button>
        </div>

        <div class="copy">
            <p>&copy; 2024 Creators-Space. All rights reserved.</p>
        </div>
    </div>

    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
        }

        // Load saved dark mode preference
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            document.querySelector('.checkbox').checked = true;
        }
    </script>

<?php
// Include footer
include './includes/footer.php';
?>
