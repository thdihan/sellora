#!/bin/bash

# Manual Database Import Script for Sellora
# This script helps import SQL files instead of using Laravel migrations

echo "Sellora Manual Database Import"
echo "============================="

# Check if .env file exists
if [ ! -f .env ]; then
    echo "Error: .env file not found!"
    echo "Please create your .env file first."
    exit 1
fi

# Source environment variables
source .env

echo "Database Configuration:"
echo "Host: $DB_HOST"
echo "Database: $DB_DATABASE"
echo "Username: $DB_USERNAME"
echo ""

# Check if SQL files directory exists
if [ ! -d "database/sql" ]; then
    echo "Error: database/sql directory not found!"
    echo "Please run the export script first to generate SQL files."
    exit 1
fi

# Menu for import options
echo "Select import option:"
echo "1. Fresh installation (production - no sample data)"
echo "2. Complete database (development - with sample data)"
echo "3. Schema only (structure without data)"
echo "4. Custom file import"
echo ""
read -p "Enter your choice (1-4): " choice

case $choice in
    1)
        SQL_FILE="database/sql/fresh_install.sql"
        echo "Importing fresh installation..."
        ;;
    2)
        SQL_FILE="database/sql/complete_mysql.sql"
        echo "Importing complete database with sample data..."
        ;;
    3)
        SQL_FILE="database/sql/schema_mysql.sql"
        echo "Importing schema only..."
        ;;
    4)
        echo "Available SQL files:"
        ls -la database/sql/*.sql
        echo ""
        read -p "Enter SQL file path: " SQL_FILE
        ;;
    *)
        echo "Invalid choice!"
        exit 1
        ;;
esac

# Check if file exists
if [ ! -f "$SQL_FILE" ]; then
    echo "Error: SQL file not found: $SQL_FILE"
    exit 1
fi

echo ""
echo "File to import: $SQL_FILE"
echo "Target database: $DB_DATABASE"
echo ""

# Confirm import
read -p "Are you sure you want to proceed? This will overwrite existing data! (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "Import cancelled."
    exit 0
fi

# Create database if it doesn't exist (MySQL only)
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Creating database if it doesn't exist..."
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "Database ready."
    else
        echo "Warning: Could not create database. It may already exist or you may not have permissions."
    fi
fi

# Import SQL file
echo "Importing SQL file..."
if [ "$DB_CONNECTION" = "mysql" ]; then
    mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < "$SQL_FILE"
    
    if [ $? -eq 0 ]; then
        echo "Database import completed successfully!"
    else
        echo "Error: Database import failed!"
        exit 1
    fi
else
    echo "Error: This script only supports MySQL databases."
    exit 1
fi

# Post-import tasks
echo ""
echo "Running post-import tasks..."

# Clear caches
echo "1. Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Create storage link
echo "2. Creating storage symbolic link..."
php artisan storage:link

# Set proper permissions
echo "3. Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Cache configuration for production
if [ "$APP_ENV" = "production" ]; then
    echo "4. Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

echo ""
echo "Import completed successfully!"
echo ""
echo "Next steps:"
echo "1. Test your application: php artisan serve"
echo "2. Create your admin user if needed"
echo "3. Configure your application settings"
echo ""
echo "Database import summary:"
echo "- File imported: $SQL_FILE"
echo "- Database: $DB_DATABASE"
echo "- Environment: $APP_ENV"
echo ""
