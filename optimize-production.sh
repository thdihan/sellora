#!/bin/bash

# Production Optimization Script for Sellora
# Run this script after deploying to production

echo "Sellora Production Optimization"
echo "==============================="

# Check if we're in the right directory
if [ ! -f artisan ]; then
    echo "Error: artisan file not found!"
    echo "Please run this script from the Laravel root directory."
    exit 1
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo "Error: .env file not found!"
    echo "Please create your .env file first."
    exit 1
fi

echo "Starting production optimization..."

# Step 1: Clear all caches
echo "1. Clearing all caches..."
php artisan optimize:clear

# Step 2: Install/update dependencies for production
echo "2. Installing production dependencies..."
composer install --optimize-autoloader --no-dev --no-interaction

# Step 3: Generate application key if needed
echo "3. Checking application key..."
if ! grep -q "APP_KEY=base64:" .env; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Step 4: Run database migrations
echo "4. Running database migrations..."
php artisan migrate --force

# Step 5: Seed database (optional)
read -p "Do you want to seed the database? (y/N): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Seeding database..."
    php artisan db:seed --force
fi

# Step 6: Create storage link
echo "6. Creating storage symbolic link..."
php artisan storage:link

# Step 7: Set proper permissions
echo "7. Setting file permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env

# Step 8: Cache everything for production
echo "8. Caching configuration for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Step 9: Optimize autoloader
echo "9. Optimizing Composer autoloader..."
composer dump-autoload --optimize --no-dev

# Step 10: Final optimization
echo "10. Running final optimization..."
php artisan optimize

echo ""
echo "Production optimization completed successfully!"
echo ""
echo "Your application is now optimized for production."
echo ""
echo "Important reminders:"
echo "- Make sure APP_DEBUG=false in .env"
echo "- Ensure all sensitive data is secured"
echo "- Set up regular backups"
echo "- Monitor error logs regularly"
echo "- Keep dependencies updated"
echo ""
echo "Application is ready for production use!"
