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
      background: white;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    .main-content {
      display: flex;
      justify-content: center;
      align-items: center;
      flex: 1;
      padding: 2rem 1rem;
    }

    .container {
      background: white;
      padding: 2rem;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 150px rgba(0, 0, 0, 0.15);
      border: 1px solid #ccc;
      font-weight: 500;
    }

    h2 {
      text-align: center;
      margin-bottom: 1rem;
    }

    .form-group {
      margin-bottom: 1rem;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 5px;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      padding-right: 40px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .toggle-password {
      position: absolute;
      right: 10px;
      top: 38px;
      cursor: pointer;
      color: #555;
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
      color: black;
      text-decoration: none;
    }

    .signup-page {
      display: block;
      text-align: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: black;
      text-decoration: none;
    }

    .btn {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 6px;
      background-color: #0d0d0d;
      color: white;
      font-weight: bold;
      cursor: pointer;
    }

    .btn:disabled {
      background-color: #999;
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
    }

    .forgot-form {
      display: none;
    }

    .forgot-form.active {
      display: block;
    }
  </style>
</head>

<body>
  <div class="main-content">
    <div class="container">
      <a href="index.php"
      style="display: inline-block; margin-bottom: 10px; font-size: 14px; color: #555; text-decoration: none;">
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
        <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">
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
        <a href="#" onclick="toggleForgotPassword()" style="font-size: 0.9rem; color: #666;">← Back to Login</a>
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

      fetch('../backend/auth/login_process.php', {
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

      fetch('backend/auth/forgot_password.php', {
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
