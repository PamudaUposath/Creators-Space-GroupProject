# 🌐 Creators-Space E-Learning Management System

<img src="./frontend/assets/images/logo.png" alt="Creators-Space Logo" height="100px" />

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-orange)](https://www.mysql.com/)

**Creators-Space** is a comprehensive e-learning management system built with PHP and MySQL, designed to empower the next generation of tech innovators through quality education and hands-on learning experiences.

## 🚀 Major Update - PHP + MySQL Implementation

This project has been completely restructured and upgraded from a static HTML/localStorage system to a full-featured PHP + MySQL web application with:

- ✅ **Backend Architecture**: Organized into `frontend/` and `backend/` directories
- ✅ **User Authentication**: Secure login/signup with password hashing and session management
- ✅ **Admin Panel**: Complete administrative dashboard for user and content management
- ✅ **Database Integration**: MySQL database with proper relationships and constraints
- ✅ **Security Features**: CSRF protection, rate limiting, secure password reset
- ✅ **Role-based Access**: User, Instructor, and Admin roles with appropriate permissions

---

## 📁 Project Structure

```
Creators-Space-GroupProject/
├── frontend/                    # Client-facing application
│   ├── index.php               # Homepage
│   ├── login.php               # User login
│   ├── signup.php              # User registration
│   ├── courses.php             # Course catalog
│   ├── profile.php             # User profile
│   ├── assets/                 # Images, CSS, JS
│   ├── src/                    # Source files
│   └── README.md               # Frontend documentation
├── backend/                     # Server-side application
│   ├── public/                 # Public backend entry points
│   │   └── admin_login.php     # Admin login
│   ├── auth/                   # Authentication endpoints
│   │   ├── signup_process.php
│   │   ├── login_process.php
│   │   ├── logout.php
│   │   ├── forgot_password.php
│   │   └── reset_password.php
│   ├── admin/                  # Admin panel
│   │   ├── dashboard.php
│   │   └── users.php
│   ├── config/                 # Configuration
│   │   └── db_connect.php
│   ├── sql/                    # Database files
│   │   ├── db_schema.sql
│   │   └── seed_admin.sql
│   ├── lib/                    # Helper libraries
│   ├── ER_ASCII.txt           # Database ER diagram
│   └── README.md              # Backend documentation
├── docs/                       # Documentation and screenshots
│   ├── INSTALL.md             # Installation guide
│   ├── STATUS.md              # Project status
│   └── *.png                  # Screenshot files
├── setup.bat                   # Windows setup script
├── setup.sh                   # Linux/macOS setup script
├── .gitignore
├── CODE_OF_CONDUCT.md
├── CONTRIBUTING.md
├── LICENSE
└── README.md                  # This file
```

---

## ✨ Features

### 🔐 User Management
- **Secure Authentication**: Registration, login, logout with session management
- **Password Security**: Hashed passwords using PHP's `password_hash()`
- **Password Reset**: Secure token-based password reset via email
- **Role-based Access**: User, Instructor, and Admin roles
- **Profile Management**: Users can manage their profiles and preferences

### 📚 Course Management
- **Course Catalog**: Browse and search available courses
- **Enrollment System**: Users can enroll in courses
- **Progress Tracking**: Track learning progress and completion
- **Certificates**: Generate certificates upon course completion
- **Bookmarking**: Save courses for later reference

### 🎓 Learning Features
- **Interactive Lessons**: Structured course content with video support
- **Projects Portfolio**: Showcase user projects and work
- **Skills Tracking**: Track and display user skills and achievements
- **Blog System**: Educational articles and tech insights

### 💼 Career Services
- **Internship Portal**: Browse and apply for internship opportunities
- **Campus Ambassador Program**: Student ambassador applications
- **Career Guidance**: Professional development resources
- **Networking**: Connect with instructors and peers

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
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Web server (Apache/Nginx) or PHP built-in server
- Git (for cloning the repository)

### 1. Clone the Repository
```bash
git clone https://github.com/PamudaUposath/Creators-Space-GroupProject.git
cd Creators-Space-GroupProject
```

### 2. Database Setup
```bash
# Create MySQL database
mysql -u root -p
CREATE DATABASE creators_space;
exit

# Import schema and seed data
mysql -u root -p creators_space < backend/sql/db_schema.sql
mysql -u root -p creators_space < backend/sql/seed_admin.sql
```

### 3. Configure Database Connection
Edit `backend/config/db_connect.php`:
```php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'creators_space';
$DB_USER = 'root';          // Your MySQL username
$DB_PASS = '';              // Your MySQL password
```

### 4. Start Development Server
```bash
# Frontend (Port 8000)
cd frontend
php -S localhost:8000

# Backend (Port 8080) - In another terminal
cd backend/public
php -S localhost:8080
```

### 5. Access the Application
- **Frontend**: http://localhost:8000
- **Admin Panel**: http://localhost:8080/admin_login.php

### Default Admin Credentials
- **Email**: `admin@creatorsspace.local`
- **Password**: `password` (change this immediately!)

---

## 📸 Screenshots

### Homepage
![Homepage](docs/intro(light).png)

### Admin Dashboard
![Admin Dashboard](docs/course.png)

### User Login
![Login Page](docs/login.png)

### Course Catalog
![Courses](docs/course.png)

---

## 🏗️ Database Schema

The system uses a well-structured MySQL database with the following key entities:

- **Users**: Authentication and profile information
- **Courses**: Course content and metadata
- **Lessons**: Individual course modules
- **Enrollments**: User-course relationships
- **Certificates**: Completion certificates
- **Internships**: Career opportunities
- **Blog Posts**: Educational content

See `backend/ER_ASCII.txt` for detailed entity relationships and constraints.

---

## 🔒 Security Features

- **Authentication**: Secure session-based authentication
- **Password Security**: Hashed passwords with salt
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Token-based form protection
- **Rate Limiting**: Brute force attack prevention
- **Secure Password Reset**: Time-limited token system
- **Role-based Authorization**: Protected admin areas

---

## 📖 Documentation

Detailed documentation is available for each component:

- **[Frontend Documentation](frontend/README.md)**: Client-side setup, features, and development guide
- **[Backend Documentation](backend/README.md)**: Server-side setup, APIs, and database information
- **[Database Schema](backend/ER_ASCII.txt)**: Entity relationships and table structures

---

## 🤝 Contributing

We welcome contributions from the community! This project is part of **GirlScript Summer of Code 2025 (GSSoC'25)**.

### How to Contribute

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Contribution Guidelines

- Follow the existing code style and structure
- Write clear commit messages
- Add documentation for new features
- Test your changes thoroughly
- Ensure security best practices

See [CONTRIBUTING.md](CONTRIBUTING.md) for detailed guidelines.

---

## 🐛 Issue Tracking

Found a bug or have a feature request? Please check our [Issue Tracker](https://github.com/PamudaUposath/Creators-Space-GroupProject/issues).

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
