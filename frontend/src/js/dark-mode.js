/* ================================================
   CREATORS-SPACE UNIVERSAL DARK MODE CONTROLLER
   ================================================ */

class DarkModeController {
    constructor() {
        this.storageKey = 'creators-space-theme';
        this.body = document.body;
        this.toggle = null;
        this.checkbox = null;
        
        this.init();
    }

    init() {
        // Load saved theme on page load
        this.loadSavedTheme();
        
        // Create toggle if it doesn't exist
        this.createToggleIfNeeded();
        
        // Set up event listeners
        this.setupEventListeners();
        
        // Update logo based on current theme
        this.updateLogo();
        
        console.log('Dark Mode Controller initialized');
    }

    loadSavedTheme() {
        const savedTheme = localStorage.getItem(this.storageKey);
        
        if (savedTheme === 'dark') {
            this.body.classList.add('dark');
        } else {
            this.body.classList.remove('dark');
        }
    }

    createToggleIfNeeded() {
        // Check if toggle already exists
        let existingToggle = document.querySelector('.dark-mode-toggle');
        
        if (!existingToggle) {
            // Create the toggle
            const toggleContainer = document.createElement('div');
            toggleContainer.className = 'dark-mode-toggle';
            
            toggleContainer.innerHTML = `
                <div class="toggle-switch">
                    <label class="switch-label" title="Toggle Dark Mode">
                        <input type="checkbox" class="checkbox" id="dark-mode-toggle" />
                        <span class="slider"></span>
                    </label>
                </div>
            `;
            
            // Add to body
            document.body.appendChild(toggleContainer);
            existingToggle = toggleContainer;
        }
        
        this.toggle = existingToggle;
        this.checkbox = this.toggle.querySelector('input[type="checkbox"]');
        
        // Set checkbox state based on current theme
        this.updateCheckboxState();
    }

    updateCheckboxState() {
        if (this.checkbox) {
            this.checkbox.checked = this.body.classList.contains('dark');
        }
    }

    setupEventListeners() {
        if (this.checkbox) {
            this.checkbox.addEventListener('change', (e) => {
                this.toggleTheme();
            });
        }

        // Listen for system theme changes
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            mediaQuery.addListener((e) => {
                // Only auto-switch if user hasn't manually set a preference
                if (!localStorage.getItem(this.storageKey)) {
                    this.setTheme(e.matches ? 'dark' : 'light', false);
                }
            });
        }

        // Keyboard shortcut (Ctrl/Cmd + D)
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'd') {
                e.preventDefault();
                this.toggleTheme();
            }
        });
    }

    toggleTheme() {
        const isDark = this.body.classList.contains('dark');
        const newTheme = isDark ? 'light' : 'dark';
        this.setTheme(newTheme, true);
    }

    setTheme(theme, saveToStorage = true) {
        if (theme === 'dark') {
            this.body.classList.add('dark');
        } else {
            this.body.classList.remove('dark');
        }

        if (saveToStorage) {
            localStorage.setItem(this.storageKey, theme);
        }

        this.updateCheckboxState();
        this.updateLogo();
        this.dispatchThemeChangeEvent(theme);
    }

    updateLogo() {
        const logo = document.getElementById('navbar-logo');
        if (logo) {
            const isDark = this.body.classList.contains('dark');
            const logoPath = isDark 
                ? './assets/images/logo-nav-light.png' 
                : './assets/images/logo-nav-light.png'; // You might want different logos
            
            logo.src = logoPath;
        }
    }

    dispatchThemeChangeEvent(theme) {
        // Dispatch custom event for other components to listen to
        const event = new CustomEvent('themeChanged', {
            detail: { theme: theme, isDark: theme === 'dark' }
        });
        document.dispatchEvent(event);
    }

    getCurrentTheme() {
        return this.body.classList.contains('dark') ? 'dark' : 'light';
    }

    // Public methods for external use
    enableDarkMode() {
        this.setTheme('dark');
    }

    enableLightMode() {
        this.setTheme('light');
    }

    // Auto-detect system preference
    detectSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            this.setTheme('dark', false);
        } else {
            this.setTheme('light', false);
        }
    }
}

// Initialize dark mode when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Make sure we don't initialize multiple times
    if (!window.darkModeController) {
        window.darkModeController = new DarkModeController();
    }
});

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DarkModeController;
}

// Add some utility functions for backward compatibility
window.toggleDarkMode = function() {
    if (window.darkModeController) {
        window.darkModeController.toggleTheme();
    }
};

window.setDarkMode = function(isDark) {
    if (window.darkModeController) {
        window.darkModeController.setTheme(isDark ? 'dark' : 'light');
    }
};

/* ================================================
   THEME CHANGE ANIMATIONS
   ================================================ */

// Add smooth transition class when theme changes
document.addEventListener('themeChanged', (e) => {
    document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
    
    // Remove transition after animation completes
    setTimeout(() => {
        document.body.style.transition = '';
    }, 300);
});

// Preload theme on very first visit
(function() {
    // Check if user has a saved preference
    const savedTheme = localStorage.getItem('creators-space-theme');
    
    if (!savedTheme) {
        // Check system preference
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.body.classList.add('dark');
        }
    } else if (savedTheme === 'dark') {
        document.body.classList.add('dark');
    }
})();