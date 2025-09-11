@echo off
REM Creators-Space E-Learning Management System Setup Script
REM This script helps set up the database and initial configuration

echo ==================================================
echo Creators-Space E-Learning Management System Setup
echo ==================================================
echo.

REM Database configuration
set DB_NAME=creators_space
set DB_USER=%1
if "%DB_USER%"=="" set DB_USER=root
set DB_PASS=%2
if "%DB_PASS%"=="" set DB_PASS=

echo Database Configuration:
echo - Database Name: %DB_NAME%
echo - Username: %DB_USER%
echo - Password: %DB_PASS%
echo.

REM Check if MySQL is available
mysql --version >nul 2>&1
if errorlevel 1 (
    echo ❌ MySQL is not installed or not in PATH
    echo Please install MySQL and try again
    pause
    exit /b 1
)

echo ✅ MySQL found

REM Create database
echo 📝 Creating database...
mysql -u %DB_USER% -p%DB_PASS% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" >nul 2>&1

if errorlevel 1 (
    echo ❌ Failed to create database. Please check your MySQL credentials.
    pause
    exit /b 1
)

echo ✅ Database '%DB_NAME%' created successfully

REM Import schema
echo 📝 Importing database schema...
mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < backend\sql\db_schema.sql >nul 2>&1

if errorlevel 1 (
    echo ❌ Failed to import schema
    pause
    exit /b 1
)

echo ✅ Database schema imported successfully

REM Import seed data
echo 📝 Importing seed data...
mysql -u %DB_USER% -p%DB_PASS% %DB_NAME% < backend\sql\seed_admin.sql >nul 2>&1

if errorlevel 1 (
    echo ❌ Failed to import seed data
    pause
    exit /b 1
)

echo ✅ Seed data imported successfully

echo.
echo 🎉 Setup completed successfully!
echo.
echo Default Admin Credentials:
echo Email: admin@creatorsspace.local
echo Password: password
echo.
echo ⚠️  IMPORTANT: Change the admin password after first login!
echo.
echo To start the development server:
echo Frontend: cd frontend ^&^& php -S localhost:8000
echo Backend:  cd backend\public ^&^& php -S localhost:8080
echo.
echo Access URLs:
echo Frontend: http://localhost:8000
echo Admin Panel: http://localhost:8080/admin_login.php
echo.
pause
