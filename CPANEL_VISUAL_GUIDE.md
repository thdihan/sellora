# cPanel Database Import - Visual Guide

## 📸 **Step-by-Step Screenshots Guide**

### **Step 1: Create MySQL Database**

#### 1.1 Find MySQL Databases

```
cPanel Dashboard → Databases Section → MySQL Databases
```

#### 1.2 Create Database

```
Create New Database:
┌─────────────────────────────────────┐
│ New Database: [sellora_db        ] │
│ [Create Database]                   │
└─────────────────────────────────────┘
```

#### 1.3 Create User

```
Add New User:
┌─────────────────────────────────────┐
│ Username: [sellora_user          ] │
│ Password: [******************     ] │
│ Password (Again): [**************] │
│ [Create User]                       │
└─────────────────────────────────────┘
```

#### 1.4 Add User to Database

```
Add User To Database:
┌─────────────────────────────────────┐
│ User: [your_user] Database: [your_db]│
│ Privileges: [✓] ALL PRIVILEGES      │
│ [Make Changes]                      │
└─────────────────────────────────────┘
```

### **Step 2: Import via phpMyAdmin**

#### 2.1 Open phpMyAdmin

```
cPanel Dashboard → Databases Section → phpMyAdmin
```

#### 2.2 Select Database

```
phpMyAdmin Left Sidebar:
┌─────────────────────┐
│ ► your_account_    │
│   ► information_sc  │
│   ► performance_sc  │
│   ► mysql          │
│ ► your_account_sell │ ← Click this
│   ► sys            │
└─────────────────────┘
```

#### 2.3 Import Tab

```
phpMyAdmin Top Menu:
┌─────────────────────────────────────────┐
│ Structure | SQL | Search | Insert | Import │ ← Click Import
└─────────────────────────────────────────┘
```

#### 2.4 Import Settings

```
Import Interface:
┌─────────────────────────────────────────┐
│ File to import:                         │
│ [Choose File] fresh_install.sql         │
│                                         │
│ Format: SQL ▼                          │
│ Character set: utf8 ▼                  │
│                                         │
│ [Go] ← Click to import                  │
└─────────────────────────────────────────┘
```

#### 2.5 Success Message

```
✅ Import has been successfully finished.
62 queries executed.
```

### **Step 3: File Upload Process**

#### 3.1 cPanel File Manager

```
cPanel Dashboard → Files Section → File Manager
```

#### 3.2 Upload ZIP

```
File Manager:
┌─────────────────────────────────────────┐
│ public_html/ ← Navigate here            │
│ ┌─────────────────────────────────────┐ │
│ │ [Upload] [New File] [New Folder]   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Upload Interface:                       │
│ [Select Files] sellora.zip              │
│ [Upload Files]                          │
└─────────────────────────────────────────┘
```

#### 3.3 Extract Files

```
Right-click sellora.zip:
┌─────────────────────┐
│ Extract            │ ← Click this
│ Copy               │
│ Move               │
│ Delete             │
│ Properties         │
└─────────────────────┘
```

### **Step 4: Environment Configuration**

#### 4.1 Copy .env.production

```
File Manager:
.env.production → Right-click → Copy
Right-click in empty space → Paste
Rename copied file to: .env
```

#### 4.2 Edit .env File

```
Right-click .env → Edit

Critical Settings:
┌─────────────────────────────────────────┐
│ DB_CONNECTION=mysql                     │
│ DB_HOST=localhost                       │
│ DB_DATABASE=your_cpanel_prefix_sellora_db│
│ DB_USERNAME=your_cpanel_prefix_sellora_user│
│ DB_PASSWORD=your_secure_password        │
│                                         │
│ MANUAL_DATABASE_SETUP=true              │
│ SKIP_MIGRATION_CHECK=true               │
└─────────────────────────────────────────┘
```

### **Step 5: Directory Structure Options**

#### Option A: Subdirectory (Simple)

```
public_html/
├── sellora/               ← Laravel app here
│   ├── public/           ← Access via /sellora/public
│   └── ...
└── other files
```

#### Option B: Root Domain (Recommended)

```
public_html/
├── sellora/               ← Laravel app (except public)
│   ├── app/
│   ├── config/
│   └── ...
├── index.php             ← Moved from public/
├── .htaccess             ← Moved from public/
└── assets/               ← Moved from public/
```

### **Step 6: File Permissions**

#### Set Permissions via File Manager

```
Select folder/file → Right-click → Permissions

storage/ → 755 (rwxr-xr-x)
┌─────────────────────────────────────────┐
│ Owner: [✓]Read [✓]Write [✓]Execute     │
│ Group: [✓]Read [ ]Write [✓]Execute     │
│ World: [✓]Read [ ]Write [✓]Execute     │
│                                         │
│ Numeric: 755                            │
│ [Change Permissions]                    │
└─────────────────────────────────────────┘

.env → 644 (rw-r--r--)
┌─────────────────────────────────────────┐
│ Owner: [✓]Read [✓]Write [ ]Execute     │
│ Group: [✓]Read [ ]Write [ ]Execute     │
│ World: [✓]Read [ ]Write [ ]Execute     │
│                                         │
│ Numeric: 644                            │
│ [Change Permissions]                    │
└─────────────────────────────────────────┘
```

## 🔍 **Verification Checklist**

### ✅ **Database Import Verification**

-   [ ] phpMyAdmin shows ~60+ tables
-   [ ] No import errors displayed
-   [ ] Tables have proper structure

### ✅ **File Upload Verification**

-   [ ] All Laravel files uploaded
-   [ ] vendor/ folder present and complete
-   [ ] .env file configured with correct credentials

### ✅ **Permissions Verification**

-   [ ] storage/ folder: 755
-   [ ] bootstrap/cache/ folder: 755
-   [ ] .env file: 644

### ✅ **PHP Configuration Verification**

-   [ ] PHP version 8.2+
-   [ ] Required extensions enabled
-   [ ] Memory limit 256M+

### ✅ **Application Verification**

-   [ ] Visit domain shows Laravel application
-   [ ] No 500 errors
-   [ ] Database connection working

## 🚨 **Common Error Messages & Solutions**

### **"500 Internal Server Error"**

```
Check:
1. .env file exists and is readable
2. File permissions are correct
3. PHP extensions are enabled
4. Check error logs in cPanel
```

### **"Database connection refused"**

```
Check:
1. Database credentials in .env
2. Database user has ALL PRIVILEGES
3. Database exists and tables imported
4. Host is 'localhost' not '127.0.0.1'
```

### **"Class not found"**

```
Check:
1. vendor/ folder uploaded completely
2. composer.json exists
3. PHP version is 8.2+
```

### **"Storage link not found"**

```
Solutions:
1. Create symlink via SSH: php artisan storage:link
2. Contact hosting provider to create symlink
3. Use alternative file storage configuration
```

## 📞 **Getting Help**

### **Check These First:**

1. **Error Logs** in cPanel
2. **Laravel Logs** in `storage/logs/laravel.log`
3. **PHP Error Logs** in cPanel

### **Contact Points:**

1. **Hosting Provider** - for server configuration issues
2. **cPanel Documentation** - for cPanel-specific questions
3. **Laravel Documentation** - for application issues

---

**🎯 This visual guide ensures you can successfully deploy Sellora using manual database import even without technical expertise.**
