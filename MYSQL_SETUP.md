# Sellora - Quick MySQL Setup Guide

## Local Development Setup

### 1. Install MySQL (if not already installed)

```bash
# Ubuntu/Debian
sudo apt install mysql-server

# macOS with Homebrew
brew install mysql

# Windows - Download from MySQL website
```

### 2. Create Local Database

```bash
mysql -u root -p
CREATE DATABASE sellora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sellora_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON sellora_db.* TO 'sellora_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Update Environment File

```bash
# Update .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sellora_db
DB_USERNAME=sellora_user
DB_PASSWORD=your_password
```

### 4. Run Migration

```bash
# Make migration script executable
chmod +x migrate-to-mysql.sh

# Run migration
./migrate-to-mysql.sh
```

## cPanel Production Deployment

### 1. Create Database in cPanel

1. Login to cPanel
2. Go to "MySQL Databases"
3. Create database: `your_account_sellora`
4. Create user with full privileges
5. Note credentials

### 2. Upload Files

1. Zip your entire project
2. Upload to cPanel File Manager
3. Extract to public_html

### 3. Configure Environment

```bash
# Copy production environment file
cp .env.production .env

# Edit .env with your cPanel database credentials
APP_URL=https://yourdomain.com
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_cpanel_database_password
```

### 4. Setup Database

```bash
# Via SSH (if available)
php setup-mysql.php
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
```

### 5. File Structure Options

#### Option A: Subdirectory (Easier)

-   Access via: `yourdomain.com/sellora/public`
-   No file moving required

#### Option B: Root Domain (Recommended)

1. Move Laravel app to `public_html/sellora/`
2. Move `public/*` contents to `public_html/`
3. Replace `public_html/index.php` with `cpanel-index.php`
4. Access via: `yourdomain.com`

## Testing

1. Visit your website
2. Test database connection
3. Test file uploads
4. Check error logs

## Troubleshooting

### Common Issues:

-   **500 Error**: Check file permissions (755 for directories, 644 for files)
-   **Database Error**: Verify credentials in .env
-   **Storage Issues**: Run `php artisan storage:link`
-   **Cache Issues**: Clear all caches with `php artisan optimize:clear`

### Required PHP Extensions:

-   PDO MySQL
-   OpenSSL
-   Mbstring
-   Tokenizer
-   XML
-   Ctype
-   JSON
-   BCMath
-   Fileinfo
-   ZIP

## Performance Tips

1. Enable OPcache in PHP settings
2. Use production caching:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
3. Enable gzip compression in .htaccess
4. Optimize images and assets
5. Use CDN for static files

## Security Checklist

-   [ ] Set `APP_DEBUG=false` in production
-   [ ] Use strong database passwords
-   [ ] Enable HTTPS with SSL certificate
-   [ ] Set proper file permissions
-   [ ] Hide sensitive files (.env, composer.json)
-   [ ] Enable security headers in .htaccess
-   [ ] Regular backups

## Support

For deployment issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check cPanel error logs
3. Verify PHP version and extensions
4. Contact hosting provider for server issues
