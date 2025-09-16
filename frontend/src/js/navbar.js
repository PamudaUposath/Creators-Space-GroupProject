const toggleNav = () => {
    var navLinks = document.querySelector(".nav-links");
    var bargerLogo = document.querySelector("#navbar-barger-logo");
    var crossLogo = document.querySelector("#navbar-x-logo");
    navLinks.classList.toggle("active");

    crossLogo.classList.toggle('d-none');
    bargerLogo.classList.toggle('d-none');
//  if (element.classList.contains("dark")) {
//         localStorage.setItem("theme", "dark");
//         logo.src = "./assets/images/logo-nav-dark.png"; // Dark mode logo
//     } else {
//         localStorage.setItem("theme", "light");
//         logo.src = "./assets/images/logo-nav-light.png"; // Light mode logo
//     }
}

// Dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Handle dropdown interactions
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // Desktop hover functionality
        if (window.innerWidth > 768) {
            dropdown.addEventListener('mouseenter', () => {
                dropdown.classList.add('active');
            });
            
            dropdown.addEventListener('mouseleave', () => {
                dropdown.classList.remove('active');
            });
        }
        
        // Mobile click functionality
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close other dropdowns on mobile
            if (window.innerWidth <= 768) {
                dropdowns.forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('active');
                    }
                });
                
                // Toggle current dropdown
                dropdown.classList.toggle('active');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Handle window resize for responsive behavior
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            // Reset mobile dropdown states when switching to desktop
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
});