# MySQL Credentials Setup Guide

This guide explains how to set up MySQL username and password in XAMPP and configure the project to use those credentials.

## For Collaborators: Setting Up MySQL Password in XAMPP

### Option 1: Using phpMyAdmin (Easiest)

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL

2. **Open phpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`

3. **Go to User Accounts**
   - Click on "User accounts" tab at the top

4. **Edit root user**
   - Find the user `root` with host `localhost`
   - Click "Edit privileges"

5. **Change Password**
   - Click on "Change password" tab
   - Select "Password" (not "No password")
   - Enter your new password (e.g., `mypassword123`)
   - Re-type the password to confirm
   - Click "Go" at the bottom

6. **Update phpMyAdmin config** (Important!)
   - Navigate to: `C:\xampp\phpMyAdmin\config.inc.php`
   - Open with a text editor
   - Find this line:
     ```php
     $cfg['Servers'][$i]['password'] = '';
     ```
   - Change it to:
     ```php
     $cfg['Servers'][$i]['password'] = 'mypassword123';
     ```
   - Save the file

### Option 2: Using MySQL Command Line

1. **Open Command Prompt as Administrator**

2. **Navigate to MySQL bin**:
   ```bash
   cd C:\xampp\mysql\bin
   ```

3. **Login to MySQL** (no password initially):
   ```bash
   mysql -u root
   ```

4. **Set new password**:
   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED BY 'mypassword123';
   FLUSH PRIVILEGES;
   EXIT;
   ```

5. **Update phpMyAdmin config** (same as Option 1, step 6)

## Updating Project Database Configuration

After setting your MySQL password, you MUST update the project's database connection file:

1. **Open the file**: `includes/db_connect.php`

2. **Update the password**:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "mypassword123";  // Change this to your MySQL password
   $dbname = "ecommerce_db";
   ```

3. **Save the file**

## For Different MySQL Users

If you want to use a different MySQL user (not root):

1. **Create a new MySQL user** in phpMyAdmin:
   - Go to "User accounts" → "Add user account"
   - Username: `ecommerce_user`
   - Host: `localhost`
   - Password: `your_secure_password`
   - Check "Create database with same name and grant all privileges"
   - Click "Go"

2. **Update `includes/db_connect.php`**:
   ```php
   $servername = "localhost";
   $username = "ecommerce_user";      // Your new username
   $password = "your_secure_password"; // Your new password
   $dbname = "ecommerce_db";
   ```

## Restart MySQL After Changes

After making any password changes:

1. Stop MySQL in XAMPP Control Panel
2. Start MySQL again
3. Test the connection by accessing your website

## Troubleshooting

### "Access denied for user 'root'@'localhost'"
- You changed MySQL password but didn't update `includes/db_connect.php`
- Solution: Update the password in `db_connect.php` to match your MySQL password

### "phpMyAdmin - Cannot connect: invalid settings"
- You changed MySQL password but didn't update phpMyAdmin config
- Solution: Update `C:\xampp\phpMyAdmin\config.inc.php` with your new password

### "Connection failed" error on website
- MySQL service not running
- Wrong credentials in `db_connect.php`
- Solution: Check XAMPP, verify credentials match

## Security Best Practices

1. **Never use simple passwords** like "123456" or "password"
2. **Don't share your MySQL password** in public repositories
3. **Each collaborator should set their own local MySQL password**
4. **Keep `includes/db_connect.php` in `.gitignore`** (already configured)

## For Team Collaboration

**Important**: Each team member can have different MySQL passwords on their local machine. They just need to:

1. Import the database using `database/schema.sql`
2. Set their own MySQL password (optional)
3. Update their local `includes/db_connect.php` file
4. The `.gitignore` file prevents sharing personal credentials

**You don't need to share your MySQL password with collaborators!** Each person configures their own local setup.
