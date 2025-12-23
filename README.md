# 🛍️ TechHub Electronics - E-Commerce Platform

A modern, premium e-commerce platform for electronics built with PHP and MySQL.

![TechHub Electronics](https://img.shields.io/badge/PHP-8.x-blue)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

---

## ✨ Features

### 🎨 **Modern Design**
- Premium gradient color scheme (Deep Space Blue → Electric Purple)
- Responsive design for all devices
- Smooth animations and microinteractions
- Professional UI/UX with cyan accents

### 🛒 **E-Commerce Functionality**
- Product browsing with category filters
- Advanced search and filtering
- Shopping cart with session management
- Secure checkout process
- Multiple payment methods (Card, Cash on Delivery)
- Order tracking and history
- Wishlist functionality

### 👤 **User Management**
- User registration and authentication
- Secure password hashing
- Profile management
- Order history
- Shipping address management

### 🎯 **Product Features**
- Product categories and brands
- Product images and galleries
- Stock management
- Price comparison (sale prices)
- Featured products
- Product reviews (ready for implementation)

### 💳 **Checkout System**
- Two-column checkout layout
- Order summary sidebar
- Progress indicator
- Promo code support
- Shipping cost calculation
- Order confirmation emails

### 🔐 **Security**
- Prepared statements (SQL injection protection)
- Password hashing (bcrypt)
- Session security
- CSRF protection ready
- Input validation and sanitization
- Secure file access controls

---

## 🚀 Technology Stack

- **Backend**: PHP 8.x
- **Database**: MySQL 8.0 / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **Icons**: Bootstrap Icons, Font Awesome
- **Server**: Apache (with .htaccess)

---

## 📁 Project Structure

```
Online_Phones_Store/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet (1770+ lines)
│   ├── js/
│   │   └── main.js            # JavaScript functionality
│   └── images/                # Product and site images
├── includes/
│   ├── db_connect.php         # Database connection
│   ├── template.php           # Page template
│   └── user_auth.php          # Authentication functions
├── admin/                     # Admin panel (if applicable)
├── index.php                  # Homepage
├── product_detail.php         # Product details page
├── Cart.php                   # Shopping cart
├── checkout.php               # Checkout page
├── checkout_process.php       # Order processing
├── thank_you.php              # Order confirmation
├── login.php                  # User login
├── signup.php                 # User registration
├── profile.php                # User profile
├── orders.php                 # Order history
├── wishlist.php               # User wishlist
├── logout.php                 # Logout handler
├── .htaccess                  # Apache configuration
├── DEPLOYMENT.md              # Deployment guide
└── README.md                  # This file
```

---

## 🎨 Design Highlights

### Color Palette
- **Primary Gradient**: Deep Space Blue (#0F172A) → Electric Purple (#7C3AED)
- **Accent Cyan**: #06B6D4
- **Accent Silver**: #94A3B8
- **Background**: Light Grey (#F5F7FA)
- **Card White**: #FFFFFF

### Key Design Elements
- Animated gradient hero section
- Floating product cards with 3D hover effects
- Modern payment selection boxes
- Progress indicators for checkout
- Gradient buttons with ripple effects
- Soft shadows and rounded corners (24px)
- Premium typography with custom letter spacing

---

## 💾 Database Schema

### Main Tables
- `users` - User accounts and authentication
- `products` - Product catalog
- `categories` - Product categories
- `brands` - Product brands
- `product_images` - Product image gallery
- `orders` - Customer orders
- `order_items` - Order line items
- `wishlists` - User wishlists
- `user_addresses` - Shipping addresses
- `coupons` - Discount codes

---

## 🔧 Installation (Local Development)

### Prerequisites
- XAMPP/WAMP/MAMP (PHP 8.x + MySQL)
- Web browser
- Text editor/IDE

### Steps
1. **Clone/Download** project to `htdocs` folder
2. **Import Database**:
   - Open phpMyAdmin
   - Create database `online_phones_store`
   - Import SQL file
3. **Configure Database**:
   - Edit `includes/db_connect.php`
   - Update credentials
4. **Start Server**:
   - Start Apache and MySQL
   - Visit `http://localhost/Online_Phones_Store`

---

## 🌐 Deployment

See **[DEPLOYMENT.md](DEPLOYMENT.md)** for detailed deployment instructions.

### Recommended Hosting
- **InfinityFree** (Free, PHP + MySQL, No Ads)
- **000webhost** (Free, Easy Setup)
- **Awardspace** (Free, Good Performance)

### Quick Deploy Steps
1. Export database from phpMyAdmin
2. Upload files to hosting (via cPanel File Manager)
3. Create MySQL database on host
4. Import database via phpMyAdmin
5. Update `db_connect.php` with production credentials
6. Test all functionality

---

## 🔐 Security Features

- ✅ Prepared statements for all database queries
- ✅ Password hashing with `password_hash()`
- ✅ Session-based authentication
- ✅ Input validation and sanitization
- ✅ HTTPS ready (SSL support)
- ✅ Protected sensitive files via `.htaccess`
- ✅ CSRF protection ready
- ✅ XSS prevention
- ✅ SQL injection protection

---

## 📱 Responsive Design

- **Desktop**: Full-featured experience with sidebars
- **Tablet**: Optimized layout with collapsible menus
- **Mobile**: Touch-friendly interface, stacked layout

---

## 🎯 Key Pages

### Customer-Facing
- **Homepage**: Hero section, category filters, product grid, featured brands
- **Product Detail**: Images, description, add to cart, reviews
- **Cart**: Item management, quantity updates, totals
- **Checkout**: Two-column layout, order summary, payment selection
- **Profile**: Account info, order history, wishlist
- **Login/Signup**: Modern auth forms with gradient design

### Admin (If Implemented)
- Dashboard
- Product management
- Order management
- User management

---

## 🛠️ Customization

### Changing Colors
Edit `assets/css/style.css` - CSS variables at top:
```css
:root {
    --accent-cyan: #06B6D4;
    --electric-purple: #A855F7;
    /* ... more variables */
}
```

### Adding Products
1. Access phpMyAdmin
2. Insert into `products` table
3. Add images to `product_images` table
4. Set categories and brands

### Email Configuration
Update SMTP settings in order processing files for email notifications.

---

## 📊 Features Roadmap

- [ ] Product reviews and ratings
- [ ] Advanced search with filters
- [ ] Email notifications (order confirmations)
- [ ] Admin dashboard
- [ ] Inventory alerts
- [ ] Sales analytics
- [ ] Customer support chat
- [ ] Social media integration

---

## 🐛 Known Issues

None currently. Report issues via GitHub or contact.

---

## 📄 License

This project is for educational/commercial use. Modify as needed.

---

## 👨‍💻 Developer

**TechHub Electronics Platform**
- Modern PHP E-Commerce Solution
- Built with attention to UX and security
- Fully responsive and production-ready

---

## 🙏 Acknowledgments

- Bootstrap 5 for responsive framework
- Bootstrap Icons for icon set
- Font Awesome for additional icons
- Community feedback and testing

---

## 📞 Support

For deployment help, see **DEPLOYMENT.md**
For technical issues, check hosting provider documentation

---

**🎉 Ready to deploy your TechHub Electronics store!**
