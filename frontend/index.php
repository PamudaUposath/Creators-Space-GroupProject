<?php
// frontend/index.php
session_start();

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
  <title>Creators-Space</title>
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
  <meta name="description" content="Creators-Space - Welcome to the future of tech learning...">
  <meta name="keywords" content="Creators-Space, coding, gwalior, technology">
  <link rel="stylesheet" href="./src/css/utils.css">
  <link rel="stylesheet" href="./src/css/style.css">
  <link rel="stylesheet" href="./src/css/responsive.css">
  <link rel="stylesheet" href="./src/css/mobile-components.css">
  <link rel="stylesheet" href="./src/css/newsletter.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
    integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- Enhanced Stylish CSS for Index Page -->
  <style>
    /* Modern Gradient Background */
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }
    
    /* Animated Background Particles */
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
      z-index: -1;
    }
    
    @keyframes particleFloat {
      0% { transform: translateY(0) rotate(0deg); }
      100% { transform: translateY(-100vh) rotate(360deg); }
    }
    
    /* Modern Stylish Navbar */
    .navbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background: linear-gradient(135deg, rgba(40,40,80,0.9) 0%, rgba(60,60,100,0.9) 100%);
      backdrop-filter: blur(30px);
      -webkit-backdrop-filter: blur(30px);
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding: 0.8rem 0;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 40px rgba(0,0,0,0.15);
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
      max-width: 1400px;
      margin: 0 auto;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      position: relative;
      z-index: 2;
    }
    
    .navbar-right {
      display: flex;
      align-items: center;
      gap: 2rem;
    }
    
    /* Logo Section */
    .navbar h1 {
      margin: 0;
      position: relative;
      margin-right: auto;
    }
    
    .navbar h1 a {
      display: flex;
      align-items: center;
      gap: 0.8rem;
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
      width: 50px;
      height: 50px;
      object-fit: contain;
      transition: all 0.3s ease;
    }
    
    .navbar h1 a:hover #navbar-logo {
      transform: scale(1.05);
      filter: brightness(1.1);
    }
    
    /* Navigation Links */
    .nav-links {
      display: flex;
      align-items: center;
      gap: 2rem;
      list-style: none;
      margin: 0;
      padding: 0;
    }
    
    .nav-links a {
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
    }
    
    .nav-links a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      transition: width 0.3s ease;
    }
    
    .nav-links a:hover {
      color: #ffffff !important;
      text-shadow: 0 0 8px rgba(255,255,255,0.6);
    }
    
    .nav-links a:hover::after {
      width: 100%;
    }
    
    /* Authentication Section */
    #authSection, #userSection {
      display: flex;
      align-items: center;
      gap: 0.8rem;
    }
    
    #userSection {
      background: rgba(255,255,255,0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 30px;
      padding: 0.8rem 1.5rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    #userSection span {
      color: #ffffff !important;
      font-weight: 500;
      font-size: 0.9rem;
      margin-right: 0.5rem;
      text-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    /* Modern Button Styles */
    .btn {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      padding: 0.6rem 1rem;
      border-radius: 20px;
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
    }
    
    .btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .btn:hover::before {
      left: 100%;
    }
    
    .btn.login {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #ffffff;
      border-color: rgba(255,255,255,0.2);
      box-shadow: 0 8px 25px rgba(102,126,234,0.3);
    }
    
    .btn.signup {
      background: rgba(255,255,255,0.1);
      color: #ffffff;
      border-color: rgba(255,255,255,0.3);
      box-shadow: 0 8px 25px rgba(255,255,255,0.1);
    }
    
    .btn.profile-btn {
      background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
      color: #ffffff;
      border-color: rgba(255,255,255,0.2);
      box-shadow: 0 8px 25px rgba(76,175,80,0.3);
      font-size: 0.8rem;
      padding: 0.5rem 0.9rem;
    }
    
    .btn.admin-btn {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
      color: #ffffff;
      border-color: rgba(255,255,255,0.2);
      box-shadow: 0 8px 25px rgba(255,107,107,0.3);
      font-size: 0.75rem;
      padding: 0.4rem 0.8rem;
    }
    
    .btn.logout-btn {
      background: linear-gradient(135deg, #95a5a6 0%, #7f8c8d 100%);
      color: #ffffff;
      border-color: rgba(255,255,255,0.2);
      box-shadow: 0 8px 25px rgba(149,165,166,0.3);
      font-size: 0.85rem;
      padding: 0.6rem 1.2rem;
    }
    
    .btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .btn.login:hover {
      box-shadow: 0 15px 35px rgba(102,126,234,0.4);
    }
    
    .btn.signup:hover {
      background: rgba(255,255,255,0.2);
      box-shadow: 0 15px 35px rgba(255,255,255,0.2);
    }
    
    .btn.profile-btn:hover {
      box-shadow: 0 15px 35px rgba(76,175,80,0.4);
    }
    
    .btn.admin-btn:hover {
      box-shadow: 0 15px 35px rgba(255,107,107,0.4);
    }
    
    .btn.logout-btn:hover {
      box-shadow: 0 15px 35px rgba(149,165,166,0.4);
    }
    
    /* Mobile Navigation Toggle */
    .nav-toggle {
      display: none;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      width: 45px;
      height: 45px;
      cursor: pointer;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
      backdrop-filter: blur(20px);
    }
    
    .nav-toggle:hover {
      background: rgba(255,255,255,0.2);
      transform: scale(1.05);
    }
    
    .nav-toggle span {
      display: block;
      width: 20px;
      height: 2px;
      background: #ffffff;
      margin: 4px 0;
      transition: all 0.3s ease;
      border-radius: 2px;
    }
    
    /* Mobile Responsive Design */
    @media (max-width: 1200px) {
      .navbar-container {
        padding: 0 1.5rem;
      }
      
      .nav-links {
        gap: 1.5rem;
      }
      
      .nav-links a {
        font-size: 0.9rem;
      }
    }
    
    @media (max-width: 992px) {
      .navbar-container {
        padding: 0 1rem;
      }
      
      .nav-links {
        gap: 1.2rem;
      }
      
      .nav-links a {
        font-size: 0.85rem;
      }
      
      .btn {
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
      }
    }
    
    @media (max-width: 768px) {
      .nav-toggle {
        display: flex;
      }
      
      .nav-links {
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(30,30,60,0.95) 100%);
        backdrop-filter: blur(30px);
        flex-direction: column;
        gap: 0;
        padding: 2rem 0;
        transform: translateY(-100vh);
        transition: transform 0.3s ease;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
      }
      
      .nav-links.active {
        transform: translateY(0);
      }
      
      .nav-links a {
        width: 100%;
        text-align: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        font-size: 1rem;
      }
      
      .nav-links a::after {
        display: none;
      }
      
      .nav-links a:hover {
        background: rgba(102,126,234,0.1);
        color: #667eea;
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
      
      .navbar h1 a {
        font-size: 1.2rem;
      }
      
      #navbar-logo {
        width: 40px;
        height: 40px;
      }
    }
    
    /* Navbar Scroll Effect */
    .navbar.scrolled {
      background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(20,20,40,0.95) 100%);
      box-shadow: 0 10px 50px rgba(0,0,0,0.3);
      padding: 0.5rem 0;
    }
    
    /* Add body padding for fixed navbar */
    body {
      padding-top: 80px;
    }
    
    /* Mobile Navigation Toggle */
    .nav-toggle {
      display: none !important;
      width: 40px;
      height: 40px;
      background: rgba(255,255,255,0.15);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 10px;
      cursor: pointer;
      transition: all 0.3s ease;
      padding: 8px;
    }
    
    .nav-toggle:hover {
      background: rgba(255,255,255,0.25);
    }
    
    .nav-toggle svg {
      width: 100%;
      height: 100%;
      color: white;
    }
    
    @media (max-width: 768px) {
      .nav-toggle {
        display: flex !important;
      }
      
      .nav-links {
        display: none;
      }
    }
    
    /* Modern Typography System */
    * {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
      text-rendering: optimizeLegibility;
    }
    
    /* Modern Design Utilities */
    .modern-text-white {
      color: rgba(255,255,255,0.95);
      text-shadow: 0 1px 8px rgba(0,0,0,0.15);
    }
    
    .modern-text-dark {
      color: #2c3e50;
      font-weight: 500;
    }
    
    .modern-gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    /* Navigation Hover Effects */
    .nav-links > a::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #667eea, #764ba2);
      transition: width 0.3s ease;
    }
    
    .nav-links > a:hover::after {
      width: 100%;
    }
    
    /* Floating Action Buttons */
    .hero-actions {
      margin-top: 2rem;
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      justify-content: center;
    }
    
    .hero-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #ffffff;
      padding: 16px 32px;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      border: 2px solid rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
      position: relative;
      overflow: hidden;
      text-shadow: 0 1px 4px rgba(0,0,0,0.2);
      letter-spacing: 0.5px;
      font-size: 16px;
    }
    
    .hero-btn::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s ease;
    }
    
    .hero-btn:hover::before {
      left: 100%;
    }
    
    .hero-btn:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 15px 35px rgba(0,0,0,0.3);
      border-color: rgba(255,255,255,0.6);
    }
    
    /* Enhanced Hero Section */
    .main {
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%) !important;
      border: 1px solid rgba(255,255,255,0.3);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      margin: 2rem;
      padding: 3rem 2rem;
      box-shadow: 0 20px 40px rgba(0,0,0,0.15);
      animation: heroFloat 6s ease-in-out infinite;
    }
    
    @keyframes heroFloat {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-8px); }
    }
    
    .main h1 {
      color: white !important;
      font-size: 3.5rem;
      font-weight: 700;
      text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
      margin-bottom: 1.5rem;
      animation: titleGlow 3s ease-in-out infinite alternate;
    }
    
    @keyframes titleGlow {
      0% { text-shadow: 2px 2px 8px rgba(0,0,0,0.4); }
      100% { text-shadow: 2px 2px 12px rgba(0,0,0,0.6), 0 0 20px rgba(255,255,255,0.3); }
    }
    
    .main p {
      color: rgba(255,255,255,0.95) !important;
      font-size: 1.2rem;
      line-height: 1.8;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
      font-weight: 400;
    }
    
    /* Hero Section Styles */
    .hero-section {
      background: transparent;
      padding: 4rem 2rem;
      min-height: 80vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .hero-content-centered {
      text-align: center;
      max-width: 1000px;
      width: 100%;
    }
    
    .hero-title {
      font-size: 3.5rem;
      font-weight: 800;
      color: #ffffff;
      margin-bottom: 1.5rem;
      text-shadow: 0 2px 20px rgba(0,0,0,0.5);
      line-height: 1.2;
      letter-spacing: -0.02em;
      position: relative;
    }
    
    .highlight {
      color: #ffffff !important;
      font-weight: 900;
      text-shadow: 0 2px 10px rgba(0,0,0,0.5);
      position: relative;
      display: inline-block;
      z-index: 10;
    }
    
    .hero-description {
      font-size: 1.2rem;
      color: rgba(255,255,255,0.92);
      line-height: 1.7;
      margin-bottom: 2rem;
      text-shadow: 0 1px 10px rgba(0,0,0,0.2);
      font-weight: 400;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }
    
    /* Hero Images Row */
    .hero-images-row {
      display: flex;
      justify-content: center;
      gap: 2rem;
      margin-top: 3rem;
      flex-wrap: wrap;
    }
    
    .hero-image-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.5rem;
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.25);
      border-radius: 25px;
      padding: 2rem 1.5rem;
      transition: all 0.3s ease;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      min-width: 200px;
    }
    
    .hero-image-item:hover {
      transform: translateY(-8px);
      background: rgba(255,255,255,0.2);
      box-shadow: 0 15px 40px rgba(0,0,0,0.25);
      border-color: rgba(255,255,255,0.4);
    }
    
    .hero-img {
      width: 140px;
      height: 140px;
      object-fit: cover;
      border-radius: 20px;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      border: 2px solid rgba(255,255,255,0.3);
    }
    
    .hero-image-item:hover .hero-img {
      transform: scale(1.05);
      box-shadow: 0 12px 30px rgba(0,0,0,0.3);
      border-color: rgba(255,255,255,0.5);
    }
    
    .image-label {
      color: #ffffff;
      font-weight: 700;
      font-size: 16px;
      text-shadow: 0 2px 10px rgba(0,0,0,0.6), 0 1px 3px rgba(0,0,0,0.8);
      letter-spacing: 0.8px;
      text-transform: uppercase;
      background: rgba(255,255,255,0.1);
      padding: 8px 16px;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.2);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }
    
    .hero-image-item:hover .image-label {
      background: rgba(255,255,255,0.2);
      color: #ffffff;
      text-shadow: 0 2px 15px rgba(0,0,0,0.8);
      transform: translateY(-2px);
    }
    
    /* Enhanced Cards with 3D Effect */
    .card {
      background: rgba(255,255,255,0.95) !important;
      backdrop-filter: blur(20px) !important;
      border: 1px solid rgba(255,255,255,0.3) !important;
      border-radius: 20px !important;
      transition: all 0.4s ease !important;
      transform-style: preserve-3d;
      position: relative;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
      color: #2c3e50 !important;
    }
    
    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.8) 100%);
      border-radius: 20px;
      z-index: -1;
    }
    
    .card:hover {
      transform: translateY(-10px) !important;
      box-shadow: 0 25px 50px rgba(0,0,0,0.25) !important;
      border-color: rgba(102, 126, 234, 0.6) !important;
      background: rgba(255,255,255,0.98) !important;
    }
    
    .card h3, .card h4, .card h5 {
      color: #2c3e50 !important;
      text-shadow: none !important;
    }
    
    .card p, .card span {
      color: #34495e !important;
      text-shadow: none !important;
    }
    
    /* Animated Statistics Counter */
    .stats-container {
      background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
      border-radius: 20px;
      padding: 2rem;
      margin: 3rem 2rem;
      border: 1px solid rgba(255,255,255,0.3);
      backdrop-filter: blur(20px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }
    
    .stat-item {
      text-align: center;
      padding: 1.5rem;
      border-radius: 15px;
      background: rgba(255,255,255,0.9);
      border: 1px solid rgba(255,255,255,0.3);
      transition: all 0.3s ease;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .stat-item:hover {
      transform: translateY(-5px);
      background: rgba(255,255,255,0.95);
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: none;
    }
    
    .stat-label {
      font-size: 1rem;
      margin-top: 0.5rem;
      color: #2c3e50;
      font-weight: 500;
      text-shadow: none;
    }
    
    /* Enhanced Footer */
    .footer {
      background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(30,30,60,0.9) 100%) !important;
      backdrop-filter: blur(20px) !important;
      -webkit-backdrop-filter: blur(20px) !important;
      border-top: 1px solid rgba(255,255,255,0.1);
      padding: 3rem 0 1rem 0;
      margin-top: 4rem;
      position: relative;
      overflow: hidden;
    }
    
    .footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="80" cy="80" r="1" fill="rgba(255,255,255,0.05)"/><circle cx="40" cy="60" r="0.5" fill="rgba(255,255,255,0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      pointer-events: none;
    }
    
    .footer-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-bottom: 2rem;
      position: relative;
      z-index: 1;
    }
    
    .footer-section {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 15px;
      padding: 1.5rem;
      transition: all 0.3s ease;
    }
    
    .footer-section:hover {
      background: rgba(255,255,255,0.08);
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .footer-section h3 {
      color: #ffffff;
      font-size: 1.4rem;
      font-weight: 700;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -0.01em;
    }
    
    .footer-section h4 {
      color: #ffffff;
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 1rem;
      text-shadow: 0 1px 8px rgba(0,0,0,0.2);
      letter-spacing: 0.2px;
    }
    
    .footer-section p {
      color: rgba(255,255,255,0.88);
      line-height: 1.6;
      font-size: 15px;
      font-weight: 400;
      text-shadow: 0 1px 6px rgba(0,0,0,0.15);
    }
    
    .footer-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .footer-section ul li {
      margin-bottom: 0.5rem;
    }
    
    .footer-section ul li a {
      color: rgba(255,255,255,0.85);
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      align-items: center;
      padding: 0.4rem 0;
      border-radius: 6px;
      position: relative;
      font-weight: 400;
      font-size: 14px;
    }
    
    .footer-section ul li a::before {
      content: '→';
      margin-right: 0.5rem;
      opacity: 0;
      transform: translateX(-10px);
      transition: all 0.3s ease;
    }
    
    .footer-section ul li a:hover {
      color: #ffffff;
      transform: translateX(4px);
      text-shadow: 0 0 8px rgba(255,255,255,0.3);
    }
    
    .footer-section ul li a:hover::before {
      opacity: 1;
      transform: translateX(0);
    }
    
    .social-links {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }
    
    .social-links a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 45px;
      height: 45px;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 50%;
      color: white;
      font-size: 1.2rem;
      transition: all 0.3s ease;
      text-decoration: none;
    }
    
    .social-links a:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      transform: translateY(-3px) scale(1.1);
      box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.1);
      padding-top: 1.5rem;
      text-align: center;
      background: rgba(0,0,0,0.3);
      margin: 0 -2rem -1rem -2rem;
      padding-left: 2rem;
      padding-right: 2rem;
      position: relative;
      z-index: 1;
    }
    
    .footer-bottom p {
      color: rgba(255,255,255,0.9);
      margin: 0;
      font-size: 0.9rem;
      text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
      font-weight: 500;
      filter: contrast(1.1) brightness(1.05);
    }
    
    /* Footer Responsive Design */
    @media (max-width: 992px) {
      .footer {
        padding: 2rem 0 1rem 0;
      }
      
      .footer-content {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
      }
      
      .footer-section {
        padding: 1.2rem;
      }
    }
    
    @media (max-width: 768px) {
      .footer {
        padding: 1.5rem 0 1rem 0;
        margin-top: 2rem;
      }
      
      .footer-content {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .footer-section {
        padding: 1rem;
      }
      
      .footer-section h3 {
        font-size: 1.2rem;
      }
      
      .footer-section h4 {
        font-size: 1rem;
      }
      
      .social-links a {
        width: 40px;
        height: 40px;
        font-size: 1rem;
      }
      
      .footer-bottom {
        margin: 0 -1rem -1rem -1rem;
        padding-left: 1rem;
        padding-right: 1rem;
      }
    }
    
    /* Scroll Indicator */
    .scroll-indicator {
      position: fixed;
      top: 0;
      left: 0;
      width: 0;
      height: 4px;
      background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
      z-index: 9999;
      transition: width 0.3s ease;
    }
    
    /* Hero Section Styles */
    .hero-section {
      background: transparent;
      padding: 4rem 2rem;
      min-height: 80vh;
      display: flex;
      align-items: center;
    }
    
    .hero-content {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 3rem;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .hero-description {
      font-size: 1.2rem;
      color: rgba(255,255,255,0.9);
      line-height: 1.8;
      margin-bottom: 2rem;
    }
    
    /* Hero Images */
    .hero-images {
      display: flex;
      gap: 1rem;
      justify-content: center;
      align-items: center;
    }
    
    .hero-img {
      max-width: 200px;
      border-radius: 15px;
      transition: all 0.3s ease;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .hero-img:hover {
      transform: translateY(-10px) scale(1.05);
    }
    
    /* Features Section */
    .features-section {
      padding: 4rem 2rem;
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(15px);
      margin: 2rem;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .section-title {
      text-align: center;
      font-size: 2.5rem;
      color: #ffffff;
      margin-bottom: 3rem;
      text-shadow: 0 2px 15px rgba(0,0,0,0.2);
      font-weight: 700;
      letter-spacing: -0.02em;
      line-height: 1.2;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
      margin-top: 2rem;
    }
    
    .feature-card {
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 20px;
      padding: 2rem;
      text-align: center;
      transition: all 0.4s ease;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .feature-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
      border-color: rgba(102, 126, 234, 0.6);
      background: rgba(255,255,255,0.95);
    }
    
    .feature-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: none;
    }
    
    .feature-card h3 {
      font-size: 1.4rem;
      margin-bottom: 1rem;
      color: #2c3e50;
      font-weight: 700;
      letter-spacing: -0.01em;
      line-height: 1.3;
    }
    
    .feature-card p {
      color: #5a6c7d;
      line-height: 1.6;
      font-weight: 400;
      font-size: 15px;
    }
    
    /* Courses Section */
    .courses-section {
      padding: 4rem 2rem;
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(15px);
      margin: 2rem;
      border-radius: 20px;
      border: 1px solid rgba(255,255,255,0.2);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }
    
    /* Small Course Cards */
    .course-card {
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 15px;
      overflow: hidden;
      transition: all 0.4s ease;
      max-width: 320px;
      margin: 0 auto;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .course-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.25);
      border-color: rgba(102, 126, 234, 0.6);
      background: rgba(255,255,255,0.95);
    }
    
    .course-image {
      width: 100%;
      height: 160px;
      object-fit: cover;
      transition: all 0.3s ease;
    }
    
    .course-card:hover .course-image {
      transform: scale(1.05);
    }
    
    .course-content {
      padding: 1.5rem;
    }
    
    .course-content h3 {
      font-size: 1.2rem;
      margin-bottom: 0.8rem;
      color: #2c3e50;
      font-weight: 700;
      letter-spacing: -0.01em;
      line-height: 1.4;
    }
    
    .course-content p {
      color: #5a6c7d;
      font-size: 14px;
      line-height: 1.5;
      margin-bottom: 1rem;
      font-weight: 400;
    }
    
    .course-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }
    
    .price {
      color: #667eea;
      font-weight: 600;
      font-size: 1.1rem;
      text-shadow: none;
    }
    
    .duration {
      color: #7f8c8d;
      text-shadow: none;
    }
    
    .course-btn {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 0.6rem 1.2rem;
      border-radius: 25px;
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-block;
      text-align: center;
    }
    
    .course-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    
    /* Container Styles */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    
    /* Tablet Responsive Design */
    @media (max-width: 992px) {
      .hero-title {
        font-size: 3rem;
      }
      
      .hero-images-row {
        gap: 1.5rem;
      }
      
      .hero-image-item {
        padding: 1.5rem 1rem;
        min-width: 160px;
        gap: 1rem;
      }
      
      .hero-img {
        width: 100px;
        height: 100px;
      }
      
      .image-label {
        font-size: 14px;
        padding: 6px 12px;
        letter-spacing: 0.5px;
      }
    }
    
    /* Mobile Responsive Adjustments */
    @media (max-width: 768px) {
      .hero-title {
        font-size: 2.5rem;
      }
      
      .hero-section {
        padding: 2rem 1rem;
        min-height: auto;
      }
      
      .hero-content-centered {
        max-width: 100%;
      }
      
      .hero-description {
        font-size: 1rem;
        max-width: 100%;
      }
      
      .hero-images-row {
        gap: 1rem;
        margin-top: 2rem;
      }
      
      .hero-image-item {
        padding: 1rem;
        min-width: 140px;
        gap: 0.8rem;
      }
      
      .hero-img {
        width: 80px;
        height: 80px;
      }
      
      .image-label {
        font-size: 12px;
        padding: 4px 8px;
        letter-spacing: 0.3px;
      }
      
      .hero-btn {
        padding: 12px 24px;
        font-size: 0.9rem;
      }
      
      .stats-container {
        margin: 2rem 1rem;
      }
      
      .hero-content {
        grid-template-columns: 1fr;
        gap: 2rem;
      }
      
      .hero-title {
        font-size: 2.5rem;
      }
      
      .features-section,
      .courses-section {
        margin: 1rem;
        padding: 2rem 1rem;
      }
      
      .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      
      .courses-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
      }
      
      .course-card {
        max-width: 100%;
      }
      
      .hero-images {
        flex-direction: column;
        gap: 0.5rem;
      }
      
      .hero-img {
        max-width: 150px;
      }
    }
  </style>
</head>

<body id="main-body" class="overflow-x-hidden">

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

  <!-- Scroll Progress Indicator -->
  <div class="scroll-indicator" id="scrollIndicator"></div>

  <?php if ($message): ?>
    <div id="sessionMessage" style="background: #d4edda; color: #155724; padding: 10px; text-align: center; border-bottom: 1px solid #c3e6cb;">
      <?php echo htmlspecialchars($message); ?>
    </div>
    <script>
      // Auto-hide message after 5 seconds
      setTimeout(function() {
        const msg = document.getElementById('sessionMessage');
        if (msg) msg.style.display = 'none';
      }, 5000);
    </script>
  <?php endif; ?>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="container">
      <div class="hero-content-centered">
        <h1 class="hero-title">Welcome to the Future of <span class="highlight">Tech Learning</span></h1>
        <p class="hero-description">
          Join thousands of learners on their journey to master cutting-edge technologies.
          From web development to data science, we provide hands-on courses designed by industry experts.
        </p>
        <div class="hero-actions">
          <?php if (!$isLoggedIn): ?>
            <a href="courses.php" class="hero-btn">
              <i class="fas fa-rocket"></i> Explore Courses
            </a>
            <a href="signup.php" class="hero-btn">
              <i class="fas fa-user-plus"></i> Get Started Free
            </a>
          <?php else: ?>
            <a href="courses.php" class="hero-btn">
              <i class="fas fa-play"></i> Continue Learning
            </a>
            <a href="projects.php" class="hero-btn">
              <i class="fas fa-code"></i> My Projects
            </a>
          <?php endif; ?>
          <a href="about.php" class="hero-btn">
            <i class="fas fa-info-circle"></i> Learn More
          </a>
        </div>
        
        <!-- Hero Images in a single row -->
        <!-- <div class="hero-images-row">
          <div class="hero-image-item">
            <img src="./assets/images/hero-img-left.png" alt="Learning" class="hero-img" />
            <span class="image-label">Learning</span>
          </div>
          <div class="hero-image-item">
            <img src="./assets/images/hero-img-center.png" alt="Technology" class="hero-img" />
            <span class="image-label">Technology</span>
          </div>
          <div class="hero-image-item">
            <img src="./assets/images/hero-img-right.png" alt="Innovation" class="hero-img" />
            <span class="image-label">Innovation</span>
          </div>
        </div> -->
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section">
    <div class="container">
      <h2 class="section-title">Why Choose Creators-Space?</h2>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
          <h3>Expert Instructors</h3>
          <p>Learn from industry professionals with years of real-world experience</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-laptop-code"></i>
          </div>
          <h3>Hands-on Projects</h3>
          <p>Build real projects that you can showcase in your portfolio</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-certificate"></i>
          </div>
          <h3>Certificates</h3>
          <p>Earn industry-recognized certificates upon course completion</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3>Community Support</h3>
          <p>Join a thriving community of learners and get help when you need it</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Statistics Section -->
  <section class="stats-container">
    <div class="container">
      <h2 style="text-align: center; color: white; font-size: 2.5rem; margin-bottom: 1rem;">
        Our Impact in Numbers
      </h2>
      <p style="text-align: center; color: rgba(255,255,255,0.8); font-size: 1.1rem; margin-bottom: 2rem;">
        Join thousands of successful learners worldwide
      </p>
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-number" data-target="10000">0</div>
          <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-target="50">0</div>
          <div class="stat-label">Expert Instructors</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-target="100">0</div>
          <div class="stat-label">Courses Available</div>
        </div>
        <div class="stat-item">
          <div class="stat-number" data-target="95">0</div>
          <div class="stat-label">Success Rate %</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Popular Courses Section -->
  <section class="courses-section">
    <div class="container">
      <h2 class="section-title">Popular Courses</h2>
      <div class="courses-grid" id="popularCourses">
        <!-- Courses will be loaded dynamically -->
        <div class="course-card">
          <img src="./assets/images/full-stack-web-developer.png" alt="Full Stack Development" class="course-image">
          <div class="course-content">
            <h3>Full Stack Web Development</h3>
            <p>Master frontend and backend development with modern technologies</p>
            <div class="course-meta">
              <span class="price">$99.99</span>
              <span class="duration">12 weeks</span>
            </div>
            <a href="courses.php" class="btn course-btn">Learn More</a>
          </div>
        </div>
        <div class="course-card">
          <img src="./assets/images/webdev.png" alt="Web Development" class="course-image">
          <div class="course-content">
            <h3>Web Development Basics</h3>
            <p>Start your web development journey with HTML, CSS, and JavaScript</p>
            <div class="course-meta">
              <span class="price">$49.99</span>
              <span class="duration">6 weeks</span>
            </div>
            <a href="courses.php" class="btn course-btn">Learn More</a>
          </div>
        </div>
        <div class="course-card">
          <img src="./assets/images/blogpage/uiux.jpeg" alt="UI/UX Design" class="course-image">
          <div class="course-content">
            <h3>UI/UX Design</h3>
            <p>Learn to create beautiful and user-friendly interfaces</p>
            <div class="course-meta">
              <span class="price">$79.99</span>
              <span class="duration">8 weeks</span>
            </div>
            <a href="courses.php" class="btn course-btn">Learn More</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Newsletter Section -->
  <section class="newsletter-section">
    <div class="container">
      <div class="newsletter-content">
        <h2>Stay Updated</h2>
        <p>Subscribe to our newsletter and get the latest updates on new courses and tech trends</p>
        <form id="newsletterForm" class="newsletter-form">
          <input type="email" placeholder="Enter your email" required>
          <button type="submit" class="btn">Subscribe</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3>Creators-Space</h3>
          <p>Empowering the next generation of tech innovators through quality education and hands-on learning.</p>
        </div>
        <div class="footer-section">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="about.php">About Us</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="services.php">Services</a></li>
            <li><a href="blog.php">Blog</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Support</h4>
          <ul>
            <li><a href="#">Help Center</a></li>
            <li><a href="#">Contact Us</a></li>
            <li><a href="tandc.php">Terms & Conditions</a></li>
            <li><a href="#">Privacy Policy</a></li>
          </ul>
        </div>
        <div class="footer-section">
          <h4>Connect</h4>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2025 Creators-Space. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="./src/js/navbar.js"></script>
  <script src="./src/js/hero-section.js"></script>
  <script src="./src/js/newsletter.js"></script>
  <script src="./src/js/scrollToTop.js"></script>
  <script src="./src/js/utils.js"></script>
  <script src="./src/js/mobile-responsive.js"></script>

  <!-- Enhanced JavaScript for Stylish Effects -->
  <script>
    // Scroll Progress Indicator
    window.addEventListener('scroll', function() {
      const scrollIndicator = document.getElementById('scrollIndicator');
      const scrollTop = window.pageYOffset;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      const scrollPercent = (scrollTop / docHeight) * 100;
      scrollIndicator.style.width = scrollPercent + '%';
    });

    // Animated Counter for Statistics
    function animateCounter(element, start, end, duration) {
      let startTimestamp = null;
      const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        element.innerText = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
          window.requestAnimationFrame(step);
        }
      };
      window.requestAnimationFrame(step);
    }

    // Intersection Observer for Statistics Animation
    const statsObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const statNumbers = entry.target.querySelectorAll('.stat-number');
          statNumbers.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            animateCounter(stat, 0, target, 2000);
          });
          statsObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    // Start observing when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
      const statsContainer = document.querySelector('.stats-container');
      if (statsContainer) {
        statsObserver.observe(statsContainer);
      }

      // Add parallax effect to hero section
      window.addEventListener('scroll', function() {
        const scrolled = window.pageYOffset;
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
          heroSection.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
      });

      // Add hover effect to cards
      const cards = document.querySelectorAll('.feature-card, .course-card');
      cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        card.addEventListener('mouseleave', function() {
          this.style.transform = 'translateY(0) scale(1)';
        });
      });

      // Add smooth scrolling to all anchor links
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
          }
        });
      });
    });

    // Dynamic background particles
    function createParticle() {
      const particle = document.createElement('div');
      particle.style.position = 'fixed';
      particle.style.width = Math.random() * 4 + 2 + 'px';
      particle.style.height = particle.style.width;
      particle.style.background = 'rgba(255, 255, 255, 0.1)';
      particle.style.borderRadius = '50%';
      particle.style.left = Math.random() * window.innerWidth + 'px';
      particle.style.top = window.innerHeight + 'px';
      particle.style.pointerEvents = 'none';
      particle.style.zIndex = '-1';
      
      document.body.appendChild(particle);
      
      const duration = Math.random() * 3000 + 2000;
      particle.animate([
        { transform: 'translateY(0px)', opacity: 0 },
        { transform: 'translateY(-' + (window.innerHeight + 100) + 'px)', opacity: 1 }
      ], {
        duration: duration,
        easing: 'linear'
      }).onfinish = () => particle.remove();
    }

    // Create particles periodically
    setInterval(createParticle, 300);
  </script>

  <!-- User authentication state for JavaScript -->
  <script>
    window.userAuth = {
      isLoggedIn: <?php echo $isLoggedIn ? 'true' : 'false'; ?>,
      user: <?php echo $isLoggedIn ? json_encode($user) : 'null'; ?>
    };
  </script>

</body>
</html>
    };
  </script>

</body>
</html>
