const texts = ["Vision", "Core Values" , "Mision" ,"Team" , "Stats"];
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
document.addEventListener('DOMContentLoaded', function() {
  // Start typewriter animation if target exists
  if (target) {
    typeLoop();
  }
  
  // Initialize stats counter animation
  initStatsAnimation();
  
  // Add hover effects for stat items
  const statItems = document.querySelectorAll('.stat-item');
  statItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
      this.style.transform = 'translateY(-10px) scale(1.02)';
    });
    
    item.addEventListener('mouseleave', function() {
      this.style.transform = 'translateY(0) scale(1)';
    });
  });
});

typeLoop(); // Start animation 