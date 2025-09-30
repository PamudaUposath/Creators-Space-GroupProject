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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
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
      backdrop-filter: blur(10px);
      padding: 2.5rem;
      border-radius: 20px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      font-size: 14px;
      color: #64748b;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .back-link:hover {
      color: #334155;
    }

    h2 {
      text-align: center;
      margin-bottom: 0.5rem;
      font-size: 2rem;
      font-weight: 700;
      color: #1e293b;
    }

    .subtitle {
      text-align: center;
      color: #64748b;
      font-size: 0.95rem;
      margin-bottom: 2rem;
      font-weight: 400;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #374151;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .input-wrapper {
      position: relative;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.875rem 1rem;
      padding-right: 3rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.8);
      font-size: 1rem;
      font-weight: 400;
      transition: all 0.2s ease;
      color: #1f2937;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #667eea;
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    input[type="email"]::placeholder,
    input[type="password"]::placeholder {
      color: #9ca3af;
    }

    .toggle-password {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
      font-size: 1.1rem;
      transition: color 0.2s ease;
    }

    .toggle-password:hover {
      color: #374151;
    }

    .inline-feedback {
      font-size: 0.85rem;
      color: #dc2626;
      margin-top: 0.5rem;
      font-weight: 500;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-size: 0.95rem;
      margin-bottom: 1.5rem;
    }

    .checkbox-group input[type="checkbox"] {
      width: 1.1rem;
      height: 1.1rem;
      accent-color: #667eea;
      cursor: pointer;
    }

    .checkbox-group label {
      color: #374151;
      font-weight: 400;
      cursor: pointer;
      margin-bottom: 0;
    }

    .forgot-password {
      display: block;
      text-align: right;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .forgot-password:hover {
      color: #5b21b6;
    }

    .btn {
      width: 100%;
      padding: 0.875rem;
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-weight: 600;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn:disabled {
      background: #9ca3af;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .spinner {
      display: none;
      width: 28px;
      height: 28px;
      margin: 1rem auto 0;
      border: 3px solid rgba(102, 126, 234, 0.3);
      border-top: 3px solid #667eea;
      border-radius: 50%;
    }

    .error-message {
      background: rgba(254, 226, 226, 0.8);
      color: #dc2626;
      padding: 1rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      border: 1px solid #fecaca;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .signup-section {
      text-align: center;
      margin-top: 2rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e2e8f0;
    }

    .signup-section p {
      color: #64748b;
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    .signup-section a {
      color: #667eea;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .signup-section a:hover {
      color: #5b21b6;
    }

    .forgot-password-section {
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid #e2e8f0;
    }

    .forgot-form {
      display: none;
    }

    .forgot-form.active {
      display: block;
    }

    .forgot-form h3 {
      color: #1e293b;
      margin-bottom: 0.5rem;
      font-size: 1.5rem;
      font-weight: 600;
    }

    .forgot-form p {
      font-size: 0.95rem;
      color: #64748b;
      line-height: 1.5;
      margin-bottom: 1.5rem;
    }

    .back-to-login {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.95rem;
      color: #64748b;
      text-decoration: none;
      margin-top: 1rem;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .back-to-login:hover {
      color: #374151;
    }

    @media (max-width: 640px) {
      .main-content {
        padding: 1rem;
      }
      
      .container {
        padding: 2rem 1.5rem;
        border-radius: 16px;
      }
      
      h2 {
        font-size: 1.75rem;
      }
    }

    /* Message styles */
    .error-message {
      background: rgba(254, 226, 226, 0.8);
      color: #dc2626;
      padding: 1rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      border: 1px solid #fecaca;
      font-weight: 500;
      font-size: 0.95rem;
    }

    .success-message {
      background: rgba(220, 252, 231, 0.8);
      color: #166534;
      padding: 1rem;
      border-radius: 12px;
      margin-bottom: 1.5rem;
      border: 1px solid #bbf7d0;
      font-weight: 500;
      font-size: 0.95rem;
    }
    .back-btn {
    background: linear-gradient(to right, #667eea, #764ba2); /* same as Sign In */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: 0.3s ease;
    }

    .back-btn:hover {
    opacity: 0.9; /* subtle hover effect like Sign In */
    }

  </style>
</head>

<body>
  <div class="main-content">
    <div class="container">
      <a href="index.php">
    <button type="button" class="back-btn"> Home </button>
    </a>

      <!--<a href="index.php" class="back-link">
        ← Back to Homepage
      </a>-->
    
      <h2>Welcome Back</h2>
      <p class="subtitle">Sign in to your account to continue</p>

      <?php if ($error): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <!-- Message Container for inline feedback -->
      <div id="message-container"></div>

      <!-- Login Form -->
      <form id="login-form" method="POST">
        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <input type="email" id="email" name="email" placeholder="Enter your email" required />
          </div>
          <div id="emailFeedback" class="inline-feedback"></div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" placeholder="Enter your password" required />
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
          </div>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" id="remember" name="remember_me">
          <label for="remember">Remember me for 30 days</label>
        </div>

        <button type="submit" class="btn" id="loginBtn">Sign In</button>
        <div class="spinner" id="spinner"></div>

        <a href="#" class="forgot-password" onclick="toggleForgotPassword()">Forgot your password?</a>

        <div class="signup-section">
          <p>Don't have an account? <a href="signup.php">Create one now</a></p>
          <p><small style="color: #64748b;">Students and instructors can use the same login</small></p>
        </div>
      </form>

      <!-- Forgot Password Form -->
      <div class="forgot-password-section">
        <div id="forgot-form" class="forgot-form">
          <h3>Reset Password</h3>
          <p>
            Enter your email address and we'll send you a secure link to reset your password.
          </p>
          <form id="forgot-password-form" method="POST" action="../backend/auth/forgot_password.php">
            <div class="form-group">
              <label for="reset-email">Email Address</label>
              <div class="input-wrapper">
                <input type="email" id="reset-email" name="email" placeholder="Enter your email" required />
              </div>
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
            <div class="spinner" id="forgot-spinner"></div>
          </form>
          <a href="#" onclick="toggleForgotPassword()" class="back-to-login">← Back to Login</a>
        </div>
      </div>
  </div>
  </div> <!-- Close main-content -->

  <script>
    // Message display function
    function showMessage(message, type = 'error') {
      const container = document.getElementById('message-container');
      const className = type === 'success' ? 'success-message' : 'error-message';
      container.innerHTML = `<div class="${className}">${message}</div>`;
      
      // Auto-hide after 10 seconds for success messages (longer to let user read)
      if (type === 'success') {
        setTimeout(() => {
          container.innerHTML = '';
        }, 10000);
      }
    }

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
      // Build a robust backend endpoint that works when the app is served
      // from a subfolder (Apache: /Creators-Space-GroupProject/) or from
      // project root (php -S). We derive the project root from the current
      // path using '/frontend' as an anchor.
      // Prefer the server-computed PROJECT_BASE helper (set in header.php).
      let loginUrl;
      if (window.apiUrl) {
        loginUrl = window.apiUrl('/backend/auth/login_process.php') + '?t=' + Date.now();
      } else {
        const origin = window.location.origin;
        const pathname = window.location.pathname;
        const projectRootPrefix = pathname.includes('/frontend') ? pathname.substring(0, pathname.indexOf('/frontend')) : '';
        loginUrl = origin + projectRootPrefix + '/backend/auth/login_process.php?t=' + Date.now();
      }
      console.log('Attempting login with URL:', loginUrl);

      fetch(loginUrl, {
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
          
          showMessage('Login successful! Redirecting...', 'success');
          
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
          showMessage(data.message || 'Login failed', 'error');
        }
      })
      .catch(error => {
        console.error('Fetch error:', error);
        
        // Reset button state
        spinner.style.display = 'none';
        loginBtn.disabled = false;
        loginBtn.textContent = 'Login';
        
        showMessage('An error occurred. Please try again.', 'error');
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
      let forgotUrl;
      if (window.apiUrl) {
        forgotUrl = window.apiUrl('/backend/auth/forgot_password.php');
      } else {
        const originFP = window.location.origin;
        const pathnameFP = window.location.pathname;
        const projectRootPrefixFP = pathnameFP.includes('/frontend') ? pathnameFP.substring(0, pathnameFP.indexOf('/frontend')) : '';
        forgotUrl = originFP + projectRootPrefixFP + '/backend/auth/forgot_password.php';
      }

      fetch(forgotUrl, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showMessage('Password reset link sent to your email!', 'success');
          toggleForgotPassword(); // Go back to login form
          this.reset(); // Clear the form
        } else {
          showMessage(data.message, 'error');
        }
        spinner.style.display = 'none';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Reset Link';
      })
      .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred. Please try again.', 'error');
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

</body>
</html>
