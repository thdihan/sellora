# Sellora - MySQL Migration & cPanel Deployment

This document outlines the complete conversion of Sellora from SQLite to MySQL and preparation for cPanel deployment.

## 🔄 What Was Changed

### Database Configuration

-   ✅ Updated `config/database.php` to default to MySQL
-   ✅ Optimized MySQL connection settings for production
-   ✅ Added MySQL-specific optimizations (InnoDB engine, buffered queries)

### Environment Configuration

-   ✅ Updated `.env` to use MySQL by default
-   ✅ Created `.env.production` template for production deployment
-   ✅ Updated database credentials format

### Composer Configuration

-   ✅ Removed SQLite-specific post-install scripts
-   ✅ Added production deployment scripts
-   ✅ Added cache clearing scripts

### Apache/cPanel Configuration

-   ✅ Enhanced `.htaccess` with security headers and performance optimizations
-   ✅ Added compression and caching rules
-   ✅ Configured security file access restrictions

## 📁 New Files Created

### Deployment Files

-   `CPANEL_DEPLOYMENT.md` - Complete cPanel deployment guide
-   `MYSQL_SETUP.md` - Quick MySQL setup instructions
-   `cpanel-index.php` - Root domain deployment index file
-   `.env.production` - Production environment template

### Setup Scripts

-   `migrate-to-mysql.sh` - Automated MySQL migration script
-   `optimize-production.sh` - Production optimization script
-   `setup-mysql.php` - MySQL database setup helper

## 🚀 Deployment Options

### Option 1: Local Development with MySQL

```bash
# 1. Install MySQL locally
# 2. Create database and user
# 3. Update .env file
# 4. Run migration
./migrate-to-mysql.sh
```

### Option 2: cPanel Subdirectory Deployment

1. Upload entire project to `public_html/sellora/`
2. Access via `yourdomain.com/sellora/public`
3. Follow `CPANEL_DEPLOYMENT.md` guide

### Option 3: cPanel Root Domain Deployment (Recommended)

1. Upload Laravel app to `public_html/sellora/`
2. Move `public/*` contents to `public_html/`
3. Replace `index.php` with `cpanel-index.php`
4. Access via `yourdomain.com`

## 📋 Pre-Deployment Checklist

### Local Testing

-   [ ] MySQL server installed and running
-   [ ] Database and user created
-   [ ] `.env` file updated with MySQL credentials
-   [ ] Migration script executed successfully
-   [ ] Application tested locally with MySQL

### Production Preparation

-   [ ] cPanel MySQL database created
-   [ ] Database user with full privileges created
-   [ ] `.env.production` customized with your credentials
-   [ ] SSL certificate configured (recommended)
-   [ ] File permissions verified

### Security Checklist

-   [ ] `APP_DEBUG=false` in production
-   [ ] Strong database passwords used
-   [ ] `.env` file secured (not publicly accessible)
-   [ ] Sensitive files hidden via `.htaccess`
-   [ ] Security headers enabled
-   [ ] Regular backups scheduled

## 🛠️ Quick Commands

### Local Development

```bash
# Switch to MySQL
./migrate-to-mysql.sh

# Clear all caches
php artisan optimize:clear

# Cache for production
php artisan optimize
```

### Production Deployment

```bash
# Full production optimization
./optimize-production.sh

# Manual steps
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan storage:link
```

## 🔧 Configuration Details

### Database Settings

-   **Engine**: InnoDB (for better performance and foreign key support)
-   **Charset**: utf8mb4 (full Unicode support)
-   **Collation**: utf8mb4_unicode_ci (case-insensitive Unicode)
-   **Connection**: Optimized with buffered queries and prepared statements

### Performance Optimizations

-   **Caching**: Config, routes, and views cached in production
-   **Compression**: Gzip compression for text assets
-   **Browser Caching**: Long-term caching for static assets
-   **Autoloader**: Optimized Composer autoloader

### Security Features

-   **Headers**: Security headers (XSS, CSRF, Content-Type protection)
-   **File Access**: Sensitive files blocked from public access
-   **HTTPS**: Ready for HTTPS with HSTS header (commented)
-   **PHP Settings**: Memory and execution limits configured

## 📞 Support & Troubleshooting

### Common Issues

1. **500 Internal Server Error**

    - Check file permissions (755 for directories, 644 for files)
    - Verify `.env` file exists and is properly configured
    - Check error logs in cPanel

2. **Database Connection Error**

    - Verify database credentials in `.env`
    - Ensure database user has proper privileges
    - Check if database server is accessible

3. **Storage/Upload Issues**

    - Run `php artisan storage:link`
    - Check storage directory permissions
    - Verify disk space availability

4. **Performance Issues**
    - Enable OPcache in PHP settings
    - Run production optimization script
    - Monitor database queries for optimization

### Required PHP Extensions

-   PDO MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, ZIP

### Hosting Requirements

-   PHP 8.2+
-   MySQL 5.7+ or MariaDB 10.3+
-   Minimum 256MB memory limit
-   SSL certificate (recommended)

## 📄 Documentation Links

-   [Complete cPanel Deployment Guide](CPANEL_DEPLOYMENT.md)
-   [Quick MySQL Setup](MYSQL_SETUP.md)
-   [Laravel Documentation](https://laravel.com/docs)

---

**Note**: Always test thoroughly in a staging environment before deploying to production. Keep backups of your database and files.
