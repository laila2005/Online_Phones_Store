# Testing Guide - Phase 2: The Shop Window

## Prerequisites
- ✅ XAMPP running (Apache + MySQL)
- ✅ Database `ecommerce_db` created
- ✅ Table `products` with sample data

---

## Test Checklist

### 1️⃣ Main Product Catalog (index.php)

**URL:** `http://localhost/Online_Phones_Store/index.php`

**What to Check:**
- [ ] Page loads without errors
- [ ] All products are displayed in a grid layout
- [ ] Product images show correctly (or placeholder if missing)
- [ ] Product names, prices, and descriptions are visible
- [ ] Category badges appear on products
- [ ] "View Details" buttons are present
- [ ] "Add to Cart" buttons are present

**Expected Layout:**
```
┌─────────────────────────────────────────────┐
│  Welcome to Online Phones Store             │
│  Browse our latest collection              │
├─────────────────────────────────────────────┤
│  [All Products] [Category1] [Category2]...  │
├─────────────────────────────────────────────┤
│  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐   │
│  │ IMG  │  │ IMG  │  │ IMG  │  │ IMG  │   │
│  │ Name │  │ Name │  │ Name │  │ Name │   │
│  │ $999 │  │ $799 │  │ $699 │  │ $599 │   │
│  │ Desc │  │ Desc │  │ Desc │  │ Desc │   │
│  │[View]│  │[View]│  │[View]│  │[View]│   │
│  │[Cart]│  │[Cart]│  │[Cart]│  │[Cart]│   │
│  └──────┘  └──────┘  └──────┘  └──────┘   │
└─────────────────────────────────────────────┘
```

---

### 2️⃣ Category Filtering

**Test Steps:**
1. Click on "All Products" button
   - [ ] All products are shown
   - [ ] Button is highlighted in blue

2. Click on a specific category button (e.g., "Smartphones")
   - [ ] Only products from that category are shown
   - [ ] Category button is highlighted in blue
   - [ ] URL changes to `?category=Smartphones`

3. Click on another category
   - [ ] Products update to show new category
   - [ ] Previous category button returns to outline style
   - [ ] New category button is highlighted

4. Click "All Products" again
   - [ ] All products are displayed again

---

### 3️⃣ Product Detail Page (product_detail.php)

**Test Steps:**
1. From the main page, click "View Details" on any product

**URL Pattern:** `http://localhost/Online_Phones_Store/product_detail.php?id=1`

**What to Check:**
- [ ] Breadcrumb navigation shows: Home → Category → Product Name
- [ ] Large product image is displayed
- [ ] Product name is shown as heading
- [ ] Category badge is visible
- [ ] Price is displayed prominently
- [ ] Full description is shown (not truncated)
- [ ] Stock availability status is shown
- [ ] "Add to Cart" button is present (or disabled if out of stock)
- [ ] "Continue Shopping" button works
- [ ] Related products section shows similar items

**Expected Layout:**
```
┌─────────────────────────────────────────────┐
│  Home > Category > Product Name             │
├──────────────────┬──────────────────────────┤
│                  │  Product Name            │
│   [LARGE IMAGE]  │  [Category Badge]        │
│                  │  $999.99                 │
│                  │                          │
│                  │  Description:            │
│                  │  Full product details... │
│                  │                          │
│                  │  Availability:           │
│                  │  In Stock (10 available) │
│                  │                          │
│                  │  [Add to Cart]           │
│                  │  [Continue Shopping]     │
├──────────────────┴──────────────────────────┤
│  Related Products                           │
│  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐   │
│  │ IMG  │  │ IMG  │  │ IMG  │  │ IMG  │   │
│  └──────┘  └──────┘  └──────┘  └──────┘   │
└─────────────────────────────────────────────┘
```

---

### 4️⃣ Add to Cart Functionality

**Test Steps:**
1. Click "Add to Cart" button on any product

**What to Check:**
- [ ] Alert popup appears with message
- [ ] Button text changes to "Added!"
- [ ] Button color changes to gray
- [ ] Button is temporarily disabled
- [ ] After 2 seconds, button returns to normal state

**Expected Behavior:**
```
Click → Alert: "Product X added to cart!" 
      → Button: "Add to Cart" → "Added!" → "Add to Cart"
      → Color: Green → Gray → Green
```

---

### 5️⃣ Error Handling

**Test Invalid Product ID:**

**URL:** `http://localhost/Online_Phones_Store/product_detail.php?id=99999`

**What to Check:**
- [ ] Error message is displayed
- [ ] "Product not found" alert is shown
- [ ] "Back to Shop" button is present
- [ ] No PHP errors are displayed

**Test Missing ID:**

**URL:** `http://localhost/Online_Phones_Store/product_detail.php`

**What to Check:**
- [ ] Error message: "Invalid product ID"
- [ ] Page doesn't crash

---

### 6️⃣ Responsive Design

**Test on Different Screen Sizes:**

1. **Desktop (1920px+)**
   - [ ] 4 products per row
   - [ ] Category buttons in a single row

2. **Tablet (768px - 1024px)**
   - [ ] 2-3 products per row
   - [ ] Category buttons wrap if needed

3. **Mobile (< 768px)**
   - [ ] 1-2 products per row
   - [ ] Category buttons stack vertically
   - [ ] Product detail page stacks image and info

**How to Test:**
- Resize browser window
- Use browser DevTools (F12) → Toggle device toolbar
- Test on actual mobile device

---

### 7️⃣ Navigation

**Test All Links:**

1. **Navbar Brand "Online Phones Store"**
   - [ ] Clicks returns to `index.php`

2. **Breadcrumb Links (on product detail page)**
   - [ ] "Home" → Returns to `index.php`
   - [ ] "Category" → Filters by that category on `index.php`

3. **"Continue Shopping" Button**
   - [ ] Returns to `index.php`

4. **Related Products "View Details"**
   - [ ] Opens correct product detail page

---

## Common Issues & Solutions

### Issue: "No products found"
**Solution:** 
- Check if products exist in database
- Run: `SELECT * FROM products;` in phpMyAdmin

### Issue: Images not showing
**Solution:**
- Verify `image_url` column has valid URLs or paths
- Check if image files exist at specified paths

### Issue: Category filter not working
**Solution:**
- Ensure `category` column exists in products table
- Check if products have category values assigned

### Issue: "Access denied" error
**Solution:**
- Verify `SECURE_ACCESS` constant is defined in PHP files
- Check `includes/db_connect.php` is included correctly

### Issue: Database connection failed
**Solution:**
- Verify XAMPP MySQL is running
- Check database credentials in `includes/db_connect.php`
- Ensure database `ecommerce_db` exists

---

## Sample Test Data

If you need to add test products, run this SQL:

```sql
INSERT INTO products (name, description, price, image_url, category, stock) VALUES
('iPhone 15 Pro', 'Latest iPhone with A17 Pro chip', 999.99, 'https://via.placeholder.com/300', 'Smartphones', 15),
('Samsung Galaxy S24', 'Flagship Samsung phone', 899.99, 'https://via.placeholder.com/300', 'Smartphones', 20),
('Google Pixel 8', 'Pure Android experience', 699.99, 'https://via.placeholder.com/300', 'Smartphones', 10),
('OnePlus 12', 'Fast charging flagship', 799.99, 'https://via.placeholder.com/300', 'Smartphones', 8);
```

---

## Performance Checklist

- [ ] Page loads in under 2 seconds
- [ ] Images load progressively
- [ ] No console errors (F12 → Console tab)
- [ ] Smooth hover animations on cards
- [ ] Buttons respond immediately to clicks

---

## Browser Compatibility

Test on:
- [ ] Google Chrome
- [ ] Mozilla Firefox
- [ ] Microsoft Edge
- [ ] Safari (if available)

---

## Final Verification

✅ All 4 tasks completed:
- Task 2.10: Bootstrap Setup
- Task 2.11: Product Catalog Page
- Task 2.12: Product Filtering
- Task 2.14: Product Detail Page

✅ All features working:
- Database connection
- Product display
- Category filtering
- Product details
- Add to cart (basic)
- Responsive design
- Error handling

---

**Ready for Phase 3!** 🚀
