# Backend - Creators-Space E-Learning Management System

## Overview

This is the backend component of the Creators-Space E-Learning Management System, built with PHP and MySQL. It provides RESTful APIs, authentication, and admin functionality.

## Requirements

- PHP 8.0 or higher
- MySQL 8.0 or higher (or MariaDB 10.4+)
- Web server (Apache/Nginx) or PHP built-in server
- Composer (optional, for future dependencies)

## Installation & Setup

### 1. Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE creators_space;
   ```

2. Import the database schema:
   ```bash
   mysql -u your_username -p creators_space < sql/db_schema.sql
   ```

3. Seed the admin user and sample data:
   ```bash
   mysql -u your_username -p creators_space < sql/seed_admin.sql
   ```

### 2. Configuration

1. Edit `config/db_connect.php` and update database credentials:
   ```php
   $DB_HOST = '127.0.0.1';
   $DB_NAME = 'creators_space';
   $DB_USER = 'your_username';
   $DB_PASS = 'your_password';
   ```

2. For production, use environment variables or a separate config file (not tracked in git).

### 3. Web Server Setup

#### Option A: PHP Built-in Server (Development)
```bash
cd backend/public
php -S localhost:8080
```

#### Option B: Apache/Nginx (Production)
Point your web server document root to `backend/public/` directory.

**Apache .htaccess example:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Default Admin Credentials

After running the seed script, you can log in to the admin panel with:

- **Email:** `admin@creatorsspace.local`
- **Password:** `password` (Default hash in seed file)
- **URL:** `http://your-domain/backend/public/admin_login.php`

**⚠️ IMPORTANT:** Change the admin password immediately after first login!

To generate a new password hash:
```bash
php -r "echo password_hash('YourNewPassword', PASSWORD_DEFAULT);"
```

## API Endpoints

### Authentication
- `POST /backend/auth/signup_process.php` - User registration
- `POST /backend/auth/login_process.php` - User login
- `GET /backend/auth/logout.php` - User logout
- `POST /backend/auth/forgot_password.php` - Request password reset
- `GET /backend/auth/reset_password.php` - Password reset page

### Admin Panel
- `GET /backend/public/admin_login.php` - Admin login page
- `GET /backend/admin/dashboard.php` - Admin dashboard
- `GET /backend/admin/users.php` - User management
- `GET /backend/admin/courses.php` - Course management

## Directory Structure

```
backend/
├── public/              # Public web root for backend
│   ├── index.php       # Backend API entry point
│   └── admin_login.php # Admin login page
├── auth/               # Authentication endpoints
│   ├── signup_process.php
│   ├── login_process.php
│   ├── logout.php
│   ├── forgot_password.php
│   └── reset_password.php
├── admin/              # Admin panel pages
│   ├── dashboard.php
│   └── users.php
├── config/             # Configuration files
│   └── db_connect.php  # Database connection
├── sql/                # Database files
│   ├── db_schema.sql   # Database schema
│   └── seed_admin.sql  # Admin user and sample data
├── lib/                # Helper libraries
│   └── helpers.php     # Common functions
├── ER_ASCII.txt        # Entity Relationship Diagram
└── README.md          # This file
```

## Security Features

### Password Security
- Passwords are hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`
- Password verification using `password_verify()`
- Minimum password requirements enforced

### Session Security
- Session ID regeneration on login to prevent session fixation
- Secure session configuration
- Session timeout handling

### Database Security
- PDO prepared statements to prevent SQL injection
- Input validation and sanitization
- Proper error handling without exposing sensitive information

### Authentication Security
- Rate limiting on login and password reset attempts
- Secure password reset tokens with expiration
- CSRF protection for forms
- Input validation and sanitization

### Admin Security
- Role-based access control
- Admin-only areas protected by authentication middleware
- Activity logging for security monitoring

## Rate Limiting

The system includes basic rate limiting for:
- Login attempts: 5 attempts per 15 minutes per IP
- Signup attempts: 5 attempts per 15 minutes per IP  
- Password reset: 3 attempts per 10 minutes per IP

## Email Configuration

For password reset functionality, configure email settings in `lib/helpers.php`:

```php
function sendEmail($to, $subject, $message, $headers = '') {
    // Configure your SMTP settings here
    // For production, integrate with services like:
    // - SendGrid
    // - Mailgun
    // - Amazon SES
}
```

For development, emails are logged to the error log.

## Database Schema

The system includes the following main tables:
- `users` - User accounts and authentication
- `courses` - Course information
- `lessons` - Course lessons/modules
- `enrollments` - User course enrollments
- `certificates` - Issued certificates
- `bookmarks` - User bookmarked courses
- `internships` - Internship listings
- `blog_posts` - Blog content
- `newsletter_subscriptions` - Email subscribers

See `ER_ASCII.txt` for detailed entity relationships.

## Development

### Adding New Admin Pages

1. Create PHP file in `admin/` directory
2. Include authentication check:
   ```php
   require_once __DIR__ . '/../config/db_connect.php';
   require_once __DIR__ . '/../lib/helpers.php';
   requireAdmin();
   ```

### Adding New API Endpoints

1. Create PHP file in appropriate directory
2. Include database connection and helpers
3. Implement proper error handling and validation
4. Return JSON responses using helper functions

### Database Migrations

For schema changes:
1. Create new SQL file in `sql/` directory
2. Document changes in this README
3. Update `ER_ASCII.txt` if needed

## Troubleshooting

### Common Issues

**Database Connection Failed:**
- Check database credentials in `config/db_connect.php`
- Ensure MySQL service is running
- Verify database exists and user has proper permissions

**Session Issues:**
- Check PHP session configuration
- Ensure proper file permissions for session storage
- Verify session cookies are enabled

**Email Not Sending:**
- Check email configuration in `lib/helpers.php`
- Verify SMTP settings
- Check server email capabilities

**Admin Login Not Working:**
- Verify admin user exists in database
- Check password hash in database
- Ensure user role is set to 'admin'

### Logs

- PHP errors: Check your web server error logs
- Application logs: Custom logs are written via `error_log()`
- Database errors: Enable MySQL query logging if needed

## Production Deployment

### Security Checklist

- [ ] Change default admin password
- [ ] Use environment variables for database credentials
- [ ] Enable HTTPS/SSL
- [ ] Configure proper file permissions
- [ ] Set up regular database backups
- [ ] Configure email service (SMTP/API)
- [ ] Enable proper error logging (not displaying to users)
- [ ] Set up monitoring and alerts
- [ ] Configure rate limiting at web server level
- [ ] Regular security updates

### Performance Optimization

- Enable PHP OPcache
- Configure database query caching
- Set up CDN for static assets
- Enable gzip compression
- Optimize database indexes
- Monitor and optimize slow queries

## Contributing

1. Follow PSR coding standards
2. Write proper documentation
3. Include error handling
4. Add input validation
5. Use prepared statements for database queries
6. Test thoroughly before submitting

## Support

For issues and questions:
- Check this documentation first
- Review error logs
- Contact the development team

---

**Last Updated:** September 2025  
**Version:** 1.0.0
