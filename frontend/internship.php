<?php
// Set page-specific variables
$pageTitle = "Internship";
$pageDescription = "Explore exciting internship opportunities with Creators-Space";
$additionalCSS = ['./src/css/internship.css', './src/css/courses.css'];
$additionalJS = ['./src/js/internship.js'];

// Include header
include './includes/header.php';
?>
    <!-- Dark Mode Toggle -->
    <div class="toggle-switch">
        <label class="switch-label">
            <input type="checkbox" class="checkbox" id="dark-mode-toggle" />
            <span class="slider"></span>
        </label>
    </div>

    <!-- MAIN SECTION -->
    <section class="main">
        <h1>Exciting Internship Opportunities</h1>
        <p>Launch your career with hands-on experience from top organizations.</p>
    </section>

    <!-- INTERNSHIP CARDS -->
    <div class="courses-container" id="internship-list">
        <p>Loading internships...</p>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="social">
            <h1><img width="80px" src="./assets/images/logo.png" alt="logo Creators-Space">
              <br>Creators-Space</h1>
            <h3>Social Media</h3>
            <div class="all-social-links">
                <a target="_blank" href="https://linkedin.com/in/anuragvishwakarma">
                    <svg xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" viewBox="0 0 100 100" id="linkedin">
                        <path d="M55.35,44.17h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11Zm0,0h.07v-.11ZM50.8,3.77A45.67,45.67,0,1,0,96.48,49.44,45.73,45.73,0,0,0,50.8,3.77ZM36.65,77.77H25.51V40.26H36.65ZM31.08,35.4A6.45,6.45,0,1,1,37.52,29,6.44,6.44,0,0,1,31.08,35.4ZM79.77,77.77H68.63V59.36c0-4.15-.08-9.49-5.78-9.49s-6.67,4.52-6.67,9.19V77.77H45.05V40.26H55.7v5.17h.16a11.69,11.69,0,0,1,10.53-5.78c11.27,0,13.34,7.42,13.34,17.06Z"></path>
                    </svg>
                </a>
                <a target="_blank" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" data-name="Instagram w/circle" viewBox="0 0 19.2 19.2" id="instagram">
                        <path d="M13.498 6.651a1.656 1.656 0 0 0-.95-.949 2.766 2.766 0 0 0-.928-.172c-.527-.024-.685-.03-2.02-.03s-1.493.006-2.02.03a2.766 2.766 0 0 0-.929.172 1.656 1.656 0 0 0-.949.95 2.766 2.766 0 0 0-.172.928c-.024.527-.03.685-.03 2.02s.006 1.493.03 2.02a2.766 2.766 0 0 0 .172.929 1.656 1.656 0 0 0 .95.949 2.766 2.766 0 0 0 .928.172c.527.024.685.03 2.02.03s1.493-.006 2.02-.03a2.766 2.766 0 0 0 .929-.172 1.656 1.656 0 0 0 .949-.95 2.766 2.766 0 0 0 .172-.928c.024-.527.03-.685.03-2.02s-.006-1.493-.03-2.02a2.766 2.766 0 0 0-.172-.929zM9.6 12.819A3.219 3.219 0 1 1 12.819 9.6 3.219 3.219 0 0 1 9.6 12.819zm3.346-5.814a.752.752 0 1 1 .752-.752.752.752 0 0 1-.752.752z"></path>
                        <circle cx="9.6" cy="9.6" r="2.086"></circle>
                        <path d="M9.6 0a9.6 9.6 0 1 0 9.6 9.6A9.6 9.6 0 0 0 9.6 0zM15.681 11.672a3.89 3.89 0 0 1-.246 1.288 2.73 2.73 0 0 1-1.561 1.561 3.89 3.89 0 0 1-1.288.246c-.534.024-.7.031-2.086.031s-1.552-.007-2.086-.031a3.89 3.89 0 0 1-1.288-.246 2.73 2.73 0 0 1-1.561-1.561 3.89 3.89 0 0 1-.246-1.288C5.295 11.138 5.288 10.972 5.288 9.586s.007-1.552.031-2.086a3.89 3.89 0 0 1 .246-1.288A2.73 2.73 0 0 1 7.126 4.651a3.89 3.89 0 0 1 1.288-.246C8.948 4.381 9.114 4.374 10.5 4.374s1.552.007 2.086.031a3.89 3.89 0 0 1 1.288.246 2.73 2.73 0 0 1 1.561 1.561 3.89 3.89 0 0 1 .246 1.288c.024.534.031.7.031 2.086s-.007 1.552-.031 2.086z"></path>
                    </svg>
                </a>
                <a target="_blank" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" fill="none" viewBox="0 0 512 512" id="twitter">
                        <g clip-path="url(#clip0_84_15697)">
                            <rect width="512" height="512" fill="#000" rx="60"></rect>
                            <path fill="#fff" d="M355.904 100H408.832L293.2 232.16L429.232 412H322.72L239.296 302.928L143.84 412H90.8805L214.56 270.64L84.0645 100H193.28L268.688 199.696L355.904 100ZM337.328 380.32H366.656L177.344 130.016H145.872L337.328 380.32Z"></path>
                        </g>
                        <defs>
                            <clipPath id="clip0_84_15697">
                                <rect width="512" height="512" fill="#fff"></rect>
                            </clipPath>
                        </defs>
                    </svg>
                </a>
            </div>
        </div>
       
        <div class="contact">
            <h3>Contact Us</h3>
            <p><a href="mailto:21brac0401@polygwalior.ac.in"><i class="fa-solid fa-envelope" id="envelope-icon"></i>21brac0401@polygwalior.ac.in</a></p>
            <br>
            <p><a href="tel:+9188xxxxxx89"><i class="fa-solid fa-phone" id="call-icon"></i>+91 88xxxxxx89</a></p>
        </div>

        <div class="form">
            <h3>Get In Touch</h3>
            <input type="text" placeholder="Your name">
            <input type="email" placeholder="Your email">
            <textarea type="text" placeholder="Your message"></textarea>
            <button>Send</button>
        </div>
        <div class="copy">
            <p>Copyright &copy; 2024 - 2025 Creators-Space. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="./src/js/navbar.js"></script>
    <script src="./src/js/internship.js"></script>
    <script src="./src/js/utils.js"></script>
    <script src="./src/js/mobile-responsive.js"></script>

    <!-- Loading Internship dynamically from data/internship.json -->
    <script>
    async function loadInternships() {
        const container = document.getElementById("internship-list");
        try {
            const res = await fetch("./src/data/internship.json");
            if (!res.ok) throw new Error("Failed to fetch");

            const internships = await res.json();
            container.innerHTML = "";

            internships.forEach((item) => {
                const responsibilitiesHTML = item.responsibilities
                    .map(task => `<li>${task}</li>`)
                    .join("");

                const card = document.createElement("div");
                card.className = "card";
                card.innerHTML = `
                    <div class="card-content">
                        <img src="${item.image}" alt="${item.title}" />
                        <h3>${item.title}</h3>
                        <p>Company: ${item.company}</p>
                        <p>Duration: ${item.duration} | Location: ${item.type}</p>
                        <p>Stipend: ${item.stipend}</p>
                        <div class="card-buttons">
                            <button class="button toggle-btn">View More</button>
                            <a href="${item.applyLink}" class="button">Apply Now</a>
                        </div>
                        
                        <div class="syllabus">
                            <h4>Responsibilities:</h4>
                            <ul>${responsibilitiesHTML}</ul>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            document.querySelectorAll(".toggle-btn").forEach((btn) => {
                btn.addEventListener("click", () => {
                    const card = btn.closest(".card");
                    card.classList.toggle("expanded");
                    btn.textContent = card.classList.contains("expanded") ? "View Less" : "View More";
                });
            });
        } catch (err) {
            container.innerHTML = "<p style='color:red;text-align:center;font-size:20px;'>Couldn't load the internships.</p>";
            console.error("Error loading internships:", err);
        }
    }

    // Load internships when page loads
    window.addEventListener('DOMContentLoaded', loadInternships);

    // Dark Mode Toggle
    const toggle = document.getElementById('dark-mode-toggle');
    const body = document.body;

    toggle.addEventListener('change', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('darkMode', body.classList.contains('dark-mode'));
    });

    // Load saved dark mode preference
    if (localStorage.getItem('darkMode') === 'true') {
        body.classList.add('dark-mode');
        toggle.checked = true;
    }
';

// Include footer
include './includes/footer.php';
?>
