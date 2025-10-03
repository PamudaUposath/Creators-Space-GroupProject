<?php
// frontend/change-password.php
session_start();

// Check if user is coming from email link with token
$token = $_GET['token'] ?? '';
$fromEmail = !empty($token);

// If no token and not logged in, redirect to login
if (!$fromEmail && !isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Please login to change your password.';
    header('Location: login.php');
    exit;
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
if ($error) unset($_SESSION['error']);
if ($success) unset($_SESSION['success']);

// Set page-specific variables
$pageTitle = "Change Password";
$pageDescription = "Update your account password";
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

    .password-requirements {
      background: rgba(239, 246, 255, 0.8);
      border: 1px solid #dbeafe;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      font-size: 0.85rem;
    }

    .password-requirements h4 {
      color: #1e40af;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }

    .password-requirements ul {
      color: #3730a3;
      margin-left: 1rem;
    }

    .password-requirements li {
      margin-bottom: 0.25rem;
    }

    .spinner {
      display: none;
      width: 20px;
      height: 20px;
      margin-left: 0.5rem;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .welcome-info {
      background: rgba(240, 253, 244, 0.8);
      border: 1px solid #bbf7d0;
      border-radius: 8px;
      padding: 1rem;
      margin-bottom: 1.5rem;
      font-size: 0.9rem;
      color: #166534;
    }
</style>

<div class="main-content">
    <div class="container">
        <a href="<?php echo $fromEmail ? 'login.php' : 'index.php'; ?>" class="back-link">
            ← Back to <?php echo $fromEmail ? 'Login' : 'Home'; ?>
        </a>

        <h2>Change Password</h2>
        <p class="subtitle">
            <?php if ($fromEmail): ?>
                Welcome! Please set up your new password to get started.
            <?php else: ?>
                Update your account password for better security.
            <?php endif; ?>
        </p>

        <?php if ($fromEmail): ?>
            <div class="welcome-info">
                <strong>Welcome to Creators-Space!</strong><br>
                You've been added as an instructor. Please create a secure password to complete your account setup.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div id="message-container"></div>

        <form id="change-password-form" method="POST">
            <?php if ($fromEmail): ?>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <?php endif; ?>
            
            <?php if ($fromEmail): ?>
                <div class="form-group">
                    <label for="temp_password">Current Temporary Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="temp_password" name="temp_password" 
                               placeholder="Enter temporary password from email" required>
                        <span class="toggle-password" onclick="togglePassword('temp_password')">👁️</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="current_password" name="current_password" 
                               placeholder="Enter your current password" required>
                        <span class="toggle-password" onclick="togglePassword('current_password')">👁️</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li>At least 8 characters long</li>
                    <li>Contains uppercase and lowercase letters</li>
                    <li>Contains at least one number</li>
                    <li>Contains at least one special character</li>
                </ul>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="new_password" name="new_password" 
                           placeholder="Enter your new password" required>
                    <span class="toggle-password" onclick="togglePassword('new_password')">👁️</span>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" 
                           placeholder="Confirm your new password" required>
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                </div>
            </div>

            <button type="submit" class="btn" id="changeBtn">
                Change Password
                <div class="spinner" id="spinner"></div>
            </button>
        </form>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }

    function showMessage(message, type = 'error') {
        const container = document.getElementById('message-container');
        const className = type === 'success' ? 'success-message' : 'error-message';
        container.innerHTML = `<div class="${className}">${message}</div>`;
        
        if (type === 'success') {
            setTimeout(() => {
                container.innerHTML = '';
            }, 5000);
        }
    }

    function validatePassword(password) {
        const minLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasLower = /[a-z]/.test(password);
        const hasNumber = /\d/.test(password);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        return minLength && hasUpper && hasLower && hasNumber && hasSpecial;
    }

    document.getElementById('change-password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        // Validate password strength
        if (!validatePassword(newPassword)) {
            showMessage('Password does not meet requirements. Please check the requirements above.', 'error');
            return;
        }
        
        // Validate password match
        if (newPassword !== confirmPassword) {
            showMessage('New password and confirmation do not match.', 'error');
            return;
        }
        
        const spinner = document.getElementById('spinner');
        const changeBtn = document.getElementById('changeBtn');
        
        spinner.style.display = 'inline-block';
        changeBtn.disabled = true;
        changeBtn.innerHTML = 'Changing Password... <div class="spinner" style="display: inline-block;"></div>';
        
        const formData = new FormData(this);
        
        // Determine the correct URL
        let changeUrl;
        if (window.apiUrl) {
            changeUrl = window.apiUrl('/backend/auth/change_password.php');
        } else {
            const origin = window.location.origin;
            const pathname = window.location.pathname;
            const projectRootPrefix = pathname.includes('/frontend') ? pathname.substring(0, pathname.indexOf('/frontend')) : '';
            changeUrl = origin + projectRootPrefix + '/backend/auth/change_password.php';
        }
        
        fetch(changeUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            spinner.style.display = 'none';
            changeBtn.disabled = false;
            changeBtn.innerHTML = 'Change Password';
            
            if (data.success) {
                showMessage('Password changed successfully! Redirecting to login...', 'success');
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 2000);
            } else {
                showMessage(data.message || 'Failed to change password. Please try again.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            spinner.style.display = 'none';
            changeBtn.disabled = false;
            changeBtn.innerHTML = 'Change Password';
            showMessage('An error occurred. Please try again.', 'error');
        });
    });
</script>

</body>
</html>