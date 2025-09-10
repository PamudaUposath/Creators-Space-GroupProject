<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

if ($isLoggedIn) {
    $user = [
        'id' => $_SESSION['user_id'],
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role'] ?? 'user'
    ];
}

// Handle any session messages
$message = $_SESSION['message'] ?? '';
if ($message) {
    unset($_SESSION['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="author" content="Anurag Vishwakarma Creators-Space">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - Creators-Space' : 'Creators-Space'; ?></title>
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
  <meta name="description" content="<?php echo isset($pageDescription) ? $pageDescription : 'Creators-Space - Welcome to the future of tech learning...'; ?>">
  <meta name="keywords" content="Creators-Space, coding, gwalior, technology">
  <link rel="stylesheet" href="./src/css/utils.css">
  <link rel="stylesheet" href="./src/css/style.css">
  <link rel="stylesheet" href="./src/css/responsive.css">
  <link rel="stylesheet" href="./src/css/mobile-components.css">
  <link rel="stylesheet" href="./src/css/newsletter.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
    integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Toast Notification Styles -->
  <style>
    .toast {
      background: #333;
      color: white;
      padding: 16px 20px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      transform: translateX(100%);
      transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
      opacity: 0;
      max-width: 350px;
      word-wrap: break-word;
      position: relative;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .toast.show {
      transform: translateX(0);
      opacity: 1;
    }
    
    .toast.success {
      background: #4CAF50;
    }
    
    .toast.error {
      background: #f44336;
    }
    
    .toast.warning {
      background: #ff9800;
    }
    
    .toast.info {
      background: #2196F3;
    }
    
    .toast-icon {
      font-size: 18px;
      flex-shrink: 0;
    }
    
    .toast-message {
      flex: 1;
    }
    
    .toast-close {
      background: none;
      border: none;
      color: white;
      font-size: 18px;
      cursor: pointer;
      padding: 0;
      margin-left: 10px;
      opacity: 0.7;
      flex-shrink: 0;
    }
    
    .toast-close:hover {
      opacity: 1;
    }
  </style>
  
  <?php if (isset($additionalCSS)): ?>
    <?php foreach ($additionalCSS as $css): ?>
      <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <?php if (isset($customStyles)): ?>
    <style><?php echo $customStyles; ?></style>
  <?php endif; ?>
</head>
<body<?php echo isset($bodyClass) ? ' class="' . $bodyClass . '"' : ''; ?>>

  <!-- Navigation Bar -->
  <div class="navbar">
    <div class="navbar-container">
      <h1>
          <a href="index.php">
              <img id="navbar-logo" width="80px" src="./assets/images/logo-nav-light.png" alt="logo Creators-Space">
              Creators-Space
          </a>
      </h1>
      
      <div class="navbar-right">
        <div class="nav-links align-items-center">
          <a href="index.php">Home</a>
          <a href="about.php">About Us</a>
          <a href="courses.php">Courses</a>
          <a href="services.php">Services</a>
          <a href="internship.php">Internship</a>
          <a href="campus-ambassador.php">Campus Ambassador</a>
          <?php if ($isLoggedIn): ?>
            <a href="bookmarked.php">BookMarks</a>
            <a href="projects.php">Projects</a>
          <?php endif; ?>
          <a href="blog.php">Blog</a>
          <a href="./certificate/">Certificates</a>
        </div>
          
        <!-- Authentication Section -->
        <?php if (!$isLoggedIn): ?>
          <div id="authSection">
            <a href="./login.php" class="btn login">Log In</a>
            <a href="./signup.php" class="btn signup">Sign Up</a>
          </div>
        <?php else: ?>
          <div id="userSection">
            <span>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</span>
            <?php if ($user['role'] === 'admin'): ?>
              <a href="backend/admin/dashboard.php" class="btn admin-btn">Admin Panel</a>
            <?php endif; ?>
            <a href="profile.php" class="btn profile-btn">Profile</a>
            <a href="backend/auth/logout.php" class="btn logout-btn">Logout</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>

  <!-- Main Content Starts Here -->
