# cPanel Deployment Troubleshooting Guide

## 🚨 **Common Issues & Quick Fixes**

### **Issue #1: 500 Internal Server Error**

#### **Symptoms:**

-   White page with "Internal Server Error"
-   "The website is temporarily unable to service your request"

#### **Solutions:**

1. **Check .env file exists and is readable**

    ```
    File Manager → Check .env file is present
    Right-click .env → Permissions → Set to 644
    ```

2. **Verify PHP extensions**

    ```
    cPanel → Select PHP Version → Extensions
    Enable: mysqli, pdo_mysql, mbstring, openssl, tokenizer, xml
    ```

3. **Check error logs**

    ```
    cPanel → Error Logs → View recent errors
    Look for specific PHP errors
    ```

4. **File permissions**
    ```
    storage/ → 755
    bootstrap/cache/ → 755
    .env → 644
    ```

---

### **Issue #2: Database Connection Refused**

#### **Symptoms:**

-   "Connection refused"
-   "Access denied for user"
-   "Unknown database"

#### **Solutions:**

1. **Verify database credentials**

    ```
    cPanel → MySQL Databases → Check exact names

    Common mistake: Missing cPanel prefix
    Wrong: DB_DATABASE=sellora_db
    Right: DB_DATABASE=your_account_sellora_db
    ```

2. **Check user privileges**

    ```
    cPanel → MySQL Databases → Current Users section
    Ensure user has "ALL PRIVILEGES" on database
    ```

3. **Test connection manually**

    ```php
    // Create test-connection.php
    <?php
    $host = 'localhost';
    $db = 'your_exact_database_name_with_prefix';
    $user = 'your_exact_username_with_prefix';
    $pass = 'your_password';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        echo "✅ Connection successful!";

        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        echo "<br>Tables found: " . count($tables);

    } catch(PDOException $e) {
        echo "❌ Error: " . $e->getMessage();
    }
    ?>
    ```

---

### **Issue #3: "Class Not Found" Errors**

#### **Symptoms:**

-   "Class 'App\Something' not found"
-   "Interface not found"
-   Blank white pages

#### **Solutions:**

1. **Check vendor folder**

    ```
    File Manager → Check vendor/ folder exists and has files
    vendor/ folder should be ~50-100MB in size
    ```

2. **PHP version compatibility**

    ```
    cPanel → Select PHP Version → Set to 8.2 or higher
    ```

3. **Re-upload vendor folder**
    ```
    Delete existing vendor/ folder
    Upload fresh vendor/ folder from working local installation
    ```

---

### **Issue #4: "Page Not Found" or Routes Not Working**

#### **Symptoms:**

-   Homepage loads but other pages show 404
-   Routes not working properly

#### **Solutions:**

1. **Check .htaccess file**

    ```
    Ensure .htaccess file exists in public directory
    Content should include:

    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
    ```

2. **Verify directory structure**

    ```
    Option A (Subdirectory):
    public_html/sellora/public/ ← .htaccess here

    Option B (Root domain):
    public_html/.htaccess ← Should be here
    ```

3. **Check Apache mod_rewrite**
    ```
    Contact hosting provider to enable mod_rewrite module
    ```

---

### **Issue #5: "Storage Link Not Found"**

#### **Symptoms:**

-   Images/files not displaying
-   "Storage not found" errors
-   File upload issues

#### **Solutions:**

1. **Create storage link via SSH**

    ```bash
    php artisan storage:link
    ```

2. **Manual symlink creation**

    ```
    Contact hosting provider to create symlink:
    From: /path/to/storage/app/public
    To: /path/to/public/storage
    ```

3. **Alternative solution**
    ```
    Copy storage/app/public/ contents to public/storage/
    (Not ideal but works as temporary fix)
    ```

---

### **Issue #6: Email Not Working**

#### **Symptoms:**

-   Password reset emails not sent
-   System notifications not working

#### **Solutions:**

1. **Configure SMTP in .env**

    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=mail.yourdomain.com
    MAIL_PORT=587
    MAIL_USERNAME=noreply@yourdomain.com
    MAIL_PASSWORD=your_email_password
    MAIL_ENCRYPTION=tls
    ```

2. **Contact hosting provider**
    ```
    Ask for SMTP server settings
    Some hosts block external SMTP
    ```

---

### **Issue #7: "Memory Limit Exceeded"**

#### **Symptoms:**

-   "Fatal error: Allowed memory size exhausted"
-   Slow performance

#### **Solutions:**

1. **Increase PHP memory limit**

    ```
    cPanel → Select PHP Version → Options
    memory_limit = 256M (or higher)
    ```

2. **Contact hosting provider**
    ```
    Request higher memory limits
    Upgrade to higher hosting plan if needed
    ```

---

### **Issue #8: File Upload Errors**

#### **Symptoms:**

-   "File too large" errors
-   Upload forms not working

#### **Solutions:**

1. **Increase upload limits**

    ```
    cPanel → Select PHP Version → Options
    upload_max_filesize = 50M
    post_max_size = 50M
    max_input_vars = 3000
    ```

2. **Check file permissions**
    ```
    storage/app/ → 755
    storage/logs/ → 755
    ```

---

### **Issue #9: SQL Import Errors**

#### **Symptoms:**

-   "Invalid default value" errors during import
-   "#1067 - Invalid default value for column"
-   SQL syntax errors in phpMyAdmin

#### **Solutions:**

1. **Use the corrected SQL files**

    ```
    ✅ Use fresh_install.sql (latest version with fixes)
    ✅ All DEFAULT '''value''' syntax has been corrected
    ✅ Numeric defaults no longer have quotes
    ```

2. **If using older SQL files, manually fix syntax**

    ```sql
    -- Wrong (old files):
    DEFAULT '''0'''

    -- Correct (new files):
    DEFAULT 0
    ```

3. **Test SQL files before import**

    ```
    Upload validate-sql.php to your hosting
    Run it via browser to check SQL syntax
    ```

4. **Import order**
    ```
    1. Import fresh_install.sql (schema only)
    2. Or import complete_mysql.sql (schema + data)
    3. Do NOT import both files
    ```

#### **Common SQL Errors & Fixes:**

**Error: "#1054 - Unknown column 'display_name'"**

```sql
-- Problem: INSERT referencing missing column
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`) VALUES ...

-- Solution: Remove non-existent column
INSERT INTO `roles` (`id`, `name`, `description`) VALUES ...
```

**Error: "#1067 - Invalid default value"**

```sql
-- Problem: Quoted numeric defaults
`spent_amount` DECIMAL(10,2) DEFAULT '''0'''

-- Solution: Unquoted numeric defaults
`spent_amount` DECIMAL(10,2) DEFAULT 0
```

---

## 🔍 **Diagnostic Tools**

### **Quick Health Check Script**

Create `health-check.php` in your public directory:

```php
<?php
echo "<h2>Sellora Health Check</h2>";

// PHP Version
echo "PHP Version: " . phpversion() . "<br>";

// Extensions
$required = ['mysqli', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer'];
foreach($required as $ext) {
    echo "Extension $ext: " . (extension_loaded($ext) ? '✅' : '❌') . "<br>";
}

// Memory Limit
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";

// Database Connection
try {
    // Update with your credentials
    $pdo = new PDO("mysql:host=localhost;dbname=your_db", "your_user", "your_pass");
    echo "Database: ✅ Connected<br>";

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Users table: " . $result['count'] . " records<br>";

} catch(Exception $e) {
    echo "Database: ❌ " . $e->getMessage() . "<br>";
}

// File Permissions
$paths = ['storage', 'bootstrap/cache'];
foreach($paths as $path) {
    if(is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "Permissions $path: $perms " . ($perms >= '0755' ? '✅' : '❌') . "<br>";
    }
}

echo "<br><strong>Delete this file after checking!</strong>";
?>
```

---

## 📞 **Getting Help**

### **Step 1: Check Logs**

1. **cPanel Error Logs** - Most important
2. **Laravel Logs** - `storage/logs/laravel.log`
3. **PHP Error Logs** - cPanel → Error Logs

### **Step 2: Common Questions for Hosting Support**

-   "Can you enable mod_rewrite for my domain?"
-   "What are my SMTP server settings?"
-   "Can you create a symbolic link for storage?"
-   "Can you increase my PHP memory limit?"
-   "What PHP extensions are available?"

### **Step 3: Information to Provide**

-   Your cPanel username
-   Domain name
-   Exact error messages
-   Steps that led to the error
-   PHP version being used

---

## ✅ **Prevention Tips**

### **Before Deployment:**

-   Test everything locally with MySQL first
-   Verify all required files are included in upload
-   Double-check database credentials
-   Have hosting provider contact info ready

### **After Deployment:**

-   Keep regular backups
-   Monitor error logs weekly
-   Test functionality regularly
-   Keep Laravel updated

---

**Remember: Most deployment issues are configuration problems, not code problems. Take your time with each step and verify everything twice!**
