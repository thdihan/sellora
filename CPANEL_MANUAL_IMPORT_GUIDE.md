# Sellora - cPanel Manual Database Import Guide

This comprehensive guide walks you through deploying Sellora to cPanel hosting using **manual SQL file imports** instead of Laravel migrations.

## 🎯 **Why Manual SQL Import?**

-   ✅ **Works on ALL shared hosting** (no SSH required)
-   ✅ **No Laravel migration dependencies**
-   ✅ **Compatible with phpMyAdmin**
-   ✅ **Faster deployment process**
-   ✅ **Better for production environments**

## 📋 **Prerequisites**

### cPanel Account Requirements

-   **PHP 8.2+** support
-   **MySQL 5.7+** or **MariaDB 10.3+**
-   **File Manager** access
-   **phpMyAdmin** access
-   **Minimum 256MB** memory limit

### Required PHP Extensions

-   `mysqli`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `zip`

## 🚀 **Step-by-Step Deployment**

### **STEP 1: Prepare SQL Files (Already Done)**

Your project includes these pre-generated SQL files in `database/sql/`:

-   **`fresh_install.sql`** ← **Use this for production** (clean database)
-   **`complete_mysql.sql`** ← Use for testing (includes sample data)

### **STEP 2: Create MySQL Database in cPanel**

1. **Login to cPanel**
2. **Find "MySQL Databases"** (under Databases section)
3. **Create Database:**
    - Database Name: `sellora_db` (or your preferred name)
    - Click "Create Database"
4. **Create Database User:**
    - Username: `sellora_user` (or your preferred username)
    - Password: Generate a strong password
    - Click "Create User"
5. **Add User to Database:**
    - Select your user and database
    - Grant **ALL PRIVILEGES**
    - Click "Make Changes"
6. **Note Your Credentials:**
    ```
    Database: your_cpanel_prefix_sellora_db
    Username: your_cpanel_prefix_sellora_user
    Password: your_secure_password
    Host: localhost
    ```

### **STEP 3: Import Database via phpMyAdmin**

1. **Open phpMyAdmin** from cPanel
2. **Select Your Database** (left sidebar)
3. **Go to "Import" Tab**
4. **Choose SQL File:**
    - Click "Choose File"
    - Select `database/sql/fresh_install.sql` from your computer
    - **For production**: Use `fresh_install.sql` (no sample data)
    - **For testing**: Use `complete_mysql.sql` (includes sample data)
5. **Import Settings:**
    - Format: SQL
    - Character set: utf8
    - Leave other settings as default
6. **Click "Go"** to import
7. **Verify Import:**
    - Check for "Import has been successfully finished" message
    - You should see ~60+ tables in your database

### **STEP 4: Upload Application Files**

#### Option A: File Manager Upload (Recommended)

1. **Create ZIP file** of your Sellora project:

    ```bash
    # Exclude unnecessary files
    zip -r sellora.zip . -x "node_modules/*" ".git/*" "*.log"
    ```

2. **Upload via cPanel File Manager:**
    - Go to "File Manager" in cPanel
    - Navigate to `public_html/`
    - Click "Upload" and select your ZIP file
    - **Extract** the ZIP file
    - **Rename** the extracted folder to `sellora`

#### Option B: FTP Upload

1. **Use FTP client** (FileZilla, WinSCP, etc.)
2. **Upload entire project** to `public_html/sellora/`

### **STEP 5: Configure Environment File**

1. **Navigate** to your uploaded files in File Manager
2. **Copy** `.env.production` to `.env`:
    - Right-click `.env.production` → Copy
    - Paste and rename to `.env`
3. **Edit** `.env` file with your database credentials:

    ```env
    APP_NAME=Sellora
    APP_ENV=production
    APP_KEY=base64:N3SO1xVdR+EGwU/gbTMzsChE7LwJWoql94GEc1jyxV0=
    APP_DEBUG=false
    APP_URL=https://yourdomain.com

    # Your cPanel Database Credentials
    DB_CONNECTION=mysql
    DB_HOST=localhost
    DB_PORT=3306
    DB_DATABASE=your_cpanel_prefix_sellora_db
    DB_USERNAME=your_cpanel_prefix_sellora_user
    DB_PASSWORD=your_secure_password

    # Manual Database Setup (IMPORTANT!)
    MANUAL_DATABASE_SETUP=true
    SKIP_MIGRATION_CHECK=true

    # Production Mail Settings (configure with your email)
    MAIL_MAILER=smtp
    MAIL_HOST=mail.yourdomain.com
    MAIL_PORT=587
    MAIL_USERNAME=noreply@yourdomain.com
    MAIL_PASSWORD=your_email_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=noreply@yourdomain.com
    ```

### **STEP 6: Setup Directory Structure**

You have two options for serving your application:

#### Option A: Subdirectory Access (Easier)

-   **Keep** Laravel in `public_html/sellora/`
-   **Access** via: `https://yourdomain.com/sellora/public`
-   **No file moving required**

#### Option B: Root Domain Access (Recommended)

1. **Move Laravel app** to `public_html/sellora/`
2. **Move `public/` contents** to `public_html/`:
    ```
    public_html/
    ├── sellora/          (Laravel app files)
    ├── index.php         (moved from public/)
    ├── .htaccess         (moved from public/)
    └── assets/           (moved from public/)
    ```
3. **Update `public_html/index.php`**:

    ```php
    <?php
    define('LARAVEL_START', microtime(true));

    if (file_exists($maintenance = __DIR__.'/sellora/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__.'/sellora/vendor/autoload.php';

    $app = require_once __DIR__.'/sellora/bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();

    $kernel->terminate($request, $response);
    ```

4. **Access** via: `https://yourdomain.com`

### **STEP 7: Set File Permissions**

Using cPanel File Manager:

1. **Select `storage/` folder** → Right-click → Permissions → Set to **755**
2. **Select `bootstrap/cache/` folder** → Right-click → Permissions → Set to **755**
3. **Select `.env` file** → Right-click → Permissions → Set to **644**

### **STEP 8: Configure PHP Settings**

1. **Go to "Select PHP Version"** in cPanel
2. **Set PHP Version** to 8.2 or higher
3. **Enable Extensions** (click "Extensions" tab):

    - ✅ mysqli
    - ✅ pdo_mysql
    - ✅ mbstring
    - ✅ openssl
    - ✅ tokenizer
    - ✅ xml
    - ✅ ctype
    - ✅ json
    - ✅ bcmath
    - ✅ fileinfo
    - ✅ zip

4. **Update PHP Options** (click "Options" tab):
    ```
    memory_limit = 256M
    max_execution_time = 300
    max_input_vars = 3000
    post_max_size = 50M
    upload_max_filesize = 50M
    ```

### **STEP 9: Create Storage Symbolic Link**

#### Via SSH (if available):

```bash
cd /path/to/your/laravel/app
php artisan storage:link
```

#### Manual Method (File Manager):

1. **Go to** `public_html/` (or your Laravel public directory)
2. **Create symlink** for storage:
    - Contact your hosting provider to create symlink, OR
    - Some hosts allow symlinks via File Manager

### **STEP 10: Final Verification**

1. **Test Database Connection:**

    - Create a test PHP file in your public directory:

    ```php
    <?php
    // test-db.php
    $host = 'localhost';
    $dbname = 'your_cpanel_prefix_sellora_db';
    $username = 'your_cpanel_prefix_sellora_user';
    $password = 'your_secure_password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        echo "✅ Database connection successful!<br>";

        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "Users table has {$result['count']} records<br>";

        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Database has " . count($tables) . " tables<br>";

    } catch(PDOException $e) {
        echo "❌ Connection failed: " . $e->getMessage();
    }
    ?>
    ```

    - Visit `https://yourdomain.com/test-db.php`
    - **Delete this file** after testing!

2. **Test Laravel Application:**
    - Visit your domain
    - You should see the Sellora login page
    - Try logging in (if you imported sample data)

## 🔧 **Production Optimization**

### Cache Configuration (if SSH available)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Manual Cache (if no SSH)

-   Your application will work without caching
-   Performance may be slightly slower but fully functional

## 🚨 **Troubleshooting**

### **Issue 1: 500 Internal Server Error**

**Solutions:**

-   Check `.env` file exists and has correct database credentials
-   Verify file permissions (755 for directories, 644 for files)
-   Check PHP error logs in cPanel
-   Ensure all required PHP extensions are enabled

### **Issue 2: Database Connection Error**

**Solutions:**

-   Verify database credentials in `.env`
-   Check database user has ALL PRIVILEGES
-   Ensure database exists and tables were imported
-   Test connection with test script above

### **Issue 3: "Class not found" Errors**

**Solutions:**

-   Ensure `vendor/` folder was uploaded completely
-   Check `composer.json` and `composer.lock` are present
-   Verify PHP version is 8.2+

### **Issue 4: File Upload/Storage Issues**

**Solutions:**

-   Create storage symbolic link
-   Set proper permissions on `storage/` directory
-   Check disk space in cPanel

### **Issue 5: Email Not Working**

**Solutions:**

-   Configure SMTP settings in `.env`
-   Use your domain's email server settings
-   Contact hosting provider for email configuration

## 📊 **Performance Tips**

### **Database Optimization**

-   Enable MySQL query cache (contact hosting provider)
-   Monitor slow query log
-   Regular database maintenance

### **Application Optimization**

-   Enable OPcache in PHP settings
-   Use content delivery network (CDN)
-   Optimize images and assets
-   Enable gzip compression (already in .htaccess)

## 🔐 **Security Checklist**

-   [ ] Set `APP_DEBUG=false` in production
-   [ ] Use strong database passwords
-   [ ] Enable HTTPS/SSL certificate
-   [ ] Set proper file permissions
-   [ ] Regular backups
-   [ ] Keep Laravel updated
-   [ ] Monitor error logs

## 🎉 **You're Done!**

Your Sellora application should now be running on cPanel hosting with a manually imported MySQL database.

### **Next Steps:**

1. **Create your admin user** (if not using sample data)
2. **Configure application settings**
3. **Set up regular backups**
4. **Monitor application logs**
5. **Plan for updates and maintenance**

### **Login Information** (if using sample data):

-   Check the generated SQL files for default user credentials
-   Change default passwords immediately in production

---

**🎯 This manual import method ensures maximum compatibility with shared hosting environments while maintaining full control over your database structure.**
