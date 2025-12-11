# How to Access Database in phpMyAdmin

## Quick Steps

### 1. Open phpMyAdmin
- Go to: `http://localhost/phpmyadmin`
- Or click "Admin" button next to MySQL in XAMPP Control Panel

### 2. Refresh the Page
- Press `Ctrl + F5` (hard refresh)
- Or clear browser cache and reload

### 3. Look for the Database
- Check the **left sidebar** for `ecommerce_db`
- If you don't see it, click the **refresh icon** (🔄) next to "Databases"

### 4. If Still Not Visible
Click on "Databases" tab at the top, you should see:

| Database | Tables | Size |
|----------|--------|------|
| ecommerce_db | 9 | ~XXX KB |

### 5. Access the Database
- Click on `ecommerce_db` in the left sidebar
- You should see 9 tables:
  - admin_users
  - cart
  - categories
  - order_items
  - orders
  - products
  - users
  - order_summary (view)
  - product_inventory (view)

## Troubleshooting

### Database Not Showing in Left Sidebar

**Solution 1: Refresh phpMyAdmin**
1. Click the phpMyAdmin logo (top left)
2. Press `Ctrl + F5` to hard refresh
3. Look in left sidebar again

**Solution 2: Check Databases Tab**
1. Click "Databases" at the top
2. Look for `ecommerce_db` in the list
3. Click on it to open

**Solution 3: Restart MySQL**
1. Open XAMPP Control Panel
2. Click "Stop" next to MySQL
3. Wait 3 seconds
4. Click "Start" next to MySQL
5. Refresh phpMyAdmin

### "Access Denied" Error

Check your phpMyAdmin config: `C:\xampp\phpMyAdmin\config.inc.php`

Make sure it has:
```php
$cfg['Servers'][$i]['user'] = 'root';
$cfg['Servers'][$i]['password'] = '';
```

### Database Shows as Empty

Re-import the SQL file:
1. In phpMyAdmin, select `ecommerce_db` from left sidebar
2. Click "Import" tab
3. Choose `database/fresh_setup.sql`
4. Click "Go"

## Verify Database Contents

Once you can see `ecommerce_db`, click on it and run this SQL query:

```sql
SELECT 
    (SELECT COUNT(*) FROM products) as Products,
    (SELECT COUNT(*) FROM users) as Users,
    (SELECT COUNT(*) FROM admin_users) as Admins,
    (SELECT COUNT(*) FROM orders) as Orders;
```

**Expected Result:**
- Products: 24
- Users: 3
- Admins: 3
- Orders: 4

## Still Having Issues?

1. **Check MySQL is running** in XAMPP (should show green)
2. **Try accessing via command line**:
   ```bash
   cd C:\xampp\mysql\bin
   .\mysql -u root
   ```
   Then type: `SHOW DATABASES;`
   
3. **Clear browser cache completely**
4. **Try a different browser**
5. **Restart XAMPP completely**
