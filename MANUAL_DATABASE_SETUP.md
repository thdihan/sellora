# Manual Database Setup Guide

This guide explains how to set up Sellora using manual SQL files instead of Laravel migrations.

## 📁 Available SQL Files

The following SQL files are available in the `database/sql/` directory:

### Production Files (Recommended)

-   **`fresh_install.sql`** - Clean database structure for new installations (recommended for production)
-   **`schema_mysql.sql`** - Complete database structure with indexes and optimizations

### Development Files

-   **`data_mysql.sql`** - Sample data for development and testing
-   **`complete_mysql.sql`** - Complete database with structure and sample data

## 🚀 Setup Options

### Option 1: Fresh Production Installation (Recommended)

For new production installations without sample data:

1. **Create MySQL Database**

    ```sql
    CREATE DATABASE sellora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    CREATE USER 'sellora_user'@'localhost' IDENTIFIED BY 'your_secure_password';
    GRANT ALL PRIVILEGES ON sellora_db.* TO 'sellora_user'@'localhost';
    FLUSH PRIVILEGES;
    ```

2. **Import Structure**

    ```bash
    mysql -u sellora_user -p sellora_db < database/sql/fresh_install.sql
    ```

3. **Update Environment**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=sellora_db
    DB_USERNAME=sellora_user
    DB_PASSWORD=your_secure_password
    MANUAL_DATABASE_SETUP=true
    SKIP_MIGRATION_CHECK=true
    ```

### Option 2: Development with Sample Data

For development or testing with sample data:

1. **Create Database** (same as above)

2. **Import Complete Database**

    ```bash
    mysql -u sellora_user -p sellora_db < database/sql/complete_mysql.sql
    ```

3. **Update Environment** (same as above)

### Option 3: cPanel/phpMyAdmin Import

For shared hosting with cPanel:

1. **Create Database in cPanel**

    - Go to "MySQL Databases"
    - Create database (e.g., `your_account_sellora`)
    - Create user with full privileges

2. **Import via phpMyAdmin**

    - Open phpMyAdmin
    - Select your database
    - Go to "Import" tab
    - Choose `fresh_install.sql` (for production) or `complete_mysql.sql` (for development)
    - Click "Go"

3. **Update .env File**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_DATABASE=your_cpanel_database_name
    DB_USERNAME=your_cpanel_database_user
    DB_PASSWORD=your_cpanel_database_password
    MANUAL_DATABASE_SETUP=true
    SKIP_MIGRATION_CHECK=true
    ```

## 🔧 Configuration Details

### Environment Variables

When using manual database setup, ensure these variables are set:

```env
# Enable manual database mode
MANUAL_DATABASE_SETUP=true
SKIP_MIGRATION_CHECK=true

# MySQL Configuration
DB_CONNECTION=mysql
DB_HOST=your_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Laravel Configuration

The system is configured to:

-   Skip automatic migration checks
-   Use MySQL-optimized settings
-   Handle manual database imports
-   Provide proper error handling

## 🗂️ Database Structure Overview

### Core Tables

-   **users** - User accounts and authentication
-   **roles** - User roles and permissions
-   **products** - Product catalog
-   **orders** - Sales orders
-   **customers** - Customer information
-   **bills** - Billing information
-   **expenses** - Expense tracking

### Product Management

-   **product_categories** - Product categorization
-   **product_brands** - Brand information
-   **product_units** - Units of measurement
-   **warehouses** - Storage locations
-   **stock_balances** - Inventory tracking

### Advanced Features

-   **assessments** - Performance assessments
-   **sales_targets** - Sales goal tracking
-   **location_tracking** - GPS tracking
-   **presentations** - Presentation management
-   **reports** - Reporting system

## 🔄 Data Migration

### From Existing System

If migrating from another system:

1. **Export your existing data** to CSV/SQL format
2. **Import the fresh schema** first
3. **Map and import your data** to the appropriate tables
4. **Update relationships** and foreign keys

### Updating Database

To update the database structure:

1. **Backup your existing data**

    ```bash
    mysqldump -u username -p database_name > backup.sql
    ```

2. **Export updated schema**

    ```bash
    php generate-mysql-schema.php
    ```

3. **Import new structure**

    ```bash
    mysql -u username -p database_name < database/sql/fresh_install.sql
    ```

4. **Restore your data** (if compatible)

## 🛠️ Maintenance Commands

### Cache and Optimization

```bash
# Clear all caches
php artisan optimize:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link
```

### Database Verification

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check tables
>>> DB::select('SHOW TABLES');
```

## 🚨 Troubleshooting

### Common Issues

1. **Connection Refused**

    - Check MySQL service is running
    - Verify credentials in .env
    - Check host and port settings

2. **Permission Denied**

    - Verify database user has proper privileges
    - Check GRANT statements

3. **Charset Issues**

    - Ensure database uses utf8mb4
    - Check collation is utf8mb4_unicode_ci

4. **Import Errors**
    - Check MySQL version compatibility (5.7+)
    - Verify file encoding (UTF-8)
    - Check for syntax errors in SQL

### Verification Steps

After import, verify the setup:

```sql
-- Check tables exist
SHOW TABLES;

-- Check sample data (if imported)
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM roles;
SELECT COUNT(*) FROM products;

-- Verify relationships
SELECT u.name, r.name as role
FROM users u
LEFT JOIN roles r ON u.role_id = r.id
LIMIT 5;
```

## 📊 Performance Optimization

### Database Optimization

1. **Indexes** - Pre-defined in schema files
2. **Engine** - Uses InnoDB for transactions
3. **Charset** - UTF-8 (utf8mb4) for full Unicode
4. **Collation** - Case-insensitive Unicode

### Application Optimization

1. **Query Caching** - Enable MySQL query cache
2. **Connection Pooling** - Configure for production
3. **Monitoring** - Set up slow query log

## 🔐 Security Considerations

### Database Security

1. **User Privileges** - Grant only necessary permissions
2. **Network Access** - Restrict to localhost if possible
3. **Password Policy** - Use strong passwords
4. **Regular Backups** - Implement backup strategy

### Application Security

1. **Environment Files** - Never commit .env to version control
2. **Database Credentials** - Use environment variables
3. **Connection Encryption** - Enable SSL if available

## 📞 Support

### Getting Help

1. **Check logs** - `storage/logs/laravel.log`
2. **Database logs** - MySQL error logs
3. **Test connection** - Use `php artisan tinker`
4. **Verify environment** - Check `.env` file

---

**Note**: Always backup your database before making changes. Test in development environment first.
