# Installation Guide

## Prerequisites

### Required Software

1. **PHP 8.0 or higher**
   - Download from: https://www.php.net/downloads
   - Ensure the following extensions are enabled:
     - `pdo_mysql`
     - `mbstring`
     - `openssl`
     - `session`

2. **MySQL 8.0 or higher**
   - Download from: https://dev.mysql.com/downloads/mysql/
   - Alternative: MariaDB 10.4+

3. **Web Server (choose one)**
   - **XAMPP** (Recommended for development): https://www.apachefriends.org/
   - **WAMP** (Windows): http://www.wampserver.com/
   - **MAMP** (macOS): https://www.mamp.info/
   - Apache or Nginx for production

## Installation Methods

### Method 1: Automated Setup (Recommended)

#### For Windows Users (XAMPP):
1. Clone or download the project to `C:\xampp\htdocs\Creators-Space-GroupProject\`
2. Open Command Prompt as Administrator
3. Navigate to the project directory:
   ```cmd
   cd C:\xampp\htdocs\Creators-Space-GroupProject
   ```
4. Run the setup script:
   ```cmd
   setup.bat [username] [password]
   ```
   Example:
   ```cmd
   setup.bat root mypassword
   ```

#### For Linux/macOS Users:
1. Clone or download the project
2. Make the setup script executable:
   ```bash
   chmod +x setup.sh
   ```
3. Run the setup script:
   ```bash
   ./setup.sh [username] [password]
   ```

### Method 2: Manual Setup

#### Step 1: Database Setup

1. **Start MySQL Server**
   - XAMPP: Start MySQL from XAMPP Control Panel
   - Standalone: `sudo systemctl start mysql` (Linux) or start MySQL service

2. **Create Database**
   ```sql
   CREATE DATABASE creators_space DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import Database Schema**
   ```bash
   mysql -u root -p creators_space < backend/sql/db_schema.sql
   ```

4. **Import Seed Data**
   ```bash
   mysql -u root -p creators_space < backend/sql/seed_admin.sql
   ```

#### Step 2: Configuration

1. **Update Database Configuration**
   Edit `backend/config/db_connect.php`:
   ```php
   $DB_HOST = 'localhost';    // Your MySQL host
   $DB_NAME = 'creators_space'; // Database name
   $DB_USER = 'root';         // Your MySQL username
   $DB_PASS = 'your_password'; // Your MySQL password
   ```

2. **Set File Permissions** (Linux/macOS only)
   ```bash
   chmod 755 frontend/
   chmod 755 backend/
   chmod 644 backend/config/db_connect.php
   ```

#### Step 3: Web Server Configuration

##### For XAMPP:
1. Place project in `C:\xampp\htdocs\Creators-Space-GroupProject\`
2. Start Apache from XAMPP Control Panel
3. Access via: `http://localhost/Creators-Space-GroupProject/frontend/`

##### For Development Server:
**Frontend:**
```bash
cd frontend
php -S localhost:8000
```

**Backend API:**
```bash
cd backend/public
php -S localhost:8080
```

##### For Production (Apache):
1. **Virtual Host Configuration** (optional)
   Edit `/etc/apache2/sites-available/creators-space.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName creators-space.local
       DocumentRoot /path/to/Creators-Space-GroupProject/frontend
       
       <Directory /path/to/Creators-Space-GroupProject/frontend>
           AllowOverride All
           Require all granted
       </Directory>
       
       Alias /api /path/to/Creators-Space-GroupProject/backend/public
       <Directory /path/to/Creators-Space-GroupProject/backend/public>
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

2. **Enable Virtual Host**
   ```bash
   sudo a2ensite creators-space.conf
   sudo systemctl reload apache2
   ```

## Verification

### Test Database Connection
Visit: `http://localhost:8080/test_connection.php` (if using dev server)

### Default Admin Access
- **URL:** `http://localhost/backend/public/admin_login.php`
- **Email:** `admin@creatorsspace.local`
- **Password:** `password`

⚠️ **IMPORTANT:** Change the admin password immediately after first login!

### Frontend Access
- **URL:** `http://localhost/frontend/`

## Troubleshooting

### Common Issues

1. **"Access denied" Database Error**
   - Check MySQL credentials in `backend/config/db_connect.php`
   - Ensure MySQL service is running
   - Verify user has proper privileges

2. **"Call to undefined function mysql_connect"**
   - Enable `pdo_mysql` extension in `php.ini`
   - Restart web server after changes

3. **Session Issues**
   - Check PHP session configuration
   - Ensure write permissions on session directory
   - Clear browser cookies/cache

4. **File Permission Errors** (Linux/macOS)
   ```bash
   sudo chown -R www-data:www-data /path/to/project
   sudo chmod -R 755 /path/to/project
   ```

5. **XAMPP MySQL Won't Start**
   - Check if port 3306 is in use
   - Stop other MySQL services
   - Check XAMPP error logs

### PHP Configuration

Ensure these settings in `php.ini`:
```ini
extension=pdo_mysql
extension=mbstring
extension=openssl
session.auto_start = 0
session.cookie_httponly = 1
session.cookie_secure = 0  # Set to 1 for HTTPS
max_execution_time = 300
memory_limit = 256M
```

### MySQL Configuration

For development, ensure these settings in `my.cnf`:
```ini
[mysqld]
max_allowed_packet = 64M
innodb_buffer_pool_size = 256M
default_authentication_plugin = mysql_native_password
```

## Development Environment

### Recommended IDE Extensions
- **VS Code:**
  - PHP Intelephense
  - MySQL
  - GitLens
  - Prettier

### Development Workflow
1. **Frontend Development:** Edit files in `frontend/`
2. **Backend API:** Edit files in `backend/`
3. **Database Changes:** Update `backend/sql/` files
4. **Testing:** Use browser dev tools and error logs

### File Structure
```
Creators-Space-GroupProject/
├── frontend/              # User-facing application
├── backend/              # Admin panel and API
├── setup.bat            # Windows setup script
├── setup.sh             # Linux/macOS setup script
└── INSTALL.md           # This file
```

## Security Notes

### Production Deployment
1. **Change default passwords**
2. **Use HTTPS** (SSL certificate)
3. **Enable PHP security settings**
4. **Configure firewall rules**
5. **Regular security updates**
6. **Database user with minimal privileges**

### Environment Variables (Recommended)
Create `.env` file for sensitive data:
```env
DB_HOST=localhost
DB_NAME=creators_space
DB_USER=your_username
DB_PASS=your_secure_password
SECRET_KEY=your_secret_key_here
```

## Support

### Log Files
- **PHP Errors:** Check `error_log` in PHP directory
- **Apache Errors:** `/var/log/apache2/error.log`
- **MySQL Errors:** `/var/log/mysql/error.log`

### Getting Help
1. Check the troubleshooting section above
2. Review log files for error messages
3. Ensure all prerequisites are met
4. Verify file permissions and configurations

## Next Steps

After successful installation:
1. **Login to admin panel** and change default password
2. **Configure email settings** for password reset functionality
3. **Add courses and content** through admin panel
4. **Customize frontend** styling and branding
5. **Set up backup procedures** for database

---

**Note:** This installation guide covers development setup. For production deployment, additional security measures and server configuration are required.
