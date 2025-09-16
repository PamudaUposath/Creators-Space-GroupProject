# 🌐 Creators-Space E-Learning Management System

<img src="./frontend/assets/images/logo.png" alt="Creators-Space Logo" height="100px" />

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://www.mysql.com/)
[![Build Status](https://img.shields.io/badge/Build-Passing-brightgreen)](https://github.com/PamudaUposath/Creators-Space-GroupProject)

**Creators-Space** is a comprehensive e-learning management system built with PHP and MySQL, designed to empower the next generation of tech innovators through quality education and hands-on learning experiences.

## 🚀 Latest Updates - Full Database Integration & Enhanced Features

This project has been completely upgraded to a full-featured PHP + MySQL web application with real-time database integration:

- ✅ **Database-Driven Courses**: Real courses fetched from MySQL database with instructor information
- ✅ **Smart Course Categorization**: Automatic course categorization based on content analysis
- ✅ **Image Management**: Comprehensive image management system for courses with fallback support
- ✅ **Enhanced Search & Filtering**: Advanced search functionality with multiple filter options
- ✅ **User Authentication**: Complete session-based authentication with secure password handling
- ✅ **Admin Dashboard**: Comprehensive admin panel with user and course management
- ✅ **Mobile Responsive**: Fully responsive design optimized for all devices
- ✅ **Certificate System**: Digital certificate generation and verification
- ✅ **Progress Tracking**: Real-time learning progress and course completion tracking

---

## 📁 Project Structure

```
Creators-Space-GroupProject/
├── frontend/                    # Client-facing application
│   ├── index.php               # Homepage with hero section
│   ├── about.php               # About page with dark mode
│   ├── login.php               # User authentication
│   ├── signup.php              # User registration
│   ├── courses.php             # Database-driven course catalog
│   ├── mycourses.php           # User enrolled courses
│   ├── blog.php                # Educational blog
│   ├── projects.php            # Project showcase
│   ├── internship.php          # Internship opportunities
│   ├── services.php            # Platform services
│   ├── campus-ambassador.php   # Ambassador program
│   ├── certificate.php         # Certificate verification
│   ├── includes/               # Shared components
│   │   ├── header.php          # Navigation header
│   │   └── footer.php          # Site footer
│   ├── assets/                 # Static assets
│   │   ├── images/             # Course and UI images
│   │   │   ├── blogpage/       # Blog post images
│   │   │   └── aboutpage/      # About page assets
│   │   ├── animations/         # Loading animations
│   │   └── certificate/        # Certificate templates
│   └── src/                    # Source files
│       ├── css/                # Stylesheets
│       │   ├── style.css       # Main styles
│       │   ├── courses.css     # Course-specific styles
│       │   ├── about.css       # About page styles
│       │   └── *.css           # Component-specific styles
│       ├── js/                 # JavaScript modules
│       │   ├── courses.js      # Advanced search & filtering
│       │   ├── about.js        # Dark mode & animations
│       │   ├── navbar.js       # Navigation functionality
│       │   └── *.js            # Component scripts
│       └── data/               # Static data files
│           ├── projects.json   # Project data
│           ├── services.json   # Services data
│           └── internship.json # Internship data
├── backend/                     # Server-side application
│   ├── config/                 # Configuration
│   │   └── db_connect.php      # Database connection
│   ├── auth/                   # Authentication endpoints
│   │   ├── signup_process.php  # Registration handler
│   │   ├── login_process.php   # Login handler
│   │   ├── logout.php          # Logout handler
│   │   ├── forgot_password.php # Password reset
│   │   └── reset_password.php  # Password reset handler
│   ├── admin/                  # Admin panel
│   │   ├── dashboard.php       # Admin dashboard
│   │   └── users.php           # User management
│   ├── public/                 # Public backend entry
│   │   ├── admin_login.php     # Admin authentication
│   │   └── index.php           # Backend API entry
│   ├── sql/                    # Database files
│   │   ├── db_schema.sql       # Database structure
│   │   └── seed_admin.sql      # Admin user seed
│   ├── lib/                    # Helper libraries
│   │   └── helpers.php         # Utility functions
│   ├── add_sample_data.php     # Sample course data
│   ├── update_course_images.php # Image management
│   ├── dashboard_stats.php     # Analytics data
│   └── ER_ASCII.txt           # Database ER diagram
├── docs/                       # Documentation
│   ├── INSTALL.md             # Detailed installation guide
│   ├── STATUS.md              # Development status
│   ├── CLEANUP.md             # Code cleanup notes
│   ├── MOBILE_RESPONSIVE.md   # Mobile design guide
│   ├── SYSTEM_TEST.md         # Testing documentation
│   └── *.png                  # Screenshots
├── index.php                   # Root redirect
├── setup.bat                   # Windows setup script
├── setup.sh                   # Linux/macOS setup script
└── README.md                  # This file
```

---

## ✨ Features

### 🔐 User Management
- **Secure Authentication**: Registration, login, logout with session management
- **Password Security**: Hashed passwords using PHP's `password_hash()`
- **Password Reset**: Secure token-based password reset via email
- **Role-based Access**: User, Instructor, and Admin roles with permissions
- **Profile Management**: Comprehensive user profile with skills tracking
- **Session Security**: Secure session handling and automatic timeout

### 📚 Course Management
- **Database-Driven Catalog**: Real-time course data from MySQL database
- **Smart Categorization**: Automatic course categorization (Web Dev, Design, Programming, Data Science, Mobile, DevOps)
- **Advanced Search**: Intelligent search with suggestions and keyword matching
- **Multi-Filter System**: Filter by category, level, price, and instructor
- **Image Management**: Comprehensive image system with fallback support
- **Enrollment System**: Seamless course enrollment with progress tracking
- **Instructor Profiles**: Detailed instructor information and credentials

### 🎓 Learning Features
- **Interactive Lessons**: Structured course content with video support
- **Progress Tracking**: Real-time learning progress and completion analytics
- **Certificate Generation**: Digital certificates with verification codes
- **Bookmarking System**: Save and organize favorite courses
- **Course Reviews**: Rating and review system for courses
- **Learning Paths**: Guided learning trajectories for different skills

### 💻 Frontend Features
- **Responsive Design**: Mobile-first design optimized for all devices
- **Dark Mode Support**: Comprehensive dark theme with smooth transitions
- **Modern UI/UX**: Glassmorphism design with CSS animations
- **Performance Optimized**: Lazy loading and optimized asset delivery
- **Accessibility**: WCAG compliant with keyboard navigation support
- **PWA Ready**: Progressive Web App capabilities

### 💼 Career Services
- **Internship Portal**: Browse and apply for internship opportunities with JSON data
- **Campus Ambassador Program**: Student ambassador application system
- **Project Showcase**: Portfolio system for displaying user projects
- **Career Guidance**: Professional development resources and mentorship
- **Networking Hub**: Connect with instructors, peers, and industry professionals
- **Blog Platform**: Educational articles and tech insights

### 🔧 Admin Panel
- **User Management**: Manage users, roles, and permissions
- **Course Administration**: Create, edit, and manage courses
- **Analytics Dashboard**: User engagement and platform statistics
- **Content Management**: Blog posts, internships, and services
- **System Monitoring**: Security logs and activity tracking

---

## 🛠 Tech Stack

### Frontend
- **Languages**: HTML5, CSS3, JavaScript (ES6+), PHP
- **Styling**: Custom CSS with responsive design
- **Icons**: Font Awesome
- **Architecture**: Progressive enhancement, mobile-first design

### Backend
- **Language**: PHP 8.0+
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Session Management**: PHP Sessions
- **Security**: PDO prepared statements, CSRF protection, rate limiting
- **Architecture**: RESTful API design, MVC-inspired structure

### Development
- **Version Control**: Git
- **Server**: Apache/Nginx or PHP built-in server
- **Development Tools**: VS Code, phpMyAdmin, MySQL Workbench

---

## 🚀 Quick Start

### Prerequisites
- **PHP 8.0 or higher** with extensions: PDO, PDO_MySQL, session, json
- **MySQL 8.0 or higher** / MariaDB 10.4+
- **Web server** (Apache/Nginx) or PHP built-in server
- **Git** for cloning the repository
- **XAMPP/WAMP/MAMP** (recommended for local development)

### 1. Clone the Repository
```bash
git clone https://github.com/PamudaUposath/Creators-Space-GroupProject.git
cd Creators-Space-GroupProject
```

### 2. Database Setup
```bash
# Create MySQL database
mysql -u root -p
CREATE DATABASE creators_space DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# Import schema and seed data
mysql -u root -p creators_space < backend/sql/db_schema.sql
mysql -u root -p creators_space < backend/sql/seed_admin.sql
```

### 3. Configure Database Connection
Edit `backend/config/db_connect.php`:
```php
<?php
// Database configuration
$DB_HOST = '127.0.0.1';
$DB_NAME = 'creators_space';
$DB_USER = 'root';          // Your MySQL username
$DB_PASS = '';              // Your MySQL password (empty for XAMPP default)
$DB_CHARSET = 'utf8mb4';
?>
```

### 4. Add Sample Data (Optional)
```bash
# Add sample courses and users
cd backend
php add_sample_data.php

# Update course images
php update_course_images.php
```

### 5. Start Development Server

#### Option A: PHP Built-in Server (Recommended)
```bash
# From project root
php -S localhost:8000

# Or for Windows with XAMPP
C:\xampp\php\php.exe -S localhost:8000

# Access at: http://localhost:8000/frontend/
```

#### Option B: XAMPP/WAMP Setup
1. Move project to `htdocs/` directory
2. Start Apache and MySQL from XAMPP control panel
3. Access at: `http://localhost/Creators-Space-GroupProject/frontend/`

### 6. Access the Application
- **Frontend Homepage**: `http://localhost:8000/frontend/`
- **Course Catalog**: `http://localhost:8000/frontend/courses.php`
- **Admin Panel**: `http://localhost:8000/backend/public/admin_login.php`
- **User Login**: `http://localhost:8000/frontend/login.php`

### 7. Default Credentials
**Admin Account:**
- **Email**: `admin@creatorsspace.local`
- **Password**: `admin123` 

**Test User Account:**
- **Email**: `user@example.com`
- **Password**: `password123`

> ⚠️ **Security Note**: Change default passwords immediately in production!

---

## 🛠 Development & Troubleshooting

### Common Issues & Solutions

#### Database Connection Issues
```php
// Error: "Access denied for user"
// Solution: Check credentials in backend/config/db_connect.php
$DB_USER = 'your_mysql_username';
$DB_PASS = 'your_mysql_password';
```

#### Image Loading Issues
```bash
# Ensure proper server setup from project root
php -S localhost:8000

# Images should load from: /frontend/assets/images/
# Check image paths in database with:
php backend/update_course_images.php
```

#### Course Data Not Showing
```bash
# Add sample data if database is empty
cd backend
php add_sample_data.php
```

### Development Commands
```bash
# Start development server
php -S localhost:8000

# Add sample data
php backend/add_sample_data.php

# Update course images
php backend/update_course_images.php

# Check database status
php backend/dashboard_stats.php
```

### Browser Testing
- **Desktop**: Chrome, Firefox, Safari, Edge
- **Mobile**: iOS Safari, Android Chrome
- **Responsive**: All screen sizes 320px+

---

## 🏗️ Database Schema

The system uses a robust MySQL database with comprehensive relationships:

### Core Tables
- **users** - User accounts, profiles, and authentication
- **courses** - Course catalog with metadata and pricing
- **lessons** - Individual course modules and content  
- **enrollments** - User course registrations and progress
- **certificates** - Digital certificates with verification codes

### Content Management
- **blog_posts** - Educational articles and content
- **internships** - Career opportunities and applications
- **services** - Platform services and offerings
- **bookmarks** - User saved courses

### System Tables
- **newsletter_subscriptions** - Email marketing
- **campus_ambassador_applications** - Ambassador program
- **internship_applications** - Career applications

### Database Features
- **Foreign Key Constraints** - Data integrity enforcement
- **Automatic Timestamps** - Created/updated tracking
- **UTF8MB4 Encoding** - Full Unicode support including emojis
- **Indexed Columns** - Optimized query performance

See `backend/ER_ASCII.txt` for detailed entity relationships and `backend/sql/db_schema.sql` for complete table definitions.

---

## 🎯 API Endpoints

### Authentication
- `POST /backend/auth/signup_process.php` - User registration
- `POST /backend/auth/login_process.php` - User login
- `GET /backend/auth/logout.php` - User logout
- `POST /backend/auth/forgot_password.php` - Password reset request
- `POST /backend/auth/reset_password.php` - Password reset completion

### Course Management
- `GET /frontend/courses.php` - Course catalog with filtering
- `POST /backend/enroll_course.php` - Course enrollment
- `GET /backend/course_progress.php` - Progress tracking

### Admin Panel
- `GET /backend/admin/dashboard.php` - Admin dashboard
- `GET /backend/admin/users.php` - User management
- `POST /backend/admin/course_management.php` - Course administration

---

## 🔒 Security Features

### Authentication & Authorization
- **Session Management**: Secure PHP sessions with regeneration
- **Password Security**: Argon2 hashing with automatic salt generation
- **Role-based Access**: User/Instructor/Admin permission levels
- **Secure Logout**: Complete session cleanup

### Data Protection
- **SQL Injection Prevention**: PDO prepared statements exclusively
- **XSS Protection**: HTML entity encoding and CSP headers
- **CSRF Protection**: Token validation on forms
- **Input Validation**: Server-side data sanitization
- **Output Encoding**: Context-aware encoding

### Infrastructure Security
- **Rate Limiting**: Brute force attack prevention
- **Secure Headers**: Security header implementation
- **Database Security**: Non-root database user
- **File Upload Security**: Type validation and secure storage

---

## 📖 Documentation

Detailed documentation is available for each component:

- **[Frontend Documentation](frontend/README.md)**: Client-side setup, features, and development guide
- **[Backend Documentation](backend/README.md)**: Server-side setup, APIs, and database information
- **[Database Schema](backend/ER_ASCII.txt)**: Entity relationships and table structures

---

## 🤝 Contributing

We welcome contributions from the community! This project is part of **GirlScript Summer of Code 2025 (GSSoC'25)**.

### Getting Started
1. **Fork** the repository
2. **Clone** your fork locally
3. **Create** a new branch for your feature/fix
4. **Set up** the development environment following the installation guide

### Development Workflow
```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/Creators-Space-GroupProject.git
cd Creators-Space-GroupProject

# Install dependencies and setup database
./setup.bat  # Windows
./setup.sh   # Linux/Mac

# Create feature branch
git checkout -b feature/your-feature-name

# Make your changes and test
php -S localhost:8000

# Commit and push
git add .
git commit -m "feat: add your feature description"
git push origin feature/your-feature-name
```

### Code Standards
- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use ES6+ with consistent formatting
- **CSS**: Use BEM methodology for class naming
- **Database**: Follow SQL naming conventions (snake_case)
- **Comments**: Document complex logic and API endpoints

### Testing Guidelines
- Test all new features thoroughly
- Verify mobile responsiveness on multiple devices
- Check cross-browser compatibility
- Validate form submissions and error handling
- Test with different user roles (User/Instructor/Admin)

### Pull Request Process
1. **Update** documentation for any new features
2. **Add** screenshots for UI changes
3. **Test** on multiple browsers/devices
4. **Link** to related issues
5. **Request** review from maintainers

See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines.

---

## 🐛 Issue Tracking & Bug Reports

Found a bug or have a feature request? We use GitHub Issues for tracking.

### Reporting Bugs
When reporting bugs, please include:
- **Browser/Device** information
- **Steps to reproduce** the issue
- **Expected vs actual** behavior
- **Screenshots** if applicable
- **Console errors** if any

### Feature Requests
- Use the feature request template
- Describe the use case clearly
- Explain the expected behavior
- Consider implementation complexity

**Issue Tracker**: [GitHub Issues](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues)

---

## 📊 Project Insights

### Technical Stack Rationale
- **PHP**: Server-side processing with excellent MySQL integration
- **Vanilla JavaScript**: Fast performance without framework overhead
- **CSS Grid/Flexbox**: Modern responsive layouts
- **MySQL**: Reliable data persistence with ACID compliance

### Architecture Decisions
- **Separation of Concerns**: Clear frontend/backend boundaries
- **Database-Driven Content**: Dynamic data with easy administration
- **Mobile-First Design**: Responsive from the ground up
- **Security by Design**: Multi-layer security implementation

### Performance Optimizations
- **Lazy Loading**: Images load as needed
- **Database Indexing**: Optimized query performance
- **CSS/JS Minification**: Reduced file sizes
- **Image Optimization**: WebP format support

### Scalability Considerations
- **Modular Structure**: Easy to extend with new features
- **API-Ready**: Backend can support REST API implementation
- **Database Design**: Normalized structure supports growth
- **Cache-Friendly**: Static assets with proper headers

---

### Reporting Bugs
- Use the bug report template
- Include steps to reproduce
- Provide system information
- Add screenshots if applicable

### Feature Requests
- Use the feature request template
- Describe the use case
- Explain the expected behavior
- Consider implementation complexity

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🌟 Acknowledgments

- **GirlScript Summer of Code 2025** for platform and support
- **Contributors** who helped build and improve this platform
- **Open Source Community** for inspiration and best practices
- **Educational Technology** pioneers who paved the way

---

## 📞 Support & Contact

- **Website**: [Creators-Space Platform](https://creators-space.netlify.app/)
- **GitHub Issues**: [Report Issues](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues)
- **Email**: contact@creatorsspace.local
- **Documentation**: See `frontend/` and `backend/` README files

---

## 🚀 Future Roadmap

- [ ] **Mobile App**: React Native or Flutter mobile application
- [ ] **Video Streaming**: Integrated video hosting and streaming
- [ ] **Payment Integration**: Course purchase and subscription system
- [ ] **Discussion Forums**: Community discussion and Q&A
- [ ] **Live Classes**: Real-time video learning sessions
- [ ] **AI Recommendations**: Personalized course recommendations
- [ ] **Multi-language Support**: Internationalization and localization
- [ ] **API Documentation**: Comprehensive API documentation with Swagger

---

**Made with ❤️ by the Creators-Space Team**

*Empowering the next generation of tech innovators through quality education and hands-on learning.*
