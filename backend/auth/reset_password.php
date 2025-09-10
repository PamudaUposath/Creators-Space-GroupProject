<?php
// backend/auth/reset_password.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = sanitizeInput($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($token)) {
        $error = 'Invalid reset token.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
        $error = 'Password must contain at least one lowercase letter, one uppercase letter, and one number.';
    } else {
        try {
            // Verify token and check expiration
            $stmt = $pdo->prepare("
                SELECT id, first_name, email 
                FROM users 
                WHERE reset_token = ? AND reset_expires > NOW() AND is_active = 1
            ");
            $stmt->execute([$token]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'Invalid or expired reset token.';
            } else {
                // Update password and clear reset token
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET password_hash = ?, reset_token = NULL, reset_expires = NULL 
                    WHERE id = ?
                ");
                $stmt->execute([$passwordHash, $user['id']]);

                // Log activity
                logActivity($user['id'], 'password_reset_completed', "Password reset completed for: " . $user['email']);

                $success = 'Password reset successfully! You can now log in with your new password.';
                
                // Clear the token variable to hide the form
                $token = '';
            }
        } catch (PDOException $e) {
            error_log("Reset password error: " . $e->getMessage());
            $error = 'Unable to reset password. Please try again.';
        }
    }
}

// If token is provided in URL, verify it's valid
if (!empty($token) && empty($error)) {
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM users 
            WHERE reset_token = ? AND reset_expires > NOW() AND is_active = 1
        ");
        $stmt->execute([$token]);
        if (!$stmt->fetch()) {
            $error = 'Invalid or expired reset token.';
            $token = '';
        }
    } catch (PDOException $e) {
        error_log("Token verification error: " . $e->getMessage());
        $error = 'Unable to verify reset token.';
        $token = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Creators-Space</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 150px rgba(0, 0, 0, 0.15);
            border: 1px solid #ddd;
        }
        h2 {
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: #0d0d0d;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover {
            background-color: #333;
        }
        .error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 1rem;
        }
        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 1rem;
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #0d0d0d;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <div class="back-link">
                <a href="/frontend/login.php">← Back to Login</a>
            </div>
        <?php elseif (!empty($token)): ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password" required 
                           minlength="8" placeholder="Enter new password">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           minlength="8" placeholder="Confirm new password">
                </div>
                
                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php else: ?>
            <p>Invalid or expired reset link. Please request a new password reset.</p>
            <div class="back-link">
                <a href="/frontend/login.php">← Back to Login</a>
            </div>
        <?php endif; ?>
        
        <?php if (!$success && !empty($token)): ?>
            <div class="back-link">
                <a href="/frontend/login.php">← Back to Login</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Client-side password confirmation validation
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
