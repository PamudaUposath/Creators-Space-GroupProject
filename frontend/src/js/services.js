document.addEventListener("DOMContentLoaded", () => {
  // Dark mode is now handled by dark-mode.js
  // Initialize dark mode controller if not already done
  if (typeof DarkModeController !== 'undefined' && !window.darkModeController) {
    window.darkModeController = new DarkModeController();
  }
  
  // Services-specific functionality can go here
});
