<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;

if ($isLoggedIn) {
    // Fetch current user data from database to get latest profile image
    try {
        require_once __DIR__ . '/../../backend/config/db_connect.php';
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, username, role, profile_image
            FROM users 
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dbUser) {
            $user = $dbUser;
        } else {
            // Fallback to session data if database query fails
            $user = [
                'id' => $_SESSION['user_id'],
                'first_name' => $_SESSION['first_name'] ?? '',
                'last_name' => $_SESSION['last_name'] ?? '',
                'email' => $_SESSION['email'] ?? '',
                'role' => $_SESSION['role'] ?? 'user',
                'profile_image' => null
            ];
        }
    } catch (PDOException $e) {
        // Fallback to session data if database connection fails
        $user = [
            'id' => $_SESSION['user_id'],
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? 'user',
            'profile_image' => null
        ];
    }
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
  <?php
  // Compute a project base URL so frontend JS can reliably build backend endpoints
  $script_dir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
  $projectRoot = '';
  if (strpos($script_dir, '/frontend') !== false) {
      $projectRoot = substr($script_dir, 0, strpos($script_dir, '/frontend'));
  }
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $projectBase = rtrim($scheme . '://' . $host . $projectRoot, '/');
  ?>
  <script>
    // Global base used by frontend JS to build API/backend URLs.
    window.PROJECT_BASE = '<?php echo $projectBase; ?>';
    // Helper to build an absolute API path under the project
    window.apiUrl = function(path) {
      // Ensure leading slash on path
      if (!path.startsWith('/')) path = '/' + path;
      return window.PROJECT_BASE + path;
    };
  </script>
  <link rel="stylesheet" href="./src/css/responsive.css">
  <link rel="stylesheet" href="./src/css/mobile-components.css">
  <link rel="stylesheet" href="./src/css/newsletter.css">
  <link rel="stylesheet" href="./src/css/enhanced-modern.css">
  <link rel="stylesheet" href="./src/css/go-to-top.css">
  <link rel="stylesheet" href="./src/css/ai-agent.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
    integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Load additional page-specific CSS -->
  <?php if (isset($additionalCSS) && is_array($additionalCSS)): ?>
    <?php foreach ($additionalCSS as $css): ?>
      <link rel="stylesheet" href="<?php echo $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
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

    /* Enhanced Navbar Styles - Applied to All Pages */
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding-top: 80px;
    }

    /* Modern Stylish Navbar */
    .navbar {
      position: fixed !important;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: linear-gradient(135deg, rgba(40,40,80,0.9) 0%, rgba(60,60,100,0.9) 100%) !important;
      backdrop-filter: blur(30px);
      -webkit-backdrop-filter: blur(30px);
      border-bottom: 1px solid rgba(255,255,255,0.2) !important;
      padding: 0.8rem 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 40px rgba(0,0,0,0.15) !important;
      height: auto !important;
    }
    
    .navbar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .navbar:hover::before {
      opacity: 1;
    }
    
    .navbar-container {
      max-width: 1400px !important;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: flex-start;
      padding: 0 2rem !important;
      position: relative;
      z-index: 2;
      height: 100% !important;
      margin-right: auto!important;
    }
    
    .navbar-right {
      display: flex;
      align-items: center;
      gap: 2rem;
      margin-left: auto;
      justify-content: flex-end;
      margin-left: auto!important;
    }
    
    /* Logo Section */
    .navbar h1 {
      margin: 0 !important;
      position: relative;
      margin-right: auto;
      font-size: 24px !important;
      font-weight: bold !important;
      color: black !important;
    }
    
    .navbar h1 a {
      display: flex !important;
      align-items: center;
      gap: 0.8rem !important;
      text-decoration: none;
      color: #ffffff !important;
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: -0.02em;
      transition: all 0.3s ease;
      text-shadow: 0 2px 10px rgba(0,0,0,0.5);
      width: auto;
    }
    
    .navbar h1 a:hover {
      color: #667eea !important;
      text-shadow: 0 0 20px rgba(102,126,234,0.8);
      transform: translateY(-1px);
    }
    
    #navbar-logo {
      width: 50px !important;
      height: 50px !important;
      object-fit: contain;
      transition: all 0.3s ease;
    }
    
    .navbar h1 a:hover #navbar-logo {
      transform: scale(1.05);
      filter: brightness(1.1);
    }
    
    /* Navigation Links */
    .navbar .nav-links {
      display: flex !important;
      align-items: center;
      gap: 2rem !important;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    
    .navbar .nav-links a {
      position: relative;
      color: #ffffff !important;
      text-decoration: none;
      padding: 0.5rem 0;
      font-weight: 500;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
      transition: all 0.3s ease;
      border-bottom: 2px solid transparent;
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
      margin: 10px 2px;
    }
    
    .navbar .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      transition: width 0.3s ease;
    }
    
    .navbar .nav-links a:hover {
      color: #ffffff !important;
      text-shadow: 0 0 8px rgba(255,255,255,0.6);
    }
    
    .navbar .nav-links a:hover::after {
      width: 100%;
    }

    /* Cart Link Styling */
    .cart-link {
      position: relative;
      margin-left: 1rem;
    }

    .cart-counter {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ff4757;
      color: white;
      border-radius: 50%;
      width: 18px;
      height: 18px;
      font-size: 11px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      line-height: 1;
      text-align: center;
      box-sizing: border-box;
      padding: 0;
      margin: 0;
    }

    /* Enhanced Dropdown Styles */
    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-toggle {
      display: flex !important;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
      padding: 0.5rem 0;
      color: #ffffff !important;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.95rem;
      letter-spacing: 0.3px;
      transition: all 0.3s ease;
      position: relative;
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
      margin: 10px 2px;
    }

    .dropdown-toggle::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      transition: width 0.3s ease;
    }

    .dropdown-toggle:hover::after {
      width: 100%;
    }

    .dropdown-toggle i {
      font-size: 12px;
      transition: transform 0.3s ease;
    }

    .dropdown:hover .dropdown-toggle i,
    .dropdown.active .dropdown-toggle i {
      transform: rotate(180deg);
    }

    .dropdown-menu {
      position: absolute;
      top: 100%;
      left: 0;
      background: linear-gradient(135deg, rgba(40,40,80,0.95) 0%, rgba(60,60,100,0.95) 100%);
      min-width: 200px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      border-radius: 12px;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: all 0.3s ease;
      z-index: 1000;
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(30px);
      display: flex !important;
      flex-direction: column !important;
      overflow: hidden;
    }

    .dropdown:hover .dropdown-menu,
    .dropdown.active .dropdown-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .dropdown-menu a {
      display: block !important;
      padding: 14px 20px;
      color: #ffffff !important;
      text-decoration: none;
      font-weight: 500;
      font-size: 0.9rem;
      transition: all 0.3s ease;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
      margin: 0 !important;
      width: 100%;
      clear: both;
      text-align: left !important;
      flex-shrink: 0 !important;
      white-space: nowrap;
      position: relative;
      letter-spacing: 0.3px;
    }

    .dropdown-menu a:last-child {
      border-bottom: none;
    }

    .dropdown-menu a:hover {
      background: linear-gradient(135deg, rgba(102,126,234,0.2) 0%, rgba(118,75,162,0.2) 100%);
      color: #667eea !important;
      text-shadow: 0 0 8px rgba(102,126,234,0.5);
      text-align: left !important;
      padding-left: 24px;
      transform: translateX(4px);
    }

    .dropdown-menu a:first-child {
      border-top-left-radius: 12px;
      border-top-right-radius: 12px;
    }

    .dropdown-menu a:last-child {
      border-bottom-left-radius: 12px;
      border-bottom-right-radius: 12px;
    }
    
    /* Authentication Section */
    #authSection, #userSection {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    #userSection {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 25px;
      padding: 0.4rem 0.8rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      max-width: fit-content;
    }
    
    #userSection span {
      color: #ffffff !important;
      font-weight: 500;
      font-size: 0.75rem;
      margin-right: 0.2rem;
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
      white-space: nowrap;
      max-width: 60px;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    /* Modern Button Styles */
    .navbar .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.6rem 1rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.85rem;
      letter-spacing: 0.3px;
      border: 1px solid transparent;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      backdrop-filter: blur(20px);
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
      color: #ffffff !important;
      margin: 10px 2px;
    }
    
    .navbar .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .navbar .btn:hover::before {
      left: 100%;
    }
    
    .navbar .btn.login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      color: #ffffff !important;
      border-color: rgba(255,255,255,0.2) !important;
      box-shadow: 0 8px 25px rgba(102,126,234,0.3);
    }
    
    .navbar .btn.signup {
      background: rgba(255,255,255,0.1) !important;
      color: #ffffff !important;
      border-color: rgba(255,255,255,0.3) !important;
      box-shadow: 0 8px 25px rgba(255,255,255,0.1);
    }
    
    .navbar .btn.profile-btn {
      background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important;
      color: #ffffff !important;
      border-color: rgba(255,255,255,0.2) !important;
      box-shadow: 0 8px 25px rgba(76,175,80,0.3);
      font-size: 0.9rem !important;
      padding: 0 !important;
      width: 35px !important;
      height: 35px !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-width: 35px !important;
      max-width: 35px !important;
      min-height: 35px !important;
      max-height: 35px !important;
      text-align: center !important;
      line-height: 1 !important;
    }
    
    .navbar .btn.admin-btn {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
      color: #ffffff !important;
      border-color: rgba(255,255,255,0.2);
      box-shadow: 0 8px 25px rgba(255,107,107,0.3);
      font-size: 0.65rem;
      padding: 0.3rem 0.5rem;
    }
    
    .navbar .btn.logout-btn {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
      color: #ffffff !important;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 8px;
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.5rem 1rem;
      text-align: center;
      min-width: auto;
      white-space: nowrap;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2);
    }
    
    .navbar .btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .navbar .btn.login:hover {
      box-shadow: 0 15px 35px rgba(102,126,234,0.4);
    }
    
    .navbar .btn.signup:hover {
      background: rgba(255,255,255,0.2) !important;
      box-shadow: 0 15px 35px rgba(255,255,255,0.2);
    }
    
    .navbar .btn.profile-btn:hover {
      box-shadow: 0 15px 35px rgba(76,175,80,0.4);
    }
    
    .navbar-profile-img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
    }
    
    .navbar-profile-img:hover {
      border-color: rgba(255, 255, 255, 0.8);
      transform: scale(1.1);
    }
    
    body.dark-mode .navbar-profile-img {
      border-color: rgba(100, 181, 246, 0.5);
    }
    
    body.dark-mode .navbar-profile-img:hover {
      border-color: rgba(100, 181, 246, 1);
    }
    
    .navbar .btn.admin-btn:hover {
      box-shadow: 0 15px 35px rgba(255,107,107,0.4);
    }
    
    .navbar .btn.logout-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
      background: linear-gradient(135deg, #ff5252 0%, #f44336 100%);
      border-color: rgba(255, 255, 255, 0.3);
    }

    /* Theme Toggle Button */
    .theme-toggle {
      display: flex;
      align-items: center;
      margin-left: 1rem;
    }

    .theme-btn {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #ffffff;
      padding: 0.6rem;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-size: 1rem;
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(20px);
      position: relative;
      overflow: hidden;
    }

    .theme-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s ease;
    }

    .theme-btn:hover::before {
      left: 100%;
    }

    .theme-btn:hover {
      background: rgba(255,255,255,0.2);
      border-color: rgba(255,255,255,0.3);
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 8px 25px rgba(255,255,255,0.1);
    }

    .theme-btn:active {
      transform: translateY(0) scale(0.95);
    }

    #theme-icon {
      transition: all 0.3s ease;
      font-size: 1.1rem;
    }

    .theme-btn:hover #theme-icon {
      transform: rotate(15deg);
    }

    /* Dark mode styles */
    body.dark-mode {
      background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
      color: #ffffff;
    }

    body.dark-mode .navbar {
      background: linear-gradient(135deg, rgba(10,10,20,0.95) 0%, rgba(20,20,40,0.95) 100%) !important;
      border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    }

    body.dark-mode .theme-btn {
      background: rgba(255,255,255,0.15);
      border-color: rgba(255,255,255,0.25);
    }

    body.dark-mode .theme-btn:hover {
      background: rgba(255,255,255,0.25);
      border-color: rgba(255,255,255,0.35);
    }

    /* Dark mode for all content sections */
    body.dark-mode .main-content,
    body.dark-mode .hero-section,
    body.dark-mode .featured-courses-section,
    body.dark-mode .technologies-section,
    body.dark-mode .testimonials-section,
    body.dark-mode .section,
    body.dark-mode .container {
      background: transparent;
      color: #ffffff;
    }

    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3,
    body.dark-mode h4,
    body.dark-mode h5,
    body.dark-mode h6 {
      color: #ffffff;
    }

    body.dark-mode p,
    body.dark-mode span,
    body.dark-mode div {
      color: #e0e0e0;
    }

    /* Dark mode for cards and components */
    body.dark-mode .course-card,
    body.dark-mode .tech-card,
    body.dark-mode .blog-card,
    body.dark-mode .project-card,
    body.dark-mode .card {
      background: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      color: #ffffff;
    }

    /* If the site needs card backgrounds dark but card text should remain black, force it here */
    body.dark-mode .card,
    body.dark-mode .card h1,
    body.dark-mode .card h2,
    body.dark-mode .card h3,
    body.dark-mode .card h4,
    body.dark-mode .card p,
    body.dark-mode .card span,
    body.dark-mode .card a {
      color: #000000 !important;
    }

    body.dark-mode .course-card:hover,
    body.dark-mode .tech-card:hover,
    body.dark-mode .blog-card:hover,
    body.dark-mode .project-card:hover,
    body.dark-mode .card:hover {
      background: rgba(255,255,255,0.1);
      border-color: rgba(255,255,255,0.2);
    }

    /* Dark mode for forms and inputs */
    body.dark-mode input,
    body.dark-mode textarea,
    body.dark-mode select {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #ffffff;
    }

    body.dark-mode input::placeholder,
    body.dark-mode textarea::placeholder {
      color: rgba(255,255,255,0.6);
    }

    /* Dark mode for buttons */
    body.dark-mode .btn:not(.hero-btn):not(.course-btn):not(.cta-btn),
    body.dark-mode button:not(.hero-btn):not(.course-btn):not(.cta-btn) {
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      color: #ffffff;
    }

    body.dark-mode .btn:hover:not(.hero-btn):not(.course-btn):not(.cta-btn),
    body.dark-mode button:hover:not(.hero-btn):not(.course-btn):not(.cta-btn) {
      background: rgba(255,255,255,0.2);
      border-color: rgba(255,255,255,0.3);
    }

    /* Dark mode for specific sections */
    body.dark-mode .featured-courses-section {
      background: linear-gradient(135deg, #1e1e2e 0%, #2a2a3e 100%);
    }

    body.dark-mode .technologies-section {
      background: rgba(255,255,255,0.02);
    }

    body.dark-mode .section-title {
      color: #ffffff;
    }

    body.dark-mode .section-subtitle {
      color: #b0b0b0;
    }

    /* Dark mode for footer */
    body.dark-mode footer {
      background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
      color: #ffffff;
    }

    /* Dark mode for tables */
    body.dark-mode table {
      background: rgba(255,255,255,0.05);
      color: #ffffff;
    }

    body.dark-mode th,
    body.dark-mode td {
      border-color: rgba(255,255,255,0.1);
      color: #ffffff;
    }

    /* Dark mode for modals and overlays */
    body.dark-mode .modal,
    body.dark-mode .overlay {
      background: rgba(0,0,0,0.8);
      color: #ffffff;
    }

    body.dark-mode .modal-content {
      background: #1a1a2e;
      border: 1px solid rgba(255,255,255,0.1);
    }

    /* Mobile Responsive Design */
    @media (max-width: 768px) {
      .navbar .nav-links {
        position: fixed !important;
        top: 80px !important;
        left: 0 !important;
        right: 0 !important;
        background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(30,30,60,0.95) 100%) !important;
        backdrop-filter: blur(30px) !important;
        flex-direction: column !important;
        gap: 0 !important;
        padding: 2rem 0 !important;
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        width: 100vw !important;
        height: 100vh !important;
        justify-content: center !important;
        z-index: 1000 !important;
      }
      
      .navbar .nav-links.active {
        transform: translateX(0) !important;
        display: flex !important;
      }
      
      .navbar .nav-links a {
        width: 100%;
        text-align: center;
        padding: 1.5rem 2rem !important;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 1rem !important;
        margin: 0 !important;
      }
      
      .navbar .nav-links a::after {
        display: none;
      }
      
      .navbar .nav-links a:hover {
        background: rgba(102,126,234,0.1);
        color: #667eea !important;
      }

      /* Mobile dropdown styles */
      .dropdown {
        width: 100%;
      }

      .dropdown-toggle {
        width: 100%;
        justify-content: center;
        padding: 1.5rem 2rem !important;
        font-size: 1rem !important;
        border-bottom: 1px solid rgba(255,255,255,0.1);
      }

      .dropdown-menu {
        position: static;
        width: 100%;
        box-shadow: none;
        border: none;
        background: rgba(102,126,234,0.1);
        border-radius: 0;
        opacity: 1;
        visibility: visible;
        transform: none;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
      }

      .dropdown.active .dropdown-menu {
        max-height: 300px;
        padding: 0.5rem 0;
      }

      .dropdown-menu a {
        padding: 1rem 2rem !important;
        font-size: 0.95rem !important;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        background: transparent;
      }

      .dropdown-menu a:hover {
        background: rgba(102,126,234,0.2);
        padding-left: 2.5rem !important;
      }
      
      #authSection, #userSection {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.9);
        backdrop-filter: blur(30px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 25px;
        padding: 1rem 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      }
      
      .theme-toggle {
        position: fixed;
        top: 15px;
        right: 80px;
        z-index: 1001;
      }
      
      .navbar h1 a {
        font-size: 1.2rem !important;
      }
      
      #navbar-logo {
        width: 40px !important;
        height: 40px !important;
      }
    }

    /* Navbar Scroll Effect */
    .navbar.scrolled {
      background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(20,20,40,0.95) 100%) !important;
      box-shadow: 0 10px 50px rgba(0,0,0,0.3) !important;
      padding: 0.5rem 0;
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
          
          <!-- Learning Dropdown -->
          <div class="dropdown">
            <a href="#" class="dropdown-toggle">Learning <i class="fas fa-chevron-down"></i></a>
            <div class="dropdown-menu">
              <a href="courses.php">Courses</a>
              <a href="internship.php">Internship</a>
            </div>
          </div>
          
          <!-- Resources Dropdown -->
          <div class="dropdown">
            <a href="#" class="dropdown-toggle">Resources <i class="fas fa-chevron-down"></i></a>
            <div class="dropdown-menu">
              <!-- <a href="./blog.php">Blog</a> -->
              <a href="./projects.php">Projects</a>
              <a href="./certificate.php">Verify Certificates</a>
            </div>
          </div>
          
          <a href="services.php">Services</a>
          
          <!-- My Courses - Only show when logged in -->
          <?php if ($isLoggedIn): ?>
            <a href="mycourses.php">My Courses</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'user'): ?>
              <a href="student-messages.php">Messages</a>
            <?php endif; ?>
            <a href="cart.php" class="cart-link">
              <i class="fas fa-shopping-cart"></i>
              <span class="cart-counter" style="display: none;">0</span>
            </a>
          <?php endif; ?>
          
          <!-- Dark/Light Mode Toggle -->
          <div class="theme-toggle">
            <button id="theme-toggle-btn" class="theme-btn" title="Toggle Dark/Light Mode">
              <i class="fas fa-moon" id="theme-icon"></i>
            </button>
          </div>
        </div>
          
        <!-- Authentication Section -->
        <?php if (!$isLoggedIn): ?>
          <div id="authSection">
            <a href="./login.php" class="btn login">Log In</a>
            <a href="./signup.php" class="btn signup">Sign Up</a>
          </div>
        <?php else: ?>
          <div id="userSection">
            <?php if ($user['role'] === 'admin'): ?>
              <a href="../backend/admin/dashboard.php" class="btn admin-btn">Admin Panel</a>
            <?php endif; ?>
            <a href="profile.php" class="btn profile-btn" title="Profile">
              <?php if (isset($user['profile_image']) && $user['profile_image']): ?>
                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="navbar-profile-img" id="navbarProfileImg">
              <?php else: ?>
                <i class="fas fa-user"></i>
              <?php endif; ?>
            </a>
            <a href="../backend/auth/logout.php" class="btn logout-btn">Logout</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Toast Notification Container -->
  <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>

  <!-- Theme Toggle JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const themeToggleBtn = document.getElementById('theme-toggle-btn');
      const themeIcon = document.getElementById('theme-icon');
      
      // Load saved theme preference
      const savedTheme = localStorage.getItem('theme');
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeIcon.className = 'fas fa-sun';
      } else {
        themeIcon.className = 'fas fa-moon';
      }
      
      // Theme toggle functionality
      themeToggleBtn.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        
        if (document.body.classList.contains('dark-mode')) {
          themeIcon.className = 'fas fa-sun';
          localStorage.setItem('theme', 'dark');
        } else {
          themeIcon.className = 'fas fa-moon';
          localStorage.setItem('theme', 'light');
        }
        
        // Add a little animation to the button
        themeToggleBtn.style.transform = 'scale(0.9)';
        setTimeout(() => {
          themeToggleBtn.style.transform = '';
        }, 150);
      });
    });

    // Cart counter functionality
    async function updateCartCounter() {
      try {
        const response = await fetch('../backend/api/cart.php');
        const data = await response.json();
        
        const counter = document.querySelector('.cart-counter');
        if (counter) {
          if (data.success && data.items && data.items.length > 0) {
            const totalItems = data.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);
            counter.textContent = totalItems;
            counter.style.display = 'flex';
          } else {
            counter.style.display = 'none';
          }
        }
      } catch (error) {
        console.log('Could not update cart counter:', error);
      }
    }

    // Make updateCartCounter globally available
    window.updateCartCounter = updateCartCounter;

    // Update cart counter when page loads (for logged in users only)
    <?php if ($isLoggedIn): ?>
    document.addEventListener('DOMContentLoaded', updateCartCounter);
    <?php endif; ?>
  </script>
  
  <!-- Load additional page-specific JavaScript -->
  <?php if (isset($additionalJS) && is_array($additionalJS)): ?>
    <?php foreach ($additionalJS as $js): ?>
      <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- AI Learning Assistant -->
  <script src="./src/js/ai-agent.js"></script>

  <!-- Main Content Starts Here -->
