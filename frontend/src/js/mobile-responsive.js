/**
 * Mobile Navigation Enhancement
 * Enhanced mobile navigation with touch gestures and smooth animations
 */

class MobileNavigation {
    constructor() {
        this.navbar = document.querySelector('.navbar');
        this.navLinks = document.querySelector('.nav-links');
        this.navToggle = document.querySelector('.nav-toggle');
        this.navItems = document.querySelectorAll('.nav-links a');
        this.isOpen = false;
        this.touchStartX = 0;
        this.touchStartY = 0;

        this.init();
    }

    init() {
        this.createToggleButton();
        this.bindEvents();
        this.handleResize();
    }

    createToggleButton() {
        if (!this.navToggle) {
            // Create hamburger menu button if it doesn't exist
            this.navToggle = document.createElement('button');
            this.navToggle.className = 'nav-toggle';
            this.navToggle.setAttribute('aria-label', 'Toggle navigation');
            this.navToggle.setAttribute('aria-expanded', 'false');
            
            // Create hamburger lines
            for (let i = 0; i < 3; i++) {
                const span = document.createElement('span');
                this.navToggle.appendChild(span);
            }
            
            // Insert toggle button in navbar
            this.navbar.appendChild(this.navToggle);
        }
    }

    bindEvents() {
        // Toggle button click
        if (this.navToggle) {
            this.navToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleNav();
            });
        }

        // Close nav when clicking on links
        this.navItems.forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    this.closeNav();
                }
            });
        });

        // Close nav when clicking outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.navbar.contains(e.target)) {
                this.closeNav();
            }
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.closeNav();
            }
        });

        // Touch gestures for mobile
        if (this.navLinks) {
            this.navLinks.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
            this.navLinks.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: true });
            this.navLinks.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: true });
        }

        // Handle window resize
        window.addEventListener('resize', this.handleResize.bind(this));

        // Prevent body scroll when nav is open
        this.navLinks?.addEventListener('scroll', (e) => {
            e.preventDefault();
        });
    }

    toggleNav() {
        if (this.isOpen) {
            this.closeNav();
        } else {
            this.openNav();
        }
    }

    openNav() {
        this.isOpen = true;
        this.navLinks?.classList.add('active');
        this.navToggle?.classList.add('active');
        this.navToggle?.setAttribute('aria-expanded', 'true');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
        
        // Focus management for accessibility
        const firstLink = this.navLinks?.querySelector('a');
        firstLink?.focus();

        // Add animation class
        this.navLinks?.classList.add('nav-opening');
        setTimeout(() => {
            this.navLinks?.classList.remove('nav-opening');
        }, 300);
    }

    closeNav() {
        this.isOpen = false;
        this.navLinks?.classList.remove('active');
        this.navToggle?.classList.remove('active');
        this.navToggle?.setAttribute('aria-expanded', 'false');
        
        // Close any open dropdowns
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            dropdown.classList.remove('active');
        });
        
        // Restore body scrolling
        document.body.style.overflow = '';
        
        // Return focus to toggle button
        this.navToggle?.focus();

        // Add animation class
        this.navLinks?.classList.add('nav-closing');
        setTimeout(() => {
            this.navLinks?.classList.remove('nav-closing');
        }, 300);
    }

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
    }

    handleTouchMove(e) {
        if (!this.touchStartX || !this.touchStartY) return;

        const touchEndX = e.touches[0].clientX;
        const touchEndY = e.touches[0].clientY;

        const diffX = this.touchStartX - touchEndX;
        const diffY = this.touchStartY - touchEndY;

        // Check if horizontal swipe is more significant than vertical
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
            // Swipe left to close (diffX > 0)
            if (diffX > 0 && this.isOpen) {
                this.closeNav();
            }
        }
    }

    handleTouchEnd() {
        this.touchStartX = 0;
        this.touchStartY = 0;
    }

    handleResize() {
        // Close mobile nav if screen becomes large
        if (window.innerWidth > 768 && this.isOpen) {
            this.closeNav();
        }
    }
}

/**
 * Mobile Form Enhancement
 * Improves form usability on mobile devices
 */
class MobileFormEnhancement {
    constructor() {
        this.forms = document.querySelectorAll('form');
        this.inputs = document.querySelectorAll('input, textarea, select');
        
        this.init();
    }

    init() {
        this.enhanceInputs();
        this.addFormValidation();
        this.handleKeyboard();
    }

    enhanceInputs() {
        this.inputs.forEach(input => {
            // Add proper input types for mobile keyboards
            if (input.name?.includes('email') || input.type === 'email') {
                input.setAttribute('inputmode', 'email');
                input.setAttribute('autocomplete', 'email');
            }
            
            if (input.name?.includes('phone') || input.type === 'tel') {
                input.setAttribute('inputmode', 'tel');
                input.setAttribute('autocomplete', 'tel');
            }
            
            if (input.name?.includes('url') || input.type === 'url') {
                input.setAttribute('inputmode', 'url');
            }

            // Add touch-friendly focus handling
            input.addEventListener('focus', () => {
                input.parentElement?.classList.add('input-focused');
            });

            input.addEventListener('blur', () => {
                input.parentElement?.classList.remove('input-focused');
            });

            // Auto-resize textareas
            if (input.tagName === 'TEXTAREA') {
                input.addEventListener('input', () => {
                    input.style.height = 'auto';
                    input.style.height = input.scrollHeight + 'px';
                });
            }
        });
    }

    addFormValidation() {
        this.forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const isValid = this.validateForm(form);
                if (!isValid) {
                    e.preventDefault();
                    this.showValidationErrors(form);
                }
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('error');
                this.showFieldError(input, 'This field is required');
            } else {
                input.classList.remove('error');
                this.hideFieldError(input);
            }
        });

        return isValid;
    }

    showFieldError(input, message) {
        let errorElement = input.parentElement.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            input.parentElement.appendChild(errorElement);
        }
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    hideFieldError(input) {
        const errorElement = input.parentElement.querySelector('.error-message');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    showValidationErrors(form) {
        const firstError = form.querySelector('.error');
        if (firstError) {
            firstError.focus();
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    handleKeyboard() {
        // Handle virtual keyboard on mobile
        let viewportHeight = window.innerHeight;
        
        window.addEventListener('resize', () => {
            const currentHeight = window.innerHeight;
            const heightDifference = viewportHeight - currentHeight;
            
            // If height decreased significantly, keyboard is likely open
            if (heightDifference > 150) {
                document.body.classList.add('keyboard-open');
            } else {
                document.body.classList.remove('keyboard-open');
            }
        });
    }
}

/**
 * Touch Gesture Enhancement
 * Adds touch gestures for better mobile interaction
 */
class TouchGestures {
    constructor() {
        this.swipeElements = document.querySelectorAll('[data-swipe]');
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.minSwipeDistance = 50;
        
        this.init();
    }

    init() {
        this.bindSwipeEvents();
        this.addPullToRefresh();
    }

    bindSwipeEvents() {
        this.swipeElements.forEach(element => {
            element.addEventListener('touchstart', this.handleTouchStart.bind(this), { passive: true });
            element.addEventListener('touchmove', this.handleTouchMove.bind(this), { passive: true });
            element.addEventListener('touchend', this.handleTouchEnd.bind(this), { passive: true });
        });
    }

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.touchStartY = e.touches[0].clientY;
    }

    handleTouchMove(e) {
        // Prevent default for horizontal swipes to avoid page navigation
        const touchMoveX = e.touches[0].clientX;
        const diffX = Math.abs(this.touchStartX - touchMoveX);
        
        if (diffX > 10) {
            e.preventDefault();
        }
    }

    handleTouchEnd(e) {
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        
        const diffX = this.touchStartX - touchEndX;
        const diffY = this.touchStartY - touchEndY;
        
        // Check if it's a horizontal swipe
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > this.minSwipeDistance) {
            const direction = diffX > 0 ? 'left' : 'right';
            this.triggerSwipe(e.target.closest('[data-swipe]'), direction);
        }
    }

    triggerSwipe(element, direction) {
        const swipeEvent = new CustomEvent('swipe', {
            detail: { direction, element }
        });
        element.dispatchEvent(swipeEvent);
    }

    addPullToRefresh() {
        let startY = 0;
        let pullDistance = 0;
        const maxPullDistance = 100;
        let pullToRefreshElement = null;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                startY = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (startY > 0) {
                pullDistance = e.touches[0].clientY - startY;
                
                if (pullDistance > 0 && pullDistance < maxPullDistance) {
                    if (!pullToRefreshElement) {
                        pullToRefreshElement = this.createPullToRefreshElement();
                    }
                    pullToRefreshElement.style.height = `${pullDistance}px`;
                    pullToRefreshElement.style.opacity = pullDistance / maxPullDistance;
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (pullDistance > 50) {
                // Trigger refresh
                window.location.reload();
            }
            
            if (pullToRefreshElement) {
                pullToRefreshElement.remove();
                pullToRefreshElement = null;
            }
            
            startY = 0;
            pullDistance = 0;
        }, { passive: true });
    }

    createPullToRefreshElement() {
        const element = document.createElement('div');
        element.className = 'pull-to-refresh';
        element.innerHTML = '<i class="fas fa-sync-alt"></i> Pull to refresh';
        element.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: var(--primary-color);
            color: white;
            text-align: center;
            padding: 10px;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            z-index: 9999;
        `;
        document.body.appendChild(element);
        return element;
    }
}

/**
 * Initialize Mobile Enhancements
 */
document.addEventListener('DOMContentLoaded', () => {
    // Initialize mobile navigation
    new MobileNavigation();
    
    // Initialize form enhancements
    new MobileFormEnhancement();
    
    // Initialize touch gestures
    new TouchGestures();
    
    // Add mobile-specific CSS classes
    if (window.innerWidth <= 768) {
        document.body.classList.add('mobile-device');
    }
    
    // Handle orientation change
    window.addEventListener('orientationchange', () => {
        setTimeout(() => {
            // Recalculate viewport height after orientation change
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        }, 100);
    });
    
    // Set CSS custom property for accurate mobile viewport height
    const setVH = () => {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    };
    
    setVH();
    window.addEventListener('resize', setVH);
    
    // Add smooth scrolling for mobile
    if (CSS.supports('scroll-behavior', 'smooth')) {
        document.documentElement.style.scrollBehavior = 'smooth';
    }
});

/**
 * Export for module systems
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        MobileNavigation,
        MobileFormEnhancement,
        TouchGestures
    };
}
