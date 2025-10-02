#!/bin/bash

# Database Migration Script from SQLite to MySQL
# This script helps migrate data from SQLite to MySQL

echo "Sellora Database Migration Script"
echo "================================="

# Check if .env file exists
if [ ! -f .env ]; then
    echo "Error: .env file not found!"
    echo "Please make sure you're running this script from the Laravel root directory."
    exit 1
fi

# Source environment variables
source .env

echo "Starting database migration process..."

# Step 1: Clear any cached config
echo "1. Clearing Laravel cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 2: Run migrations
echo "2. Running database migrations..."
php artisan migrate:fresh --force

if [ $? -ne 0 ]; then
    echo "Error: Migration failed!"
    exit 1
fi

# Step 3: Seed database
echo "3. Seeding database with initial data..."
php artisan db:seed --force

if [ $? -ne 0 ]; then
    echo "Warning: Seeding failed or partially completed"
fi

# Step 4: Create storage link
echo "4. Creating storage symbolic link..."
php artisan storage:link

# Step 5: Cache configuration for production
echo "5. Caching configuration for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "Migration completed successfully!"
echo ""
echo "Next steps:"
echo "1. Update your .env file with production MySQL credentials"
echo "2. Test the application thoroughly"
echo "3. Upload to your cPanel hosting"
echo ""
echo "Database: $DB_DATABASE"
echo "Connection: $DB_CONNECTION"
echo ""
