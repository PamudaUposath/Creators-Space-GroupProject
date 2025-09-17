<?php
// Set page-specific variables
$pageTitle = "Services";
$pageDescription = "Discover our comprehensive services including consulting, training, and development solutions.";
$additionalCSS = ['./src/css/services.css'];
$additionalJS = ['./src/js/services.js'];

// Include header
include './includes/header.php';
?>
    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Our Professional Services</h1>
            <p class="page-subtitle">Empowering your journey with comprehensive career and technical guidance.</p>
        </div>

        <!-- Services Cards -->
        <section class="section">
            <div class="services-container" id="services-list">
                <p style="text-align: center; color: rgba(255,255,255,0.8); font-size: 1.1rem;">Loading services...</p>
            </div>
        </section>
    </div>

<?php
// Include footer
include './includes/footer.php';
?>
    <script src="./src/js/mobile-responsive.js"></script>

    <!-- Loading Services dynamically from data/services.json -->
    <script>
    async function loadServices() {
        const container = document.getElementById("services-list");
        try {
            const res = await fetch("./src/data/services.json");
            if (!res.ok) throw new Error("Failed to fetch");

            const services = await res.json();
            container.innerHTML = "";

            services.forEach((item) => {
                const featuresHTML = item.features
                    .map(feature => `<li>${feature}</li>`)
                    .join("");

                const card = document.createElement("div");
                card.className = "card";
                card.innerHTML = `
                    <div class="card-content">
                        <img src="${item.image}" alt="${item.title}" />
                        <h3>${item.title}</h3>
                        <p class="service-description">${item.description}</p>
                        <div class="price">₹${item.price}</div>
                        <div class="features">
                            <h4>What's Included:</h4>
                            <ul>${featuresHTML}</ul>
                        </div>
                        <div class="card-buttons">
                            <button class="button toggle-btn">View More</button>
                            <a href="${item.bookingLink}" class="button">Book Now</a>
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
            container.innerHTML = "<p style='color:red;text-align:center;font-size:20px;'>Couldn't load the services.</p>";
            console.error("Error loading services:", err);
        }
    }

    // Load services when page loads
    window.addEventListener("DOMContentLoaded", loadServices);
';

// Include footer
include './includes/footer.php';
?>
