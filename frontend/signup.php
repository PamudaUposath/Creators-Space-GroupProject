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
    input[type="password"],
    input[type="text"] {
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

    .login-page {
      display: block;
      text-align: center;
      margin-top: 1rem;
      font-size: 0.9rem;
      color: black;
      text-decoration: none;
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
  </style>
</head>

<body>
  <div class="main-content">
    <div class="container">
      <a href="index.php"
      style="display: inline-block; margin-bottom: 10px; font-size: 14px; color: #555; text-decoration: none;">
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
';

// Include footer
include './includes/footer.php';
?>
