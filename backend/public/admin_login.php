<?php
// backend/public/admin_login.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// If already logged in as admin, redirect to dashboard
if (isAdmin()) {
    header('Location: /backend/admin/dashboard.php');
    exit;
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    if (!checkRateLimit('admin_login_' . $_SERVER['REMOTE_ADDR'])) {
        $error = 'Too many login attempts. Please try again later.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (!$email || empty($password)) {
            $error = 'Email and password are required.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    SELECT id, first_name, last_name, email, username, password_hash, role, is_active 
                    FROM users 
                    WHERE email = ? AND role IN ('admin', 'instructor')
                    LIMIT 1
                ");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user || !password_verify($password, $user['password_hash'])) {
                    logActivity($user['id'] ?? 0, 'failed_admin_login', "Failed admin login attempt for: $email");
                    $error = 'Invalid credentials or insufficient privileges.';
                } elseif (!$user['is_active']) {
                    $error = 'Account is deactivated.';
                } else {
                    // Successful login
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['last_name'] = $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in_at'] = time();

                    logActivity($user['id'], 'admin_login', "Admin login successful for: $email");
                    
                    header('Location: /backend/admin/dashboard.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Admin login error: " . $e->getMessage());
                $error = 'Login failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Creators-Space</title>
    <link rel="shortcut icon" href="../../frontend/favicon.ico" type="image/x-icon">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        .container {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .logo {
            margin-bottom: 1rem;
        }
        .logo img {
            width: 80px;
            height: auto;
        }
        h2 {
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 24px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-1px);
        }
        .error {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 1rem;
            font-size: 14px;
        }
        .back-link {
            margin-top: 1.5rem;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .admin-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 1rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/frontend/assets/images/logo.png" alt="Creators-Space Logo" onerror="this.style.display='none'">
        </div>
        
        <div class="admin-badge">ADMIN PANEL</div>
        <h2>Admin Login</h2>
        <p class="subtitle">Access the administration dashboard</p>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                       placeholder="Enter your admin email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn">Login to Admin Panel</button>
        </form>
        
        <div class="back-link">
            <a href="../../frontend/index.php">← Back to Website</a>
        </div>
    </div>
</body>
</html>
