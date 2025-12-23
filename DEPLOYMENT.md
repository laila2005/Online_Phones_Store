# TechHub Electronics - Deployment Guide

## 📋 Pre-Deployment Checklist

### 1. Database Configuration
- [ ] Export your MySQL database from phpMyAdmin
- [ ] Save the SQL file (includes tables: users, products, orders, categories, brands, etc.)
- [ ] Note down all table structures and relationships

### 2. File Preparation
- [ ] Update `includes/db_connect.php` with production database credentials
- [ ] Remove any debug/development code
- [ ] Verify all file paths are relative (not absolute localhost paths)
- [ ] Check that all image URLs are correct

### 3. Security Review
- [ ] Change database password to a strong password
- [ ] Ensure SECURE_ACCESS checks are in place
- [ ] Verify session security settings
- [ ] Check file upload restrictions (if any)

---

## 🚀 Recommended Hosting: 000webhost

### Why 000webhost?
- ✅ Truly free with no time limit
- ✅ PHP 8.x and MySQL support
- ✅ **Real cPanel access** (easy to use)
- ✅ Free SSL certificate
- ✅ 300 MB storage, 3 GB bandwidth
- ✅ phpMyAdmin included

### Step-by-Step Deployment

#### Step 1: Sign Up
1. Go to https://www.000webhost.com
2. Click "Free Sign Up"
3. Enter email and password
4. Verify your email

#### Step 2: Create Website
1. Click "Build Website"
2. Choose "Empty Website"
3. Pick subdomain name (e.g., `techhub-electronics`)
4. Complete setup

#### Step 3: Access Control Panel
1. Click on your website in dashboard
2. You'll see File Manager, Database Manager, phpMyAdmin, etc.

#### Step 4: Upload Files
1. Open **File Manager**
2. Navigate to `public_html` folder (this is your public root)
3. Upload all your PHP files:
   - All `.php` files
   - `assets/` folder (CSS, JS, images)
   - `includes/` folder
   - `.htaccess` file
4. You can use:
   - **Upload** button (for individual files)
   - **ZIP upload** (compress your project, upload, then extract)

#### Step 5: Create MySQL Database
1. Go to **Database Manager**
2. Click **"New Database"**
3. Create database:
   - Database name: `techhub_db`
   - Username: `techhub_user`
   - Password: (create strong password)
4. Click **"Create"**
5. Note down the full credentials (host adds prefix like `id12345_`)

#### Step 6: Import Database
1. Click **"Manage"** on your database (opens phpMyAdmin)
2. Select your database from left sidebar
3. Click **Import** tab
4. Choose your SQL file
5. Click **Go** to import

#### Step 7: Update Database Connection
1. In File Manager, edit `includes/db_connect.php`
2. Update credentials:
```php
$servername = "localhost";
$username = "id12345_techhub_user";  // Your actual username with prefix
$password = "your_strong_password";
$dbname = "id12345_techhub_db";      // Your actual database name with prefix
```
3. Save the file

#### Step 8: Test Your Site
1. Visit your site: `https://your-subdomain.000webhostapp.com`
2. Test key features:
   - Homepage loads
   - Products display
   - User registration/login
   - Add to cart
   - Checkout process
   - Profile page

---

## 🔧 Alternative: InfinityFree (No cPanel, but Unlimited)

### Why InfinityFree?
- ✅ **Unlimited** storage and bandwidth
- ✅ **No ads** on your website
- ✅ PHP 8.x and MySQL
- ⚠️ Uses vPanel (not cPanel) - different interface

### Quick Steps for InfinityFree

1. **Sign Up**: https://infinityfree.net
2. **Create Account**: Choose subdomain
3. **Access vPanel**: Their custom control panel
4. **Upload Files**: Navigate to `htdocs` folder
5. **Create Database**: Via MySQL Databases section
6. **Import SQL**: Via phpMyAdmin
7. **Update Config**: Edit `db_connect.php`

**Note**: Interface is different but functionality is similar.

---

## 📝 Important Configuration Files

### Database Connection (`includes/db_connect.php`)
```php
<?php
if (!defined("SECURE_ACCESS")) {
    die("Direct access not permitted");
}

$servername = "localhost";
$username = "YOUR_DB_USERNAME";
$password = "YOUR_DB_PASSWORD";
$dbname = "YOUR_DB_NAME";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

### Apache Configuration (`.htaccess`)
Already created in your project root - includes:
- Security headers
- File protection
- Compression
- Caching rules

---

## 🔒 Security Best Practices

### Before Going Live:
1. **Strong Passwords**: Use complex database passwords
2. **Remove Debug Code**: No `var_dump()`, `print_r()`, etc.
3. **Error Reporting**: Set to production mode
4. **File Permissions**: 
   - Folders: 755
   - Files: 644
5. **SSL Certificate**: Enable HTTPS (most free hosts provide free SSL)
6. **Backup**: Keep local backup of database and files

---

## 🐛 Common Issues & Solutions

### Issue: Database Connection Failed
**Solution**: 
- Check database credentials in `db_connect.php`
- Verify database user has correct privileges
- Confirm database name includes host prefix

### Issue: Images Not Loading
**Solution**:
- Check image paths are relative, not absolute
- Verify `assets/` folder uploaded correctly
- Check file permissions

### Issue: Sessions Not Working
**Solution**:
- Ensure `session_start()` is called
- Check PHP session settings on host
- Verify session folder has write permissions

### Issue: 404 Errors
**Solution**:
- Check `.htaccess` file uploaded
- Verify file names match exactly (case-sensitive)
- Confirm files are in `htdocs` folder

---

## 📊 Database Export Instructions

### From phpMyAdmin (Local):
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Select `online_phones_store` database
3. Click **Export** tab
4. Choose **Quick** export method
5. Format: **SQL**
6. Click **Go**
7. Save the `.sql` file

---

## 🌐 Custom Domain (Optional)

### To Use Your Own Domain:
1. Purchase domain from Namecheap, GoDaddy, etc.
2. In domain registrar, update nameservers to your host's nameservers
3. In hosting cPanel, add domain via **Addon Domains**
4. Wait 24-48 hours for DNS propagation

---

## 📞 Support Resources

- **InfinityFree Forum**: https://forum.infinityfree.net
- **000webhost Support**: https://www.000webhost.com/forum
- **PHP Documentation**: https://www.php.net/docs.php
- **MySQL Documentation**: https://dev.mysql.com/doc/

---

## ✅ Post-Deployment Checklist

- [ ] Site loads without errors
- [ ] All pages accessible
- [ ] Database connection working
- [ ] User registration works
- [ ] Login/logout functional
- [ ] Products display correctly
- [ ] Cart functionality works
- [ ] Checkout process completes
- [ ] Order confirmation sent
- [ ] Admin panel accessible (if applicable)
- [ ] SSL certificate active (HTTPS)
- [ ] Mobile responsive design working

---

## 🎉 Your Site is Live!

**TechHub Electronics** is now deployed and ready for customers!

### Next Steps:
1. Test all functionality thoroughly
2. Add real product data
3. Configure email settings for order confirmations
4. Set up Google Analytics (optional)
5. Submit to search engines
6. Share with users!

---

**Need Help?** Check the hosting provider's documentation or community forums.
