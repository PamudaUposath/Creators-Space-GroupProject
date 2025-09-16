<?php
// frontend/signup.php
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
$pageTitle = "Sign Up";
$pageDescription = "Create your Creators-Space account";
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
    input[type="password"],
    input[type="text"] {
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
    input[type="password"]:focus,
    input[type="text"]:focus {
      outline: none;
      border-color: #667eea;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    /* Dark mode styles for inputs */
    body.dark input[type="email"],
    body.dark input[type="password"],
    body.dark input[type="text"] {
      background: #2d3748;
      border: 1px solid #4a5568;
      color: #ffffff;
    }

    body.dark input[type="email"]:focus,
    body.dark input[type="password"]:focus,
    body.dark input[type="text"]:focus {
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

    .login-page {
      display: block;
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
      color: #333;
      text-decoration: none;
    }

    .login-page a {
      color: #667eea;
      text-decoration: none;
      font-weight: 500;
    }

    .login-page a:hover {
      color: #4c51bf;
      text-decoration: underline;
    }

    /* Dark mode styles for login page link */
    body.dark .login-page {
      color: #e0e0e0;
    }

    body.dark .login-page a {
      color: #66b3ff;
    }

    body.dark .login-page a:hover {
      color: #4da6ff;
    }

    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 1rem;
      border: 1px solid #f5c6cb;
    }

    .success-message {
      background: #d4edda;
      color: #155724;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 1rem;
      border: 1px solid #c3e6cb;
    }

    .form-row {
      display: flex;
      gap: 10px;
    }

    .form-row .form-group {
      flex: 1;
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

    /* Dark mode styles for Terms and Conditions link */
    label a {
      color: #667eea;
      font-weight: 500;
    }

    label a:hover {
      color: #4c51bf;
    }

    body.dark label a {
      color: #66b3ff;
    }

    body.dark label a:hover {
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
    
    <h2>Sign Up</h2>

    <?php if ($error): ?>
      <div class="error-message">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <div id="message-container"></div>

    <form id="signup-form" method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>First Name</label>
          <input type="text" id="first_name" name="first_name" required />
        </div>
        <div class="form-group">
          <label>Last Name</label>
          <input type="text" id="last_name" name="last_name" />
        </div>
      </div>

      <div class="form-group">
        <label>Username (Optional)</label>
        <input type="text" id="username" name="username" placeholder="Choose a unique username" />
        <div id="usernameFeedback" class="inline-feedback"></div>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" id="email" name="email" required />
        <div id="emailFeedback" class="inline-feedback"></div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" id="password" name="password" required minlength="8" />
        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
        <div id="passwordFeedback" class="inline-feedback"></div>
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required />
        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
        <div id="matchFeedback" class="inline-feedback"></div>
      </div>
      
      <div class="form-group">
        <input type="checkbox" id="terms" required style="width:auto; margin-right:8px;" />
        <label for="terms" style="display:inline;">
          I agree to the <a href="tandc.php" target="_blank">Terms and Conditions</a>
        </label>
      </div>

      <button type="submit" class="btn" id="signupBtn">Sign Up</button>
      <div class="spinner" id="spinner"></div>

      <div class="login-page">
        <p>Already have an account? <a href="login.php">Login</a></p>
      </div>
    </form>
  </div>
  </div> <!-- Close main-content -->

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }

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

    // Real-time validation
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const email = document.getElementById("email");
    const username = document.getElementById("username");
    const matchFeedback = document.getElementById("matchFeedback");
    const emailFeedback = document.getElementById("emailFeedback");
    const passwordFeedback = document.getElementById("passwordFeedback");
    const usernameFeedback = document.getElementById("usernameFeedback");

    // Password strength validation
    password.addEventListener("input", function() {
      const value = this.value;
      let feedback = "";
      
      if (value.length > 0) {
        if (value.length < 8) {
          feedback = "Password must be at least 8 characters long";
        } else if (!/(?=.*[a-z])/.test(value)) {
          feedback = "Password must contain at least one lowercase letter";
        } else if (!/(?=.*[A-Z])/.test(value)) {
          feedback = "Password must contain at least one uppercase letter";
        } else if (!/(?=.*\d)/.test(value)) {
          feedback = "Password must contain at least one number";
        }
      }
      
      passwordFeedback.textContent = feedback;
    });

    // Confirm password validation
    confirmPassword.addEventListener("input", function() {
      matchFeedback.textContent = this.value !== password.value && this.value !== ""
        ? "Passwords do not match"
        : "";
    });

    // Email validation
    email.addEventListener("input", function() {
      const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      emailFeedback.textContent = this.value && !regex.test(this.value)
        ? "Enter a valid email address"
        : "";
    });

    // Username validation
    username.addEventListener("input", function() {
      const value = this.value;
      if (value.length > 0) {
        if (value.length < 3) {
          usernameFeedback.textContent = "Username must be at least 3 characters long";
        } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
          usernameFeedback.textContent = "Username can only contain letters, numbers, and underscores";
        } else {
          usernameFeedback.textContent = "";
        }
      } else {
        usernameFeedback.textContent = "";
      }
    });

    // Handle form submission
    document.getElementById('signup-form').addEventListener('submit', function(e) {
      e.preventDefault();

      // Client-side validation
      if (password.value.length < 8) {
        showToast("Password must be at least 8 characters long.", 'warning');
        return;
      }

      if (password.value !== confirmPassword.value) {
        showToast("Passwords do not match.", 'warning');
        return;
      }

      if (!email.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        showMessage("Please enter a valid email address.");
        return;
      }

      const spinner = document.getElementById('spinner');
      const signupBtn = document.getElementById('signupBtn');
      
      spinner.style.display = 'block';
      signupBtn.disabled = true;
      signupBtn.textContent = 'Creating Account...';

      const formData = new FormData(this);

      fetch('../backend/auth/signup_process.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showToast(data.message, 'success');
          this.reset(); // Clear the form
          
          // Disable the form and show redirect message
          signupBtn.disabled = true;
          signupBtn.textContent = 'Redirecting to Login...';
          
          // Redirect to login page after 2 seconds
          setTimeout(() => {
            window.location.href = 'login.php';
          }, 2000);
        } else {
          showToast(data.message, 'error');
          spinner.style.display = 'none';
          signupBtn.disabled = false;
          signupBtn.textContent = 'Sign Up';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
        spinner.style.display = 'none';
        signupBtn.disabled = false;
        signupBtn.textContent = 'Sign Up';
      });
    });
  </script>

<?php
// Include footer
include './includes/footer.php';
?>
