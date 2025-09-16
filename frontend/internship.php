<?php
// Set page-specific variables
$pageTitle = "Internship";
$pageDescription = "Explore exciting internship opportunities with Creators-Space";
$additionalCSS = ['./src/css/internship.css', './src/css/courses.css'];

// Include header
include './includes/header.php';
?>

    <!-- Main Content Container -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Exciting Internship Opportunities</h1>
            <p class="page-subtitle">Launch your career with hands-on experience from top organizations.</p>
        </div>

        <!-- Internship Search and Filter -->
        <section class="search-section">
            <div class="container">
                <div class="search-controls">
                    <input type="text" id="search-input" placeholder="Search internships..." class="search-input">
                    <select id="filter-type" class="filter-select">
                        <option value="">All Types</option>
                        <option value="Remote">Remote</option>
                        <option value="On-site">On-site</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                    <select id="filter-duration" class="filter-select">
                        <option value="">All Durations</option>
                        <option value="1-3 months">1-3 months</option>
                        <option value="3-6 months">3-6 months</option>
                        <option value="6+ months">6+ months</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- Internship Cards -->
        <section class="section">
            <div class="container">
                <div class="courses-container" id="internship-list">
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Loading internships...</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Custom JavaScript for Internship Page -->
    <script>
    // Loading Internship dynamically from data/internship.json
    async function loadInternships() {
        const container = document.getElementById("internship-list");
        try {
            const res = await fetch("./src/data/internship.json");
            if (!res.ok) throw new Error("Failed to fetch");

            const internships = await res.json();
            container.innerHTML = "";

            if (internships.length === 0) {
                container.innerHTML = "<p style='text-align:center;color:#666;font-size:1.2rem;'>No internships available at the moment.</p>";
                return;
            }

            internships.forEach((item, index) => {
                const responsibilitiesHTML = item.responsibilities
                    ? item.responsibilities.map(task => `<li>${task}</li>`).join("")
                    : "<li>Responsibilities will be discussed during the application process.</li>";

                const card = document.createElement("div");
                card.className = "card internship-card";
                card.setAttribute('data-type', item.type || '');
                card.setAttribute('data-duration', item.duration || '');
                card.innerHTML = `
                    <div class="card-content">
                        <div class="card-header">
                            <img src="${item.image || './assets/images/webdev.png'}" alt="${item.title}" class="company-logo" />
                            <div class="internship-badge">${item.type || 'Remote'}</div>
                        </div>
                        <div class="card-body">
                            <h3 class="internship-title">${item.title}</h3>
                            <p class="company-name"><i class="fas fa-building"></i> ${item.company}</p>
                            <div class="internship-meta">
                                <span class="duration"><i class="fas fa-clock"></i> ${item.duration || 'Flexible'}</span>
                                <span class="stipend"><i class="fas fa-money-bill-wave"></i> ${item.stipend || 'Unpaid'}</span>
                            </div>
                            <div class="card-buttons">
                                <button class="button toggle-btn" data-index="${index}">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                                <a href="${item.applyLink || '#'}" class="button apply-btn" target="_blank">
                                    <i class="fas fa-paper-plane"></i> Apply Now
                                </a>
                            </div>
                        </div>
                        
                        <div class="syllabus internship-details">
                            <h4><i class="fas fa-tasks"></i> Key Responsibilities:</h4>
                            <ul>${responsibilitiesHTML}</ul>
                            
                            ${item.requirements ? `
                                <h4><i class="fas fa-check-circle"></i> Requirements:</h4>
                                <ul>${item.requirements.map(req => `<li>${req}</li>`).join("")}</ul>
                            ` : ''}
                            
                            ${item.benefits ? `
                                <h4><i class="fas fa-gift"></i> Benefits:</h4>
                                <ul>${item.benefits.map(benefit => `<li>${benefit}</li>`).join("")}</ul>
                            ` : ''}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            // Add event listeners for toggle buttons
            document.querySelectorAll(".toggle-btn").forEach((btn) => {
                btn.addEventListener("click", () => {
                    const card = btn.closest(".card");
                    const isExpanded = card.classList.contains("expanded");
                    
                    // Close all other expanded cards
                    document.querySelectorAll(".card.expanded").forEach(c => {
                        if (c !== card) {
                            c.classList.remove("expanded");
                            c.querySelector(".toggle-btn").innerHTML = '<i class="fas fa-eye"></i> View Details';
                        }
                    });
                    
                    // Toggle current card
                    card.classList.toggle("expanded");
                    btn.innerHTML = isExpanded 
                        ? '<i class="fas fa-eye"></i> View Details'
                        : '<i class="fas fa-eye-slash"></i> View Less';
                });
            });

            // Initialize search and filter functionality
            initializeSearchAndFilter();

        } catch (err) {
            container.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Unable to load internships at the moment.</p>
                    <button onclick="loadInternships()" class="retry-btn">
                        <i class="fas fa-redo"></i> Retry
                    </button>
                </div>
            `;
            console.error("Error loading internships:", err);
        }
    }

    // Search and Filter functionality
    function initializeSearchAndFilter() {
        const searchInput = document.getElementById('search-input');
        const filterType = document.getElementById('filter-type');
        const filterDuration = document.getElementById('filter-duration');
        const cards = document.querySelectorAll('.internship-card');

        function filterInternships() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedType = filterType.value;
            const selectedDuration = filterDuration.value;

            cards.forEach(card => {
                const title = card.querySelector('.internship-title').textContent.toLowerCase();
                const company = card.querySelector('.company-name').textContent.toLowerCase();
                const type = card.getAttribute('data-type');
                const duration = card.getAttribute('data-duration');

                const matchesSearch = title.includes(searchTerm) || company.includes(searchTerm);
                const matchesType = !selectedType || type === selectedType;
                const matchesDuration = !selectedDuration || duration.includes(selectedDuration);

                if (matchesSearch && matchesType && matchesDuration) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                    card.classList.remove('expanded'); // Close expanded cards when hidden
                }
            });

            // Show "no results" message if no cards are visible
            const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
            const container = document.getElementById('internship-list');
            
            // Remove existing no-results message
            const existingNoResults = container.querySelector('.no-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }

            if (visibleCards.length === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = `
                    <div class="no-results-content">
                        <i class="fas fa-search"></i>
                        <p>No internships match your search criteria.</p>
                        <button onclick="clearFilters()" class="clear-filters-btn">
                            <i class="fas fa-times"></i> Clear Filters
                        </button>
                    </div>
                `;
                container.appendChild(noResults);
            }
        }

        // Add event listeners
        searchInput.addEventListener('input', filterInternships);
        filterType.addEventListener('change', filterInternships);
        filterDuration.addEventListener('change', filterInternships);
    }

    // Clear all filters
    function clearFilters() {
        document.getElementById('search-input').value = '';
        document.getElementById('filter-type').value = '';
        document.getElementById('filter-duration').value = '';
        
        // Show all cards
        document.querySelectorAll('.internship-card').forEach(card => {
            card.style.display = 'block';
        });

        // Remove no-results message
        const noResults = document.querySelector('.no-results');
        if (noResults) {
            noResults.remove();
        }
    }

    // Load internships when page loads
    document.addEventListener('DOMContentLoaded', loadInternships);
    </script>

<?php
// Include footer
include './includes/footer.php';
?>
