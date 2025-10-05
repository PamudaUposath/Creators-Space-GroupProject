const texts = ["Vision", "Core Values", "Mission", "Team", "Stats"];
let currentIndex = 0;
let charIndex = 0;
let isDeleting = false;
const speed = 100;
const pauseTime = 1000;
const target = document.getElementById("typeTarget");

function typeLoop() {
  const currentText = texts[currentIndex];

  if (isDeleting) {
    target.textContent = currentText.substring(0, charIndex--);
  } else {
    target.textContent = currentText.substring(0, charIndex++);
  }

  if (!isDeleting && charIndex === currentText.length + 1) {
    setTimeout(() => { isDeleting = true; typeLoop(); }, pauseTime);
    return;
  }

  if (isDeleting && charIndex === 0) {
    isDeleting = false;
    currentIndex = (currentIndex + 1) % texts.length;
  }

  setTimeout(typeLoop, isDeleting ? speed / 2 : speed);
}

// Stats Counter Animation
function animateCounter(element, target, suffix = '', duration = 2000) {
  let startTime = null;
  const startValue = 0;

  function animate(currentTime) {
    if (startTime === null) startTime = currentTime;
    const timeElapsed = currentTime - startTime;
    const progress = Math.min(timeElapsed / duration, 1);

    // Easing function for smooth animation
    const easeOutQuart = 1 - Math.pow(1 - progress, 4);
    const currentValue = Math.floor(easeOutQuart * target);

    // Update display with current value and suffix
    element.textContent = currentValue + suffix;

    if (progress < 1) {
      requestAnimationFrame(animate);
    } else {
      // Set final value
      element.textContent = target + suffix;
    }
  }

  requestAnimationFrame(animate);
}

// Extract number and suffix from text content
function parseStatValue(text) {
  // Handle different formats: "13", "1.5K", "85%", etc.
  const match = text.match(/^(\d+(?:\.\d+)?)(.*)/);
  if (match) {
    return {
      number: parseFloat(match[1]),
      suffix: match[2] || ''
    };
  }
  return { number: 0, suffix: '' };
}

// Intersection Observer for stats animation
function initStatsAnimation() {
  const statsSection = document.querySelector('.stats-container');
  const statNumbers = document.querySelectorAll('.stat-number');

  if (!statsSection || !statNumbers.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        // Start counter animations using real values from DOM
        statNumbers.forEach((stat, index) => {
          // Store the original value and clear the element
          const originalText = stat.textContent.trim();
          const parsed = parseStatValue(originalText);

          // Clear the element for animation
          stat.textContent = '0' + parsed.suffix;

          setTimeout(() => {
            animateCounter(stat, parsed.number, parsed.suffix, 2500);
          }, index * 200); // Stagger animations
        });

        observer.unobserve(statsSection);
      }
    });
  }, {
    threshold: 0.5
  });

  observer.observe(statsSection);
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
  // Start typewriter animation if target exists
  if (target) {
    typeLoop();
  }

  // Initialize stats counter animation
  initStatsAnimation();

  // Initialize dark mode management
  initDarkModeStyles();

  // Add hover effects for stat items
  const statItems = document.querySelectorAll('.stat-item');
  statItems.forEach(item => {
    item.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-10px) scale(1.02)';
    });

    item.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });
});

// Consolidated Dark Mode Styles Management
function initDarkModeStyles() {
  // Create a dedicated style element for dark mode
  const darkModeStyles = document.createElement('style');
  darkModeStyles.id = 'about-dark-mode-styles';
  darkModeStyles.textContent = `
    /* Consolidated Dark Mode Styles for About Page */
    
    /* Navigation Dark Mode */
    body.dark-mode .navbar {
      background: rgba(18, 18, 18, 0.95);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    body.dark-mode .navbar .nav-link {
      color: rgba(255, 255, 255, 0.9);
    }
    
    body.dark-mode .navbar .nav-link:hover {
      color: #667eea;
    }
    
    /* Footer Dark Mode */
    body.dark-mode .footer {
      background: rgba(18, 18, 18, 0.95);
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    body.dark-mode .footer .footer-link {
      color: rgba(255, 255, 255, 0.8);
    }
    
    body.dark-mode .footer .footer-link:hover {
      color: #667eea;
    }
    
    body.dark-mode .footer .footer-text {
      color: rgba(255, 255, 255, 0.7);
    }
    
    /* About Page Specific Dark Mode */
    body.dark-mode .about-section {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    body.dark-mode .section-title {
      color: #ffffff;
    }
    
    body.dark-mode .section-description {
      color: rgba(255, 255, 255, 0.8);
    }
    
    /* Stats Section Dark Mode */
    body.dark-mode .stats-container {
      background: rgba(255, 255, 255, 0.05);
    }
    
    body.dark-mode .stat-item {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    body.dark-mode .stat-item:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
    }
    
    body.dark-mode .stat-number {
      color: #667eea;
    }
    
    body.dark-mode .stat-label {
      color: rgba(255, 255, 255, 0.9);
    }
    
    /* Team Section Dark Mode */
    body.dark-mode .team-member {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    body.dark-mode .team-member:hover {
      background: rgba(255, 255, 255, 0.12);
    }
    
    // body.dark-mode .member-name {
    //   color: #ffffff;
    // }
    
    // body.dark-mode .member-role {
    //   color: rgba(255, 255, 255, 0.8);
    // }
    
    /* Mission/Vision Cards Dark Mode */
    // body.dark-mode .mission-vision-card,
    body.dark-mode .values-card {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    // body.dark-mode .mission-vision-card:hover,
    body.dark-mode .values-card:hover {
      background: rgba(255, 255, 255, 0.12);
      border-color: rgba(255, 255, 255, 0.2);
    }
  `;

  // Only add if not already present
  if (!document.getElementById('about-dark-mode-styles')) {
    document.head.appendChild(darkModeStyles);
  }

  // Watch for dark mode changes
  observeDarkModeChanges();
}

// Observer for dark mode changes
function observeDarkModeChanges() {
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
        const isDarkMode = document.body.classList.contains('dark-mode');
        handleDarkModeChange(isDarkMode);
      }
    });
  });

  observer.observe(document.body, {
    attributes: true,
    attributeFilter: ['class']
  });

  // Initial check
  const isDarkMode = document.body.classList.contains('dark-mode');
  handleDarkModeChange(isDarkMode);
}

// Handle dark mode state changes
function handleDarkModeChange(isDarkMode) {
  // Additional JavaScript-based dark mode handling if needed
  const typeTarget = document.getElementById('typeTarget');
  if (typeTarget) {
    // Ensure typewriter text is visible in both modes
    if (isDarkMode) {
      typeTarget.style.color = '#ffffff';
    } else {
      typeTarget.style.color = '#333333';
    }
  }

  // Handle any dynamic style adjustments
  updateDynamicStyles(isDarkMode);
}

// Update dynamic styles based on dark mode
function updateDynamicStyles(isDarkMode) {
  const statItems = document.querySelectorAll('.stat-item');

  statItems.forEach(item => {
    if (isDarkMode) {
      item.style.transition = 'all 0.3s ease, background 0.3s ease, border-color 0.3s ease';
    } else {
      item.style.transition = 'all 0.3s ease';
    }
  });
}

typeLoop(); // Start animation 