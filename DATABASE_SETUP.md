# Database Setup Guide for Collaborators

This guide will help you set up the database for the Online Phones Store project on your local machine.

## Prerequisites

- XAMPP (or LAMP/WAMP/MAMP) installed on your machine
- Apache and MySQL services running

## Setup Instructions

### Method 1: Using phpMyAdmin (Recommended for Beginners)

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Access phpMyAdmin**
   - Open your browser and go to: `http://localhost/phpmyadmin`

3. **Import the Database**
   - Click on the "Import" tab at the top
   - Click "Choose File" and select `database/schema.sql` from the project folder
   - Click "Go" at the bottom of the page
   - Wait for the success message

4. **Verify the Database**
   - You should see `ecommerce_db` in the left sidebar
   - Click on it to expand and verify the tables: `products` and `admin_users`

### Method 2: Using MySQL Command Line

1. **Open Command Prompt/Terminal**

2. **Navigate to MySQL bin directory** (Windows example):
   ```bash
   cd C:\xampp\mysql\bin
   ```

3. **Login to MySQL**:
   ```bash
   mysql -u root -p
   ```
   (Press Enter when asked for password if you haven't set one)

4. **Import the SQL file**:
   ```bash
   source C:\xampp\htdocs\Online_Phones_Store\database\schema.sql
   ```
   (Adjust the path based on your installation)

5. **Exit MySQL**:
   ```bash
   exit
   ```

## Database Configuration

The database connection settings are in `includes/db_connect.php`:

```php
$servername = "localhost";
$username = "root";
$password = ""; // Default XAMPP has no password
$dbname = "ecommerce_db";
```

**If your MySQL setup is different**, you may need to adjust these values.

### Setting Up MySQL Password (Optional)

If you want to set a password for your MySQL root user, see the detailed guide: **`MYSQL_CREDENTIALS_SETUP.md`**

After setting a MySQL password, remember to update the `$password` value in `includes/db_connect.php` to match.

## Default Admin Credentials

After importing the database, you can login to the admin panel with:

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **IMPORTANT**: Change this password after your first login!

## Testing the Setup

1. **Start your web server**:
   - Make sure Apache and MySQL are running in XAMPP

2. **Access the website**:
   - Open browser: `http://localhost/Online_Phones_Store/`
   - You should see 8 sample products displayed

3. **Test admin panel**:
   - Go to: `http://localhost/Online_Phones_Store/admin/login.php`
   - Login with the default credentials above

## Troubleshooting

### "Connection failed" error
- Make sure MySQL service is running in XAMPP
- Check if the database name is `ecommerce_db`
- Verify your MySQL username/password in `includes/db_connect.php`

### "Access denied" error
- Your MySQL might have a password set
- Update the `$password` variable in `includes/db_connect.php`

### Database not found
- Re-import the `database/schema.sql` file
- Make sure the import completed successfully

### No products showing
- Check if the products table has data
- Run this query in phpMyAdmin: `SELECT * FROM products`

## Need Help?

If you encounter any issues, contact the project maintainer or check the MySQL error logs in XAMPP.
