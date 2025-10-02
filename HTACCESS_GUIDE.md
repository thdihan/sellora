# .htaccess Configuration Guide for Sellora cPanel Deployment

## 📄 **Current .htaccess Configuration**

Your Sellora application now includes an optimized `.htaccess` file located at `public/.htaccess` designed specifically for cPanel shared hosting with manual database setup.

## 🔧 **Configuration Sections**

### **1. Laravel Core Routing**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>
    RewriteEngine On
    # ... routing rules
</IfModule>
```

**Purpose:** Handles Laravel's URL routing and removes `index.php` from URLs

### **2. Security Headers**

```apache
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    # ... more headers
</IfModule>
```

**Purpose:** Adds security headers to protect against common web attacks

### **3. File Protection**

```apache
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

**Purpose:** Blocks access to sensitive files like `.env`, `composer.json`, etc.

### **4. Performance Optimization**

```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/css
    # ... compression rules
</IfModule>
```

**Purpose:** Enables gzip compression and browser caching for better performance

### **5. PHP Configuration**

```apache
<IfModule mod_php.c>
    php_value memory_limit 256M
    php_value max_execution_time 300
    # ... PHP settings
</IfModule>
```

**Purpose:** Sets PHP limits suitable for Laravel applications

## 📂 **File Placement for Different Deployment Options**

### **Option A: Subdirectory Deployment**

```
public_html/
└── sellora/
    └── public/
        └── .htaccess  ← Place here
```

**Access:** `yourdomain.com/sellora/public`

### **Option B: Root Domain Deployment (Recommended)**

```
public_html/
├── .htaccess      ← Move here from public/
├── index.php      ← Move here from public/
└── sellora/       ← Laravel app files
```

**Access:** `yourdomain.com`

## 🛡️ **Security Features**

### **Protected Files:**

-   `.env` files (environment configuration)
-   `composer.json` and `composer.lock` (dependency files)
-   `artisan` (Laravel command line tool)
-   Log files (`.log`, `.sql`, `.sqlite`)
-   Backup files (`.bak`, `.backup`, `.tmp`)

### **Security Headers:**

-   **X-Content-Type-Options:** Prevents MIME type sniffing
-   **X-Frame-Options:** Prevents clickjacking attacks
-   **X-XSS-Protection:** Enables browser XSS filtering
-   **Referrer-Policy:** Controls referrer information leakage

## ⚡ **Performance Optimizations**

### **Compression Enabled For:**

-   HTML, CSS, JavaScript
-   JSON, XML files
-   Text files

### **Browser Caching Set For:**

-   **CSS/JS:** 1 year cache
-   **Images:** 1 year cache
-   **Fonts:** 1 year cache
-   **HTML:** No cache (dynamic content)

## 🔧 **PHP Settings Configured**

```apache
memory_limit = 256M          # Increased for Laravel
max_execution_time = 300     # 5 minutes for heavy operations
max_input_vars = 3000        # For large forms
upload_max_filesize = 50M    # File upload limit
post_max_size = 50M          # POST data limit
expose_php = off             # Hide PHP version
display_errors = off         # Production error handling
```

## 🌐 **SSL/HTTPS Configuration**

The file includes commented HTTPS redirect rules:

```apache
# Uncomment these lines after SSL certificate is installed:
# <IfModule mod_rewrite.c>
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# </IfModule>
```

**To enable HTTPS redirect:**

1. Install SSL certificate in cPanel
2. Uncomment the HTTPS redirect section
3. Uncomment the HSTS header for additional security

## 📝 **Customization Options**

### **For Higher Memory Requirements:**

```apache
php_value memory_limit 512M
```

### **For Larger File Uploads:**

```apache
php_value upload_max_filesize 100M
php_value post_max_size 100M
```

### **For API Endpoints (add CORS headers):**

```apache
<IfModule mod_headers.c>
    Header always set Access-Control-Allow-Origin "*"
    Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
    Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
</IfModule>
```

## 🚨 **Troubleshooting**

### **Issue: 500 Internal Server Error**

**Solutions:**

1. Check if all modules are available on your hosting
2. Remove PHP settings section if not supported
3. Contact hosting provider about mod_rewrite support

### **Issue: CSS/JS Files Not Loading**

**Solutions:**

1. Check file paths in your HTML
2. Verify files exist in correct directories
3. Test with caching disabled

### **Issue: File Upload Errors**

**Solutions:**

1. Increase `upload_max_filesize` and `post_max_size`
2. Check hosting provider limits
3. Verify storage directory permissions

## 📋 **Hosting Provider Compatibility**

### **Tested Compatible With:**

-   ✅ cPanel shared hosting
-   ✅ Apache 2.4+
-   ✅ Most shared hosting providers
-   ✅ VPS with Apache

### **Required Apache Modules:**

-   `mod_rewrite` (for URL routing)
-   `mod_headers` (for security headers)
-   `mod_deflate` (for compression)
-   `mod_expires` (for caching)
-   `mod_php` (for PHP settings)

## 🎯 **Best Practices**

### **After Deployment:**

1. **Test thoroughly** - Check all routes work
2. **Enable HTTPS** - Install SSL certificate
3. **Monitor logs** - Check for any errors
4. **Performance test** - Verify compression and caching
5. **Security scan** - Use online security checkers

### **Regular Maintenance:**

1. **Review error logs** regularly
2. **Update security headers** as needed
3. **Optimize caching rules** based on usage
4. **Keep backup** of working .htaccess

## 📞 **Support**

If you encounter issues with the .htaccess configuration:

1. **Check hosting provider documentation** for supported modules
2. **Contact hosting support** for module availability
3. **Test with minimal configuration** and add features gradually
4. **Use server error logs** to identify specific issues

---

**The current .htaccess configuration is optimized for maximum compatibility with cPanel shared hosting while providing security and performance benefits for your Sellora Laravel application.**
