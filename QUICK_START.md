# Quick Start Guide - Fresh Database Setup

## For ALL Team Members (Root, Judy, Habiba)

Everyone uses the same simple setup process.

---

## Step 1: Import the Database

### Option A: Using phpMyAdmin (Easiest)

1. Start XAMPP (Apache + MySQL)
2. Open: `http://localhost/phpmyadmin`
3. Click **"Import"** tab
4. Click **"Choose File"** → Select `database/fresh_setup.sql`
5. Click **"Go"** at the bottom
6. Wait for success message ✅

### Option B: Using Command Line

```bash
cd C:\xampp\mysql\bin
.\mysql -u root < C:\xampp\htdocs\Online_Phones_Store\database\fresh_setup.sql
```

---

## Step 2: Configure Your Connection

**Everyone uses the same configuration:**

Open `includes/db_connect.php` and make sure it has:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce_db";
```

That's it! No need for separate MySQL users.

---

## Step 3: Test Your Setup

1. **Visit the website**: `http://localhost/Online_Phones_Store/`
   - You should see **24 products** displayed

2. **Run the test**: `http://localhost/Online_Phones_Store/test_connection.php`
   - All tests should pass ✅

3. **Test admin login**: `http://localhost/Online_Phones_Store/admin/login.php`
   - Username: `admin`
   - Password: `1234`

---

## What's Included in the Database

### Tables Created
- ✅ **products** (24 sample phones)
- ✅ **users** (3 test customers)
- ✅ **admin_users** (3 admin accounts)
- ✅ **orders** (4 sample orders)
- ✅ **order_items** (order details)
- ✅ **cart** (shopping cart)
- ✅ **categories** (10 phone brands)

### Sample Products (24 phones)
- 5 Apple iPhones
- 5 Samsung Galaxy phones
- 3 Google Pixels
- 3 OnePlus phones
- 3 Xiaomi phones
- 5 Other brands (Nothing, Motorola, Sony, ASUS, Oppo)

### Admin Accounts
All admin accounts have password: **admin123**

| Username | Role | Email |
|----------|------|-------|
| admin | Administrator | admin@phonesstore.com |
| judy | Manager | judy@phonesstore.com |
| habiba | Staff | habiba@phonesstore.com |

### Test Customer Accounts
All customer accounts have password: **password123**

| Username | Email |
|----------|-------|
| john_doe | john@example.com |
| jane_smith | jane@example.com |
| mike_wilson | mike@example.com |

---

## Database Features

### Advanced Features Included
- ✅ Foreign key relationships
- ✅ Indexes for better performance
- ✅ UTF-8 support for all languages
- ✅ Timestamps for tracking changes
- ✅ Sample orders with order items
- ✅ Database views for easy queries
- ✅ Stock quantity tracking

### Useful SQL Queries

**View all products by category:**
```sql
SELECT * FROM product_inventory ORDER BY category;
```

**View order summary:**
```sql
SELECT * FROM order_summary;
```

**Check low stock items:**
```sql
SELECT name, stock_quantity FROM products WHERE stock_quantity < 20;
```

---

## Troubleshooting

### "Table already exists" error
The SQL file drops the old database first. If you get this error:
1. Go to phpMyAdmin
2. Drop the `ecommerce_db` database manually
3. Re-import `fresh_setup.sql`

### "Connection failed" error
- Make sure MySQL is running in XAMPP
- Check `includes/db_connect.php` has correct credentials
- Username should be `root` with empty password

### No products showing
- Re-import the SQL file
- Check if import completed successfully
- Look for error messages in phpMyAdmin

---

## Git Configuration

The `includes/db_connect.php` file is in `.gitignore`, so:
- ✅ Everyone can use the same default configuration
- ✅ No conflicts when pulling/pushing code
- ✅ Each person's local setup is independent

---

## Need Help?

- See `TROUBLESHOOTING_MYSQL.md` for detailed solutions
- Run `test_connection.php` to diagnose issues
- Check XAMPP MySQL logs for errors

---

## Security Note

⚠️ **These are development credentials only!**
- Simple passwords are for local testing
- Never use these in production
- Change all passwords before deploying live
