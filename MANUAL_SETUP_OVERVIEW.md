# Sellora - Manual Database Setup Overview

## 🎯 **NEW APPROACH: Manual SQL File Import**

Instead of using Laravel migrations, Sellora now uses **manual SQL file imports** for better control and easier deployment to shared hosting environments like cPanel.

## 📁 **Generated SQL Files**

The following files are available in `database/sql/`:

### Production Files (Recommended)

-   **`fresh_install.sql`** - Clean database structure for production
-   **`schema_mysql.sql`** - Complete optimized database structure

### Development Files

-   **`complete_mysql.sql`** - Structure + sample data for development
-   **`data_mysql.sql`** - Sample data only

## 🚀 **Quick Setup**

### For Local Development

```bash
# 1. Create MySQL database
mysql -u root -p -e "CREATE DATABASE sellora_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import database structure and data
mysql -u root -p sellora_db < database/sql/complete_mysql.sql

# 3. Update .env file
DB_CONNECTION=mysql
DB_DATABASE=sellora_db
DB_USERNAME=root
DB_PASSWORD=your_password
MANUAL_DATABASE_SETUP=true

# 4. Verify setup
php verify-database.php
```

### For cPanel Production

```bash
# 1. Create database in cPanel MySQL Databases
# 2. Import database/sql/fresh_install.sql via phpMyAdmin
# 3. Update .env.production with your cPanel credentials
# 4. Upload and configure (see CPANEL_DEPLOYMENT.md)
```

## 🛠️ **Available Scripts**

### Database Management

-   **`export-database.php`** - Export SQLite to MySQL SQL files
-   **`generate-mysql-schema.php`** - Generate optimized MySQL schema
-   **`import-database.sh`** - Interactive database import script
-   **`verify-database.php`** - Verify database setup

### Deployment Scripts

-   **`optimize-production.sh`** - Production optimization
-   **`cpanel-index.php`** - Root domain deployment helper

## 📋 **Complete Setup Process**

### 1. Generate SQL Files (Done)

```bash
php export-database.php           # Basic export
php generate-mysql-schema.php     # Optimized export
```

### 2. Setup Database

```bash
# Interactive import
./import-database.sh

# Or manual import
mysql -u username -p database_name < database/sql/fresh_install.sql
```

### 3. Configure Environment

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
MANUAL_DATABASE_SETUP=true
SKIP_MIGRATION_CHECK=true
```

### 4. Verify Setup

```bash
php verify-database.php
```

### 5. Production Optimization

```bash
./optimize-production.sh
```

## 🔧 **Configuration Details**

### Manual Database Mode

-   **MANUAL_DATABASE_SETUP=true** - Enables manual database mode
-   **SKIP_MIGRATION_CHECK=true** - Disables Laravel migration checks
-   Uses optimized MySQL schema with proper indexes and constraints

### MySQL Optimizations

-   **Engine**: InnoDB for ACID compliance and foreign keys
-   **Charset**: utf8mb4 for full Unicode support
-   **Collation**: utf8mb4_unicode_ci for case-insensitive searches
-   **Indexes**: Optimized indexes for better performance

## 🎁 **Benefits of Manual Setup**

### ✅ **Advantages**

-   **Shared Hosting Friendly** - Works on cPanel and similar hosting
-   **No Migration Dependencies** - No need for Artisan commands
-   **Direct Database Control** - Full control over database structure
-   **Easy Backups** - Simple SQL file backups and restores
-   **Version Control Friendly** - SQL files can be versioned
-   **Hosting Provider Flexible** - Works with any MySQL hosting

### 📊 **Perfect For**

-   cPanel shared hosting
-   Managed WordPress hosting with MySQL
-   Docker containers
-   Development environments
-   Production deployments
-   Database migrations between environments

## 📚 **Documentation**

-   **[Manual Database Setup Guide](MANUAL_DATABASE_SETUP.md)** - Complete setup instructions
-   **[cPanel Deployment Guide](CPANEL_DEPLOYMENT.md)** - cPanel-specific deployment
-   **[MySQL Setup Guide](MYSQL_SETUP.md)** - Quick MySQL configuration

## 🔍 **Verification**

After setup, verify everything works:

```bash
# Check database connection and tables
php verify-database.php

# Test Laravel connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
```

## 🎯 **Next Steps**

1. **Choose your setup method** (local development vs production)
2. **Import the appropriate SQL file** (fresh_install.sql or complete_mysql.sql)
3. **Configure your environment** (.env file)
4. **Verify the setup** (verify-database.php)
5. **Deploy to production** (follow cPanel deployment guide)

---

**This approach eliminates the complexity of Laravel migrations while providing maximum compatibility with various hosting environments.**
