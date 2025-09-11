#!/bin/bash

# Creators-Space E-Learning Management System Setup Script
# This script helps set up the database and initial configuration

echo "=================================================="
echo "Creators-Space E-Learning Management System Setup"
echo "=================================================="
echo ""

# Check if MySQL is available
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL is not installed or not in PATH"
    echo "Please install MySQL and try again"
    exit 1
fi

echo "✅ MySQL found"

# Database configuration
DB_NAME="creators_space"
DB_USER=${1:-"root"}
DB_PASS=${2:-""}

echo "Database Configuration:"
echo "- Database Name: $DB_NAME"
echo "- Username: $DB_USER"
echo "- Password: ${DB_PASS:-"(empty)"}"
echo ""

# Create database
echo "📝 Creating database..."
mysql -u $DB_USER -p$DB_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Database '$DB_NAME' created successfully"
else
    echo "❌ Failed to create database. Please check your MySQL credentials."
    exit 1
fi

# Import schema
echo "📝 Importing database schema..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < backend/sql/db_schema.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Database schema imported successfully"
else
    echo "❌ Failed to import schema"
    exit 1
fi

# Import seed data
echo "📝 Importing seed data..."
mysql -u $DB_USER -p$DB_PASS $DB_NAME < backend/sql/seed_admin.sql 2>/dev/null

if [ $? -eq 0 ]; then
    echo "✅ Seed data imported successfully"
else
    echo "❌ Failed to import seed data"
    exit 1
fi

# Update database configuration
echo "📝 Updating database configuration..."
sed -i "s/\$DB_USER = 'root';/\$DB_USER = '$DB_USER';/" backend/config/db_connect.php
sed -i "s/\$DB_PASS = '';/\$DB_PASS = '$DB_PASS';/" backend/config/db_connect.php

echo "✅ Database configuration updated"

echo ""
echo "🎉 Setup completed successfully!"
echo ""
echo "Default Admin Credentials:"
echo "Email: admin@creatorsspace.local"
echo "Password: password"
echo ""
echo "⚠️  IMPORTANT: Change the admin password after first login!"
echo ""
echo "To start the development server:"
echo "Frontend: cd frontend && php -S localhost:8000"
echo "Backend:  cd backend/public && php -S localhost:8080"
echo ""
echo "Access URLs:"
echo "Frontend: http://localhost:8000"
echo "Admin Panel: http://localhost:8080/admin_login.php"
echo ""
