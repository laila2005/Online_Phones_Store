# Collaborator MySQL Setup Guide

This project has been configured with separate MySQL users for each team member.

## Team Members & MySQL Credentials

### 1. **Root User** (Project Owner)
- **MySQL Username**: `root`
- **MySQL Password**: *(no password)*
- **Database**: `ecommerce_db`

### 2. **Judy**
- **MySQL Username**: `judy`
- **MySQL Password**: `1234`
- **Database**: `ecommerce_db`

### 3. **Habiba**
- **MySQL Username**: `habiba`
- **MySQL Password**: `1234`
- **Database**: `ecommerce_db`

---

## Setup Instructions for Each Collaborator

### Step 1: Import the Database

1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Click "Import" tab
4. Select `database/schema.sql` from the project folder
5. Click "Go"

### Step 2: Create Your MySQL User

Each collaborator needs to create their MySQL user account:

#### For Judy:
```sql
CREATE USER 'judy'@'localhost' IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON ecommerce_db.* TO 'judy'@'localhost';
```

#### For Habiba:
```sql
CREATE USER 'habiba'@'localhost' IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON ecommerce_db.* TO 'habiba'@'localhost';
```

**How to run these commands:**
1. Open phpMyAdmin
2. Click on "SQL" tab
3. Paste the commands for your user
4. Click "Go"

### Step 3: Update Your Local Configuration

Each person needs to update their local `includes/db_connect.php` file with their own credentials.

#### Configuration for Root (Project Owner):
```php
<?php
if (!defined("SECURE_ACCESS")) {
    die("Access denied.");
}

$servername = "localhost";
$username = "root";
$password = ""; // No password
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

#### Configuration for Judy:
```php
<?php
if (!defined("SECURE_ACCESS")) {
    die("Access denied.");
}

$servername = "localhost";
$username = "judy";
$password = "1234";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

#### Configuration for Habiba:
```php
<?php
if (!defined("SECURE_ACCESS")) {
    die("Access denied.");
}

$servername = "localhost";
$username = "habiba";
$password = "1234";
$dbname = "ecommerce_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
```

---

## Important Notes

### Git Configuration
The `includes/db_connect.php` file is **NOT tracked by Git** (it's in `.gitignore`), so:
- ✅ Each person can have different credentials locally
- ✅ No one's password will be committed to the repository
- ✅ Each collaborator configures their own local setup

### Testing Your Setup

After updating your config file:

1. **Test the connection**:
   - Visit: `http://localhost/Online_Phones_Store/test_connection.php`
   - All tests should pass ✅

2. **View the website**:
   - Visit: `http://localhost/Online_Phones_Store/`
   - You should see products displayed

3. **Test admin panel**:
   - Visit: `http://localhost/Online_Phones_Store/admin/login.php`
   - Login: `admin` / `1234`

### Troubleshooting

**"Access denied" error:**
- Make sure you created your MySQL user (Step 2)
- Verify your username/password in `includes/db_connect.php`
- Check that MySQL is running in XAMPP

**"Database not found":**
- Re-import `database/schema.sql` via phpMyAdmin

**Need help?**
- See `TROUBLESHOOTING_MYSQL.md` for detailed solutions
- Contact the project owner

---

## Security Reminder

⚠️ **These are development credentials only!**
- Never use simple passwords like "1234" in production
- Change passwords for production deployment
- Keep `includes/db_connect.php` out of version control
