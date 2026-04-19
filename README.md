# Restaurant Management System

A complete **Single Page Application (SPA)** built with **Pure Core PHP**, **MySQL (PDO)**, and **Vanilla JavaScript**.

## 🎯 Features

- ✅ Modern SPA feel with no `.php` extensions in URLs
- ✅ Secure authentication with `password_hash()` and Sessions
- ✅ Role-based access (Admin, Waiter, Kitchen, Customer)
- ✅ Table management and reservations
- ✅ Complete menu management system
- ✅ Order tracking and management
- ✅ Billing and invoice generation
- ✅ Kitchen Display System (KDS) for real-time orders
- ✅ Inventory tracking with deduction on orders
- ✅ Revenue analytics and reporting
- ✅ Clean RESTful-style routing without frameworks

## 📁 Project Structure

```
restaurant-management/
├── public/
│   ├── index.php              # Main router entry point
│   ├── .htaccess              # URL rewriting rules
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   ├── app.js             # SPA main application
│   │   └── utils.js           # Utility functions
│   └── assets/                # Images, icons, etc.
├── app/
│   ├── controllers/           # Request handlers
│   │   ├── AuthController.php
│   │   ├── MenuController.php
│   │   ├── OrderController.php
│   │   ├── TableController.php
│   │   ├── AdminController.php
│   │   └── WaiterController.php
│   ├── models/                # Data models
│   │   ├── User.php
│   │   ├── Table.php
│   │   ├── MenuItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   └── Inventory.php
│   ├── middleware/            # Middleware classes
│   │   └── Auth.php
│   └── views/                 # HTML views
│       ├── layouts/           # Layout templates
│       ├── customer/          # Customer portal
│       ├── waiter/            # Waiter dashboard
│       ├── kitchen/           # Kitchen display
│       ├── admin/             # Admin panel
│       ├── login.php
│       └── register.php
├── config/
│   ├── database.php           # PDO connection & helpers
│   └── config.php             # App constants & helpers
└── schema.sql                 # Database schema

```

## 🚀 Installation & Setup

### 1. **Prerequisites**
- PHP 7.4+ with MySQLi/PDO support
- MySQL 5.7+
- Apache with `mod_rewrite` enabled
- A local development server (XAMPP, WAMP, LAMP, etc.)

### 2. **Clone/Download**
```bash
git clone <your-repo>
cd restaurant-management
```

### 3. **Database Setup**
```bash
# Import schema into MySQL
mysql -u root -p < schema.sql

# Or manually:
# 1. Open phpMyAdmin (http://localhost/phpmyadmin)
# 2. Create new database: restaurant_management
# 3. Import schema.sql
```

### 4. **Configure Database Credentials**
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'restaurant_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 5. **Configure Apache Virtual Host** (Optional but recommended)
Edit your Apache config or hosts file:
```apache
<VirtualHost *:80>
    ServerName restaurant-management.local
    DocumentRoot "/path/to/restaurant-management/public"
    <Directory "/path/to/restaurant-management/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to your `hosts` file:
```
127.0.0.1  restaurant-management.local
```

### 6. **Access the Application**
```
http://localhost/restaurant-management/public
or
http://restaurant-management.local
```

### 7. **Default Credentials**
After importing schema.sql, use:
- **Admin**: admin@restaurant.local / password
- **Waiter**: waiter@restaurant.local / password
- **Kitchen**: kitchen@restaurant.local / password
- **Customer**: john@customer.local / password

## 🏗️ Architecture Overview

### Routing System (`public/index.php`)
- Automatically parses requests via `.htaccess`
- Routes requests to appropriate Controllers
- Supports both HTML views and JSON API endpoints
- All requests go through `/api/` prefix for API endpoints

### Example Routes

**View Routes (HTML):**
```
GET  /dashboard          → views/dashboard.php
GET  /menu               → views/customer/menu.php
GET  /waiter/orders      → views/waiter/orders.php
GET  /admin/staff        → views/admin/staff.php
```

**API Routes (JSON):**
```
POST /api/auth/login          → AuthController::login()
GET  /api/menu/items          → MenuController::getItems()
POST /api/orders/create       → OrderController::create()
GET  /api/orders/pending      → OrderController::getPending()
PUT  /api/orders/1/status     → OrderController::updateStatus()
```

### Single Page Application (SPA)

The JavaScript SPA (`public/js/app.js`) provides:
- **Dynamic page loading** via Fetch API
- **Client-side routing** without page reloads
- **Form handling** with automatic API calls
- **Real-time notifications** (success/error messages)
- **Polling support** for Kitchen Display System (KDS)

**Example Usage:**
```javascript
// Load a page
app.loadPage('dashboard');

// Make API call
app.apiCall('auth/logout', 'POST').then(response => {
    console.log(response);
});

// Poll for real-time updates
const stopPolling = pollAPI('/api/orders/pending', (data) => {
    console.log('New pending orders:', data);
}, 5000);
```

## 🔐 Security Features

- ✅ **PDO Prepared Statements** - Prevents SQL injection
- ✅ **Password Hashing** - Using `password_hash()` and `password_verify()`
- ✅ **Session Management** - Secure session handling
- ✅ **CSRF Protection** - Can be added to forms
- ✅ **Input Sanitization** - HTML entity escaping
- ✅ **Role-based Access Control (RBAC)** - Middleware support

## 📊 Database Tables

### users
- User accounts with role-based access
- Hashed password storage
- Supports: admin, waiter, kitchen, customer roles

### tables
- Restaurant table management
- Status tracking: available, occupied, reserved
- Capacity management

### menu_items
- Complete menu catalog
- Categories and pricing
- Preparation time tracking
- Availability status

### orders
- Order tracking from creation to completion
- Status management: pending, preparing, served, paid, cancelled
- Total amount calculation
- Linked to tables and customers

### order_items
- Individual items in each order
- Special requests support
- Item-level status tracking

### inventory
- Ingredient tracking per menu item
- Quantity deduction on orders
- Reorder level management

### invoices
- Invoice generation from orders
- Payment tracking
- Tax and discount management

### reservations
- Table reservations
- Customer contact information
- Reservation status tracking

## 🎛️ Key Features Implementation

### 1. **Authentication System**
```php
// Login
POST /api/auth/login
{
    "email": "user@restaurant.local",
    "password": "password123"
}

// Register
POST /api/auth/register
{
    "name": "John Doe",
    "email": "john@restaurant.local",
    "password": "password123",
    "role": "customer"
}

// Logout
POST /api/auth/logout
```

### 2. **Order Management**
```php
// Create order
POST /api/orders/create
{
    "table_id": 1,
    "items": [
        {"menu_item_id": 1, "quantity": 2, "notes": "Less salt"}
    ]
}

// Update order status
PUT /api/orders/1/status
{
    "status": "preparing"
}

// Get pending orders (for waiter/kitchen)
GET /api/orders/pending
```

### 3. **Kitchen Display System (KDS)**
```php
// Poll for pending orders
GET /api/orders/pending

// Updates in real-time every 5 seconds
app.app.pollAPI('/api/kitchen/orders', (orders) => {
    updateKitchenDisplay(orders);
}, 5000);
```

### 4. **Inventory Management**
```php
// Deduct inventory on order
- When order_item is created, reduce inventory qty
- Check minimum_stock levels
- Generate alerts for low stock
```

### 5. **Revenue Analytics**
```sql
-- Daily revenue report
SELECT DATE(order_time) as date, SUM(total_amount) as daily_revenue
FROM orders
WHERE status = 'paid'
GROUP BY DATE(order_time);

-- Top selling items
SELECT mi.name, SUM(oi.quantity) as total_sold, SUM(oi.subtotal) as revenue
FROM order_items oi
JOIN menu_items mi ON oi.menu_item_id = mi.id
GROUP BY mi.id
ORDER BY total_sold DESC;
```

## 🎨 Frontend Implementation

### Navigation Bar
```html
<a href="#" data-link="dashboard">Dashboard</a>
<a href="#" data-link="menu">Menu</a>
<a href="#" data-link="orders">Orders</a>
<a href="#" data-link="profile">Profile</a>
```

### SPA Content Loading
```html
<div id="app-content">
    <!-- Content loads here dynamically -->
</div>
<div id="loader">
    <div class="spinner"></div>
</div>
```

### Form Submission
```html
<form data-api="orders/create" method="POST" data-clear-on-success="true">
    <input type="hidden" name="table_id" value="1">
    <button type="submit">Place Order</button>
</form>
```

## 📝 API Response Format

All API responses follow this format:
```json
{
    "success": true,
    "message": "Order created successfully",
    "code": 200,
    "data": {
        "order_id": 1,
        "order_number": "ORD-001",
        "total_amount": 45.99
    }
}
```

## 🛠️ Extending the System

### Adding a New Feature

1. **Create Model** (`app/models/Feature.php`)
```php
class Feature {
    public function __construct() { }
    public function getAll() { }
    public function create($data) { }
}
```

2. **Create Controller** (`app/controllers/FeatureController.php`)
```php
class FeatureController {
    public function index() {
        // Return data as JSON
        return ['success' => true, 'data' => []];
    }
}
```

3. **Create View** (`app/views/feature/index.php`)
```php
<h1>Feature Page</h1>
```

4. **Access via Router**
```
GET  /feature              → views/feature/index.php
GET  /api/feature/list     → FeatureController::index()
```

## 📚 Recommended Improvements

1. **Add Email Notifications**
   - Order confirmation emails
   - Reservation reminders

2. **Add Real-time WebSockets**
   - Use Socket.io for live updates
   - Replace polling with event-driven updates

3. **Add Payment Gateway**
   - Stripe/PayPal integration
   - Online payment processing

4. **Add Ratings & Reviews**
   - Customer feedback system
   - Star ratings for items

5. **Add Loyalty Program**
   - Points system
   - Rewards tracking

6. **Add SMS Notifications**
   - Twilio integration
   - Order status updates via SMS

## 🐛 Troubleshooting

### Issue: 404 errors on routes
- Ensure `.htaccess` is in `public/` folder
- Enable `mod_rewrite` in Apache: `a2enmod rewrite`
- Set `AllowOverride All` in Apache config

### Issue: Database connection error
- Check database credentials in `config/database.php`
- Ensure MySQL is running
- Verify database exists: `mysql_database`

### Issue: Session not persisting
- Check PHP `session.save_path` permissions
- Ensure cookies are enabled in browser

## 📖 Documentation

- Full API documentation in `/docs/api.md` (to be created)
- Database schema details in `/docs/database.md` (to be created)
- Development guide in `/docs/development.md` (to be created)

## 📄 License

This project is provided as-is for educational purposes.

## 👨‍💻 Support

For issues, questions, or feature requests, please contact the development team.

---

**Built with ❤️ using Pure PHP, MySQL, and Vanilla JavaScript**
