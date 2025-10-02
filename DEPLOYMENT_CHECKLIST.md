# cPanel Deployment Checklist

## 📋 **Pre-Deployment Checklist**

### **Local Preparation**

-   [ ] Generate SQL files (`php generate-mysql-schema.php` - already done)
-   [ ] Test application locally with MySQL
-   [ ] Create project ZIP file (exclude `node_modules/`, `.git/`)
-   [ ] Have `.env.production` file ready

### **cPanel Account Ready**

-   [ ] cPanel login credentials available
-   [ ] Domain/subdomain configured
-   [ ] PHP 8.2+ available
-   [ ] MySQL database quota available

## 🗄️ **Database Setup Checklist**

### **Step 1: Create Database**

-   [ ] Login to cPanel
-   [ ] Go to "MySQL Databases"
-   [ ] Create database: `your_prefix_sellora_db`
-   [ ] Create user: `your_prefix_sellora_user`
-   [ ] Set strong password
-   [ ] Grant ALL PRIVILEGES to user
-   [ ] **Note down exact names** (including prefix)

### **Step 2: Import Database**

-   [ ] Open phpMyAdmin
-   [ ] Select your database
-   [ ] Go to "Import" tab
-   [ ] Choose file: `database/sql/fresh_install.sql` (production) or `complete_mysql.sql` (development)
-   [ ] Set format: SQL
-   [ ] Set charset: utf8
-   [ ] Click "Go" to import
-   [ ] Verify success message
-   [ ] Check tables count (~60+ tables)

## 📁 **File Upload Checklist**

### **Step 3: Upload Files**

-   [ ] Go to cPanel File Manager
-   [ ] Navigate to `public_html/`
-   [ ] Upload your project ZIP file
-   [ ] Extract ZIP file
-   [ ] Rename extracted folder to `sellora`
-   [ ] Verify all files uploaded (check `vendor/` folder size)

## ⚙️ **Configuration Checklist**

### **Step 4: Environment Setup**

-   [ ] Copy `.env.production` to `.env`
-   [ ] Edit `.env` file with:
    -   [ ] `APP_URL=https://yourdomain.com`
    -   [ ] `APP_DEBUG=false`
    -   [ ] `DB_CONNECTION=mysql`
    -   [ ] `DB_HOST=localhost`
    -   [ ] `DB_DATABASE=your_exact_database_name`
    -   [ ] `DB_USERNAME=your_exact_username`
    -   [ ] `DB_PASSWORD=your_password`
    -   [ ] `MANUAL_DATABASE_SETUP=true`
    -   [ ] `SKIP_MIGRATION_CHECK=true`

### **Step 5: Directory Structure**

Choose one option:

#### **Option A: Subdirectory Access**

-   [ ] Keep Laravel in `public_html/sellora/`
-   [ ] Access via: `yourdomain.com/sellora/public`

#### **Option B: Root Domain Access (Recommended)**

-   [ ] Move `public/` contents to `public_html/`
-   [ ] Keep Laravel app in `public_html/sellora/`
-   [ ] Update `public_html/index.php` paths
-   [ ] Access via: `yourdomain.com`

## 🔧 **System Configuration Checklist**

### **Step 6: PHP Settings**

-   [ ] Go to "Select PHP Version"
-   [ ] Set PHP version to 8.2+
-   [ ] Enable required extensions:
    -   [ ] mysqli
    -   [ ] pdo_mysql
    -   [ ] mbstring
    -   [ ] openssl
    -   [ ] tokenizer
    -   [ ] xml
    -   [ ] ctype
    -   [ ] json
    -   [ ] bcmath
    -   [ ] fileinfo
    -   [ ] zip
-   [ ] Update PHP options:
    -   [ ] memory_limit = 256M
    -   [ ] max_execution_time = 300
    -   [ ] max_input_vars = 3000

### **Step 7: File Permissions**

-   [ ] Set `storage/` folder to 755
-   [ ] Set `bootstrap/cache/` folder to 755
-   [ ] Set `.env` file to 644

### **Step 8: Storage Link**

-   [ ] Create storage symbolic link (via SSH or contact host)
-   [ ] OR configure alternative storage method

## ✅ **Testing Checklist**

### **Step 9: Verification Tests**

#### **Database Connection Test**

-   [ ] Create temporary test file:

```php
<?php
$pdo = new PDO("mysql:host=localhost;dbname=your_db", "your_user", "your_pass");
echo "Database connected: " . $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() . " users";
?>
```

-   [ ] Visit test file URL
-   [ ] Should show database connection success
-   [ ] **Delete test file after verification**

#### **Application Test**

-   [ ] Visit your domain
-   [ ] Should see Sellora application (not Laravel default page)
-   [ ] No 500 errors displayed
-   [ ] Login page loads correctly

#### **Functionality Test**

-   [ ] Test login (if using sample data)
-   [ ] Test navigation menu
-   [ ] Test database operations (view products, customers, etc.)

## 🚨 **Troubleshooting Checklist**

### **If You Get Errors:**

#### **500 Internal Server Error**

-   [ ] Check `.env` file exists
-   [ ] Verify file permissions
-   [ ] Check PHP error logs in cPanel
-   [ ] Ensure all PHP extensions enabled

#### **Database Connection Error**

-   [ ] Double-check database credentials in `.env`
-   [ ] Verify database user has ALL PRIVILEGES
-   [ ] Confirm database name includes cPanel prefix
-   [ ] Test with database connection script

#### **Class Not Found Errors**

-   [ ] Verify `vendor/` folder uploaded completely
-   [ ] Check PHP version is 8.2+
-   [ ] Ensure `composer.json` exists

#### **File Not Found Errors**

-   [ ] Check directory structure matches chosen option
-   [ ] Verify `index.php` paths are correct
-   [ ] Ensure all files extracted properly

## 🎉 **Post-Deployment Checklist**

### **Step 10: Final Steps**

-   [ ] Change default passwords (if using sample data)
-   [ ] Configure email settings
-   [ ] Set up SSL certificate
-   [ ] Configure backup strategy
-   [ ] Monitor error logs
-   [ ] Document your configuration

### **Security Checklist**

-   [ ] Remove any test files
-   [ ] Ensure `APP_DEBUG=false`
-   [ ] Use strong passwords everywhere
-   [ ] Keep Laravel updated
-   [ ] Regular security monitoring

### **Performance Checklist**

-   [ ] Enable OPcache (contact hosting provider)
-   [ ] Monitor database performance
-   [ ] Optimize images and assets
-   [ ] Consider CDN setup

## 📞 **Emergency Contacts**

### **If Deployment Fails:**

1. **Hosting Provider Support** - for server issues
2. **cPanel Documentation** - for control panel issues
3. **Laravel Documentation** - for application issues

### **Common Support Questions:**

-   "How to create symbolic links?"
-   "How to enable PHP extensions?"
-   "How to increase PHP memory limit?"
-   "How to set up SSL certificate?"

---

## 🎯 **Success Criteria**

Your deployment is successful when:

-   ✅ Domain loads Sellora application
-   ✅ No PHP errors displayed
-   ✅ Database connection working
-   ✅ Navigation menu functional
-   ✅ Can access admin features

**Estimated deployment time: 30-60 minutes for first-time deployment**

---

**Print this checklist and check off each item as you complete it for a smooth deployment process!**
