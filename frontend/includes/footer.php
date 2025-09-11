  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Creators-Space</h3>
          <p>Empowering the next generation of tech innovators through quality education and hands-on learning.</p>
        </div>
        <div class="footer-section">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="about.php">About Us</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="blog.php">Blog</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Support</h4>
          <ul>
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="tandc.php">Terms & Conditions</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Connect</h4>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Creators-Space. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Common Scripts -->
  <script src="./src/js/navbar.js"></script>
  <script src="./src/js/utils.js"></script>
  <script src="./src/js/mobile-responsive.js"></script>
  
  <!-- Toast Notification JavaScript -->
  <script>
    function showToast(message, type = 'info', duration = 5000) {
      const container = document.getElementById('toast-container');
      
      // Create toast element
      const toast = document.createElement('div');
      toast.className = `toast ${type}`;
      
      // Get icon based on type
      let icon = '';
      switch (type) {
        case 'success':
          icon = '<i class="fas fa-check-circle toast-icon"></i>';
          break;
        case 'error':
          icon = '<i class="fas fa-exclamation-circle toast-icon"></i>';
          break;
        case 'warning':
          icon = '<i class="fas fa-exclamation-triangle toast-icon"></i>';
          break;
        case 'info':
        default:
          icon = '<i class="fas fa-info-circle toast-icon"></i>';
          break;
      }
      
      toast.innerHTML = `
        ${icon}
        <span class="toast-message">${message}</span>
        <button class="toast-close" onclick="removeToast(this.parentElement)">&times;</button>
      `;
      
      // Add to container
      container.appendChild(toast);
      
      // Trigger animation
      setTimeout(() => {
        toast.classList.add('show');
      }, 100);
      
      // Auto remove after duration
      setTimeout(() => {
        removeToast(toast);
      }, duration);
      
      return toast;
    }
    
    function removeToast(toast) {
      toast.classList.remove('show');
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    }
    
    // Override the default alert function
    window.originalAlert = window.alert;
    window.alert = function(message) {
      showToast(message, 'info');
    };
  </script>
  
  <?php if (isset($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
      <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <?php if (isset($customJS)): ?>
    <script><?php echo $customJS; ?></script>
  <?php endif; ?>

  <!-- User authentication state for JavaScript -->
  <script>
    window.userAuth = {
      isLoggedIn: <?php echo $isLoggedIn ? 'true' : 'false'; ?>,
      user: <?php echo $isLoggedIn ? json_encode($user) : 'null'; ?>
    };
  </script>

</body>
</html>
