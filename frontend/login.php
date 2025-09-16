<?php
// frontend/login.php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = $_SESSION['error'] ?? '';
if ($error) {
    unset($_SESSION['error']);
}

// Set page-specific variables
$pageTitle = "Login";
$pageDescription = "Sign in to your Creators-Space account";
$bodyClass = "auth-page";

// Include header
include './includes/header.php';
?>

<style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
      transition: background 0.5s ease, color 0.3s ease;
    }

    /* Animated Background Particles for light mode */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="1.5" fill="rgba(255,255,255,0.15)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="30" r="2.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="70" r="1.8" fill="rgba(255,255,255,0.12)"/></svg>') repeat;
      animation: particleFloat 20s linear infinite;
      pointer-events: none;
      z-index: -2;
    }

    @keyframes particleFloat {
      0% { transform: translateY(0) rotate(0deg); }
      100% { transform: translateY(-100vh) rotate(360deg); }
    }

    /* Dark mode styles for body */
    body.dark {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #ffffff;
    }

    /* Remove particles in dark mode */
    body.dark::before {
      display: none;
    }

    .main-content {
      display: flex;
      justify-content: center;
      align-items: center;
      flex: 1;
      padding: 2rem 1rem;
    }

    .container {
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 150px rgba(0, 0, 0, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      font-weight: 500;
      transition: background-color 0.3s ease, border-color 0.3s ease;
      backdrop-filter: blur(10px);
    }

    /* Dark mode styles for container */
    body.dark .container {
      background: rgba(40, 44, 52, 0.95);
      border: 1px solid #444;
      color: #ffffff;
      box-shadow: 0 8px 150px rgba(0, 0, 0, 0.3);
    }

    h2 {
      text-align: center;
      margin-bottom: 1rem;
      color: #2d3748;
      transition: color 0.3s ease;
      font-weight: 600;
    }

    /* Dark mode styles for headings */
    body.dark h2 {
      color: #ffffff;
    }

    .form-group {
      margin-bottom: 1rem;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 5px;
      color: #4a5568;
      transition: color 0.3s ease;
      font-weight: 500;
    }

    /* Dark mode styles for labels */
    body.dark label {
      color: #e0e0e0;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      padding-right: 40px;
      border: 1px solid rgba(102, 126, 234, 0.3);
      border-radius: 6px;
      background: rgba(255, 255, 255, 0.9);
      color: #2d3748;
      transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
      backdrop-filter: blur(5px);
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #667eea;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Dark mode styles for inputs */
    body.dark input[type="email"],
    body.dark input[type="password"] {
      background: #2d3748;
      border: 1px solid #4a5568;
      color: #ffffff;
    }

    body.dark input[type="email"]:focus,
    body.dark input[type="password"]:focus {
      border-color: #66b3ff;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      top: 38px;
      cursor: pointer;
      color: #667eea;
      transition: color 0.3s ease;
    }

    .toggle-password:hover {
      color: #4c51bf;
    }

    /* Dark mode styles for toggle password */
    body.dark .toggle-password {
      color: #e0e0e0;
    }

    .inline-feedback {
      font-size: 0.85rem;
      color: red;
      margin-top: 5px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    .forgot-password {
      display: block;
      text-align: right;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: #667eea;
      text-decoration: none;
      transition: color 0.3s ease;
      font-weight: 500;
    }

    .forgot-password:hover {
      color: #4c51bf;
      text-decoration: underline;
    }

    /* Dark mode styles for forgot password link */
    body.dark .forgot-password {
      color: #66b3ff;
    }

    body.dark .forgot-password:hover {
      color: #4da6ff;
    }

    .signup-page {
      display: block;
      text-align: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: #333;
      text-decoration: none;
    }

    .signup-page a {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
    }

    .signup-page a:hover {
      color: #4c51bf;
      text-decoration: underline;
    }

    /* Dark mode styles for signup page link */
    body.dark .signup-page {
      color: #e0e0e0;
    }

    body.dark .signup-page a {
      color: #66b3ff;
    }

    body.dark .signup-page a:hover {
      color: #4da6ff;
    }

    .btn {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }

    .btn:disabled {
      background: #999;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* Dark mode styles for button */
    body.dark .btn {
      background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
      box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
    }

    body.dark .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
    }

    body.dark .btn:disabled {
      background: #666;
      transform: none;
      box-shadow: none;
    }

    .spinner {
      display: none;
      width: 24px;
      height: 24px;
      margin: 0 auto;
      border: 3px solid #f3f3f3;
      border-top: 3px solid black;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-top: 1rem;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 1rem;
      border: 1px solid #f5c6cb;
    }

    .forgot-password-section {
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #eee;
      transition: border-color 0.3s ease;
    }

    /* Dark mode styles for forgot password section */
    body.dark .forgot-password-section {
      border-top: 1px solid #444;
    }

    .back-to-home {
      display: inline-block;
      margin-bottom: 10px;
      font-size: 14px;
      color: #555;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-to-home:hover {
      color: #667eea;
    }

    /* Dark mode styles for back to home link */
    body.dark .back-to-home {
      color: #e0e0e0;
    }

    body.dark .back-to-home:hover {
      color: #66b3ff;
    }

    .forgot-form {
      display: none;
    }

    .forgot-form.active {
      display: block;
    }

    /* Dark mode styles for reset password form */
    body.dark .forgot-form h3 {
      color: #ffffff;
    }

    body.dark .forgot-form p {
      color: #b3b3b3 !important;
    }

    body.dark .forgot-form a {
      color: #66b3ff;
    }

    body.dark .forgot-form a:hover {
      color: #4da6ff;
    }
  </style>
</head>

<body>
  <div class="main-content">
    <div class="container">
      <a href="index.php" class="back-to-home">
      ← Back to Homepage
    </a>
    
    <h2>Login</h2>

    <?php if ($error): ?>
      <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="login-form" method="POST">
      <div class="form-group">
        <label>Email</label>
        <input type="email" id="email" name="email" required />
        <div id="emailFeedback" class="inline-feedback"></div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" id="password" name="password" required />
        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
      </div>

      <div class="checkbox-group">
        <input type="checkbox" id="remember" name="remember_me" style="width:auto;">
        <label for="remember" style="display:inline;">Remember me</label>
      </div>

      <button type="submit" class="btn" id="loginBtn">Login</button>
      <div class="spinner" id="spinner"></div>

      <a href="#" class="forgot-password" onclick="toggleForgotPassword()">Forgot Password?</a>

      <div class="signup-page">
        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
      </div>
    </form>

    <!-- Forgot Password Form -->
    <div class="forgot-password-section">
      <div id="forgot-form" class="forgot-form">
        <h3>Reset Password</h3>
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem; transition: color 0.3s ease;">
          Enter your email address and we'll send you a link to reset your password.
        </p>
        <form id="forgot-password-form" method="POST" action="../backend/auth/forgot_password.php">
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" required />
          </div>
          <button type="submit" class="btn">Send Reset Link</button>
          <div class="spinner" id="forgot-spinner"></div>
        </form>
        <a href="#" onclick="toggleForgotPassword()" style="font-size: 0.9rem; color: #666; transition: color 0.3s ease;">← Back to Login</a>
      </div>
    </div>
  </div>
  </div> <!-- Close main-content -->

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }

    function toggleForgotPassword() {
      const forgotForm = document.getElementById('forgot-form');
      const loginForm = document.getElementById('login-form');
      
      if (forgotForm.classList.contains('active')) {
        forgotForm.classList.remove('active');
        loginForm.style.display = 'block';
      } else {
        forgotForm.classList.add('active');
        loginForm.style.display = 'none';
      }
    }

    // Handle login form submission
    document.getElementById('login-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const spinner = document.getElementById('spinner');
      const loginBtn = document.getElementById('loginBtn');
      
      spinner.style.display = 'block';
      loginBtn.disabled = true;
      loginBtn.textContent = 'Logging in...';

      const formData = new FormData(this);
      console.log('Attempting login with URL: ../backend/auth/login_process.php');

      fetch('../backend/auth/login_process.php?t=' + Date.now(), {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        console.log('Login response:', data); // Debug log
        
        // Reset button state
        spinner.style.display = 'none';
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
        
        if (data.success) {
          console.log('Login successful!');
          
          // Try to show toast, but don't let it break the redirect
          try {
            showToast('Login successful! Redirecting...', 'success');
          } catch (e) {
            console.log('Toast error:', e);
            alert('Login successful! Redirecting...');
          }
          
          // Get redirect path from response or default to index.php
          let redirectPath = data.data.redirect || 'index.php';
          console.log('Redirecting to:', redirectPath); // Debug log
          
          // Simple redirect without complex path manipulation
          setTimeout(() => {
            console.log('Executing redirect to:', redirectPath); // Additional debug log
            window.location.href = redirectPath;
          }, 1500); // Slightly longer delay
        } else {
          console.log('Login failed:', data.message);
          try {
            showToast(data.message || 'Login failed', 'error');
          } catch (e) {
            console.log('Toast error:', e);
            alert(data.message || 'Login failed');
          }
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        
        // Reset button state
        spinner.style.display = 'none';
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
        
        try {
          showToast('An error occurred. Please try again.', 'error');
        } catch (e) {
          console.log('Toast error:', e);
          alert('An error occurred. Please try again.');
        }
      });
    });

    // Handle forgot password form submission
    document.getElementById('forgot-password-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const spinner = document.getElementById('forgot-spinner');
      const submitBtn = this.querySelector('button[type="submit"]');
      
      spinner.style.display = 'block';
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';

      const formData = new FormData(this);

      fetch('../backend/auth/forgot_password.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showToast('Password reset link sent to your email!', 'success');
          toggleForgotPassword(); // Go back to login form
          this.reset(); // Clear the form
        } else {
          showToast(data.message, 'error');
        }
        spinner.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Reset Link';
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        spinner.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Reset Link';
      });
    });

    // Email validation
    document.getElementById('email').addEventListener('input', function() {
      const emailFeedback = document.getElementById('emailFeedback');
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      emailFeedback.textContent = regex.test(this.value) ? "" : "Enter a valid email address";
    });
    
    // Debug: Test if JavaScript is working
    console.log('Login page JavaScript loaded successfully');
  </script>

<?php
// Include footer
include './includes/footer.php';
?>
