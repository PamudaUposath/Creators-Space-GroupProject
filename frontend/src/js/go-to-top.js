// Go to Top Button - Common JavaScript Component
class GoToTopButton {
  constructor(options = {}) {
    this.options = {
      showOffset: 300,
      scrollDuration: 800,
      buttonId: 'goToTopBtn',
      ...options
    };
    
    this.button = null;
    this.isVisible = false;
    this.throttledScrollHandler = null; // Store reference to throttled scroll handler
    this.init();
  }

  // Initialize the button
  init() {
    this.createButton();
    this.attachEventListeners();
    this.handleScroll();
  }

  // Create the button element
  createButton() {
    // Check if button already exists
    if (document.getElementById(this.options.buttonId)) {
      this.button = document.getElementById(this.options.buttonId);
      return;
    }

    // Create button element
    this.button = document.createElement('button');
    this.button.id = this.options.buttonId;
    this.button.className = 'go-to-top';
    this.button.setAttribute('aria-label', 'Go to top of page');
    this.button.setAttribute('title', 'Go to Top');
    this.button.innerHTML = '<i class="fas fa-chevron-up" aria-hidden="true"></i>';
    
    // Add to document
    document.body.appendChild(this.button);
  }

  // Attach event listeners
  attachEventListeners() {
    // Scroll event with throttling for performance
    let ticking = false;
    this.throttledScrollHandler = () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          this.handleScroll();
          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', this.throttledScrollHandler, { passive: true });

    // Click event for smooth scroll to top
    this.button.addEventListener('click', (e) => {
      e.preventDefault();
      this.scrollToTop();
    });

    // Keyboard accessibility
    this.button.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        this.scrollToTop();
      }
    });
  }

  // Handle scroll visibility
  handleScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const shouldShow = scrollTop > this.options.showOffset;

    if (shouldShow && !this.isVisible) {
      this.showButton();
    } else if (!shouldShow && this.isVisible) {
      this.hideButton();
    }
  }

  // Show button with animation
  showButton() {
    this.isVisible = true;
    this.button.classList.add('show');
    this.button.classList.add('animate-in');
    
    // Remove animation class after animation completes
    setTimeout(() => {
      this.button.classList.remove('animate-in');
    }, 500);
  }

  // Hide button
  hideButton() {
    this.isVisible = false;
    this.button.classList.remove('show');
  }

  // Smooth scroll to top
  scrollToTop() {
    const startPosition = window.pageYOffset;
    const startTime = performance.now();

    const easeInOutCubic = (t) => {
      return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
    };

    const scrollAnimation = (currentTime) => {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / this.options.scrollDuration, 1);
      const easeProgress = easeInOutCubic(progress);
      
      window.scrollTo(0, startPosition * (1 - easeProgress));

      if (progress < 1) {
        requestAnimationFrame(scrollAnimation);
      }
    };

    requestAnimationFrame(scrollAnimation);
  }

  // Destroy the button (useful for cleanup)
  destroy() {
    if (this.button && this.button.parentNode) {
      this.button.parentNode.removeChild(this.button);
    }
    
    // Remove the correct throttled scroll handler reference
    if (this.throttledScrollHandler) {
      window.removeEventListener('scroll', this.throttledScrollHandler);
      this.throttledScrollHandler = null;
    }
  }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Go to Top button
  const goToTop = new GoToTopButton({
    showOffset: 300,        // Show button after scrolling 300px
    scrollDuration: 800     // Scroll animation duration in ms
  });

  // Make it globally accessible for debugging
  window.goToTopButton = goToTop;
});

// Alternative simple initialization for immediate use
function initGoToTop() {
  return new GoToTopButton();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = GoToTopButton;
}