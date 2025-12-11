# Fix: Website Taking Too Long to Load

## Most Common Cause: Database Connection Timeout

When the website hangs or takes forever to load, it's usually trying to connect to the database with **wrong credentials** and timing out.

---

## Quick Fix: Check Database Configuration

### Step 1: Verify includes/db_connect.php

Your collaborator's `includes/db_connect.php` should have:

```php
<?php
if (!defined("SECURE_ACCESS")) {
    die("Access denied.");
}

$servername = "localhost";
$username = "root";
$password = "";  // EMPTY - no password
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

**Important**: Password should be **empty** `""` for default XAMPP.

### Step 2: Test Database Connection

Go to: `http://localhost/Online_Phones_Store/test_connection.php`

**If it loads slowly or times out** → Database connection issue
**If it shows errors immediately** → Wrong credentials
**If it loads fast with green checkmarks** → Database is fine

---

## Other Common Causes

### 1. MySQL Not Running

**Check XAMPP Control Panel:**
- MySQL should show **green** and say "Running"
- If it's red or stopped, click "Start"

### 2. Wrong Database Name

Make sure the database `ecommerce_db` exists:
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Look for `ecommerce_db` in left sidebar
3. If missing, import `database/fresh_setup.sql`

### 3. External Images Loading Slowly

The products use images from Unsplash (external URLs). If internet is slow, images take time to load.

**Quick test**: Disable images temporarily
1. Open `index.php`
2. Comment out the image section (lines 75-85)
3. Reload page

If it loads fast now → Internet/image issue
If still slow → Database issue

### 4. PHP Timeout Settings

Check PHP timeout in `C:\xampp\php\php.ini`:
```ini
max_execution_time = 30
```

If it's very low (like 5), increase it to 30 or 60.

---

## Diagnostic Steps

### Test 1: Check Apache
Go to: `http://localhost/`

**Fast load** → Apache is fine
**Slow/timeout** → Apache issue

### Test 2: Check Database Connection
Go to: `http://localhost/Online_Phones_Store/test_connection.php`

**Fast with errors** → Wrong credentials
**Slow/timeout** → MySQL not running or wrong config
**Fast with green checks** → Database is working

### Test 3: Check Simple PHP
Create `test.php` in project root:
```php
<?php
echo "PHP is working!";
phpinfo();
?>
```

Go to: `http://localhost/Online_Phones_Store/test.php`

**Fast load** → PHP is fine
**Slow** → PHP configuration issue

---

## Step-by-Step Troubleshooting

### For Your Collaborator:

1. **Verify MySQL is running** (green in XAMPP)

2. **Check db_connect.php has correct settings**:
   - Username: `root`
   - Password: `""` (empty)
   - Database: `ecommerce_db`

3. **Test connection**: `http://localhost/Online_Phones_Store/test_connection.php`

4. **If test page also loads slowly**:
   - MySQL is not running, OR
   - Wrong database credentials, OR
   - Database doesn't exist

5. **Fix**:
   - Start MySQL in XAMPP
   - Import `database/fresh_setup.sql`
   - Update `db_connect.php` with correct credentials

---

## Common Mistakes

### ❌ Wrong Password
```php
$password = "1234";  // WRONG for default XAMPP
```

### ✅ Correct Password
```php
$password = "";  // CORRECT - empty string
```

### ❌ Wrong Server
```php
$servername = "localhost:3307";  // Only if you changed port
```

### ✅ Correct Server
```php
$servername = "localhost";  // Default
```

---

## If Everything Else Fails

### Clear Browser Cache
1. Press `Ctrl + Shift + Delete`
2. Clear cache and cookies
3. Reload page

### Restart Everything
1. Stop Apache and MySQL in XAMPP
2. Close XAMPP
3. Restart computer
4. Open XAMPP as Administrator
5. Start Apache and MySQL
6. Try again

### Check Error Logs

**PHP Error Log**: `C:\xampp\php\logs\php_error_log`
**Apache Error Log**: `C:\xampp\apache\logs\error.log`
**MySQL Error Log**: `C:\xampp\mysql\data\mysql_error.log`

Look for recent errors that might explain the slowness.

---

## Expected Load Times

**Normal performance:**
- Test page: 1-2 seconds
- Main page: 2-3 seconds (with images)
- Admin page: 1-2 seconds

**If taking longer than 10 seconds** → Something is wrong

---

## Quick Checklist

- [ ] MySQL is running (green in XAMPP)
- [ ] Database `ecommerce_db` exists in phpMyAdmin
- [ ] `db_connect.php` has username: `root`, password: `""`
- [ ] Test connection page loads quickly
- [ ] Apache is running (green in XAMPP)
- [ ] Project is in `C:\xampp\htdocs\Online_Phones_Store`

If all checked and still slow, share the error logs for more help.
