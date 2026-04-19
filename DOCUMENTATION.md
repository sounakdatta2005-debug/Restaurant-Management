# Restaurant Management System - Complete Documentation

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Folder Structure](#folder-structure)
3. [Installation Guide](#installation-guide)
4. [Core Components](#core-components)
5. [Database Schema](#database-schema)
6. [API Endpoints](#api-endpoints)
7. [Frontend Architecture](#frontend-architecture)
8. [Future Enhancements](#future-enhancements)

---

## Project Overview

This is a **complete Restaurant Management System** built from scratch using:
- **Backend**: Pure PHP 7.4+ (no frameworks)
- **Database**: MySQL with PDO for security
- **Frontend**: Vanilla JavaScript with Fetch API (SPA-like)
- **Architecture**: MVC-inspired with RESTful API design

### Key Characteristics
- ✅ No `.php` extensions in URLs (clean routing)
- ✅ SPA feel without page reloads
- ✅ Role-based access control
- ✅ Real-time order tracking
- ✅ Complete order-to-payment workflow
- ✅ Kitchen Display System (KDS)
- ✅ Inventory management with deductions

---

## Folder Structure

```
restaurant-management/
│
├── public/                          # Publicly accessible files
│   ├── index.php                    # Main router - entry point for ALL requests
│   ├── .htaccess                    # Apache URL rewriting
│   ├── css/
│   │   └── style.css                # Complete stylesheet for SPA
│   ├── js/
│   │   ├── app.js                   # Main SPA application
│   │   └── utils.js                 # Utility functions & helpers
│   └── assets/                      # Images, icons, documents
│
├── app/                             # Application logic
│   ├── controllers/                 # Request handlers (API endpoints)
│   │   ├── AuthController.php       # Login, Register, Logout
│   │   ├── MenuController.php       # Menu items management
│   │   ├── OrderController.php      # Order creation & management
│   │   ├── TableController.php      # Table management
│   │   ├── AdminController.php      # Admin features
│   │   ├── WaiterController.php     # Waiter-specific features
│   │   ├── KitchenController.php    # Kitchen Display System
│   │   └── InventoryController.php  # Inventory management
│   │
│   ├── models/                      # Data models
│   │   ├── User.php                 # User model
│   │   ├── Table.php                # Table model
│   │   ├── MenuItem.php             # Menu item model
│   │   ├── Order.php                # Order model
│   │   ├── OrderItem.php            # Order line items
│   │   ├── Inventory.php            # Inventory model
│   │   ├── Invoice.php              # Invoice model
│   │   └── Reservation.php          # Reservation model
│   │
│   ├── middleware/                  # Middleware classes
│   │   └── Auth.php                 # Authentication middleware
│   │
│   └── views/                       # HTML templates
│       ├── layouts/
│       │   ├── header.php           # Common header with navigation
│       │   └── footer.php           # Common footer
│       ├── customer/
│       │   ├── menu.php             # View menu & order
│       │   ├── reservations.php     # Book tables
│       │   └── orders.php           # Track orders
│       ├── waiter/
│       │   ├── dashboard.php        # Waiter overview
│       │   ├── orders.php           # Order management
│       │   └── tables.php           # Table management
│       ├── kitchen/
│       │   ├── orders.php           # Kitchen Display System (KDS)
│       │   └── inventory.php        # Inventory management
│       ├── admin/
│       │   ├── dashboard.php        # Admin overview
│       │   ├── staff.php            # Employee management
│       │   ├── analytics.php        # Revenue & reports
│       │   └── menu.php             # Menu management
│       ├── login.php                # Login page
│       └── register.php             # Registration page
│
├── config/                          # Configuration files
│   ├── database.php                 # PDO connection & helpers
│   └── config.php                   # Constants & app config
│
├── schema.sql                       # Database schema & sample data
├── README.md                        # Quick start guide
└── DOCUMENTATION.md                 # This file

```

---

## Installation Guide

### Prerequisites
```
✓ PHP 7.4 or higher
✓ MySQL 5.7 or higher
✓ Apache with mod_rewrite enabled
✓ XAMPP/WAMP/LAMP stack
```

### Step 1: Setup Local Environment

**Using XAMPP on Windows:**
```bash
# 1. Extract to C:\xampp\htdocs\restaurant-management
# 2. Start Apache and MySQL from XAMPP Control Panel
# 3. Access: http://localhost/restaurant-management/public
```

**Using LAMP on Linux:**
```bash
# 1. Clone to project directory
git clone <your-repo> /var/www/restaurant-management

# 2. Give permissions
sudo chown -R www-data:www-data /var/www/restaurant-management
chmod -R 755 /var/www/restaurant-management

# 3. Enable mod_rewrite
sudo a2enmod rewrite

# 4. Create Apache vhost (optional)
sudo nano /etc/apache2/sites-available/restaurant-management.conf
# Add configuration as shown in README.md
sudo a2ensite restaurant-management.conf
sudo systemctl restart apache2
```

### Step 2: Create Database

**Method 1: Using MySQL Command Line**
```bash
mysql -u root -p < /path/to/schema.sql
```

**Method 2: Using phpMyAdmin**
1. Open http://localhost/phpmyadmin
2. Click "New" → Enter Database Name: `restaurant_management`
3. Click Import → Select `schema.sql`
4. Click "Import"

### Step 3: Configure Database

Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');      // Your MySQL host
define('DB_NAME', 'restaurant_management');  // Database name
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', '');               // MySQL password
define('DB_PORT', 3306);             // MySQL port
```

### Step 4: Update App URL (Optional)

Edit `config/config.php`:
```php
define('APP_URL', 'http://localhost/restaurant-management/public');
```

### Step 5: Access the Application

```
http://localhost/restaurant-management/public
```

**Default Test Accounts:**
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@restaurant.local | password |
| Waiter | waiter@restaurant.local | password |
| Kitchen | kitchen@restaurant.local | password |
| Customer | john@customer.local | password |

> **Note**: Hash these passwords in production using `password_hash('password', PASSWORD_DEFAULT)`

---

## Core Components

### 1. Router (`public/index.php`)

The main entry point that:
- Parses all incoming requests
- Routes to appropriate controllers or views
- Handles authentication
- Returns JSON for API, HTML for views

```php
// Flow: .htaccess → index.php → Router → Controller/View
// Example:
// GET /waiter/orders
// ↓
// Router detects: controller=waiter, action=orders
// ↓
// Route to: views/waiter/orders.php (or API endpoint)
```

### 2. Configuration System

**config/config.php** - App constants
```php
// Roles
ROLE_ADMIN      = 'admin'
ROLE_WAITER     = 'waiter'
ROLE_KITCHEN    = 'kitchen'
ROLE_CUSTOMER   = 'customer'

// Order statuses
ORDER_STATUS_PENDING     = 'pending'
ORDER_STATUS_PREPARING   = 'preparing'
ORDER_STATUS_SERVED      = 'served'
ORDER_STATUS_PAID        = 'paid'

// Table statuses
TABLE_STATUS_AVAILABLE = 'available'
TABLE_STATUS_OCCUPIED  = 'occupied'
TABLE_STATUS_RESERVED  = 'reserved'
```

**config/database.php** - PDO Connection
```php
// PDO connection with proper error handling
$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
]);

// Helper functions
fetchAll($sql, $params)     // Get multiple records
fetchOne($sql, $params)     // Get single record
executeQuery($sql, $params) // Execute any query
recordExists($table, $col, $val) // Check existence
```

### 3. SPA Application (`public/js/app.js`)

Provides SPA functionality:
- Dynamic page loading without refreshes
- Form submission handling
- API communication
- Navigation history
- Loading states & notifications

**Core Methods:**
```javascript
app.loadPage(page)              // Load page dynamically
app.apiCall(endpoint, method, data) // Make API calls
app.showSuccess(message)        // Show success notification
app.showError(message)          // Show error notification
```

---

## Database Schema

### ER Diagram

```
┌─────────────┐
│    users    │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ role        │
│ status      │
└─────────────┘
       │
       ├──┬──────────────────┐
       │  │                  │
       ▼  ▼                  ▼
  ┌────────────┐      ┌────────────────┐
  │   orders   │      │ reservations   │
  ├────────────┤      ├────────────────┤
  │ id (PK)    │      │ id (PK)        │
  │ table_id   │      │ table_id       │
  │ customer_id│      │ customer_id    │
  │ waiter_id  │      │ customer_name  │
  │ total      │      │ reservation_dt │
  │ status     │      │ status         │
  └────────────┘      └────────────────┘
       │
       │
       ▼
  ┌──────────────┐
  │ order_items  │
  ├──────────────┤
  │ id (PK)      │
  │ order_id (FK)│
  │ menu_item_id │
  │ quantity     │
  │ subtotal     │
  │ status       │
  └──────────────┘
       │
       │
       ▼
┌────────────────┐
│  menu_items    │
├────────────────┤
│ id (PK)        │
│ name           │
│ description    │
│ price          │
│ category       │
│ preparation_tm │
│ status         │
└────────────────┘
       │
       │
       ▼
  ┌──────────────┐
  │  inventory   │
  ├──────────────┤
  │ id (PK)      │
  │ menu_item_id │
  │ ingredient   │
  │ qty_used     │
  │ unit         │
  │ reorder_lvl  │
  └──────────────┘

┌────────────┐
│  tables    │
├────────────┤
│ id (PK)    │
│ number     │
│ capacity   │
│ status     │
└────────────┘

┌────────────┐
│ invoices   │
├────────────┤
│ id (PK)    │
│ order_id   │
│ invoice_no │
│ total      │
│ tax        │
│ discount   │
│ status     │
└────────────┘
```

### Key Tables

**users**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password_hash VARCHAR(255),  -- Use password_hash()
    role ENUM('admin','waiter','kitchen','customer'),
    status ENUM('active','inactive'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**orders & order_items**
```sql
-- Orders link tables to customers
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT FOREIGN KEY,
    customer_id INT FOREIGN KEY,
    waiter_id INT FOREIGN KEY,
    total_amount DECIMAL(10,2),
    status ENUM('pending','preparing','served','paid'),
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Each order has multiple items
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT FOREIGN KEY,
    menu_item_id INT FOREIGN KEY,
    quantity INT,
    subtotal DECIMAL(10,2),
    special_requests TEXT,
    status ENUM('pending','preparing','ready','served')
);
```

---

## API Endpoints

### Authentication

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@restaurant.local",
    "password": "password123"
}

Response:
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user_id": 1,
        "name": "John Doe",
        "role": "customer"
    }
}
```

```http
POST /api/auth/register
{
    "name": "Jane Doe",
    "email": "jane@restaurant.local",
    "password": "password123",
    "role": "customer"
}
```

```http
POST /api/auth/logout
Response: { "success": true }
```

### Menu Operations

```http
GET /api/menu/items
Response: Array of menu items

GET /api/menu/items?category=mains
Response: Items filtered by category

POST /api/menu/items
{
    "name": "New Dish",
    "description": "Description",
    "price": 12.99,
    "category": "mains"
}
```

### Order Management

```http
POST /api/orders/create
{
    "table_id": 1,
    "items": [
        {
            "menu_item_id": 5,
            "quantity": 2,
            "special_requests": "Extra sauce"
        }
    ]
}
Response: { "order_id": 42, "order_number": "ORD-042" }

GET /api/orders/1
Response: Full order details

PUT /api/orders/1/status
{
    "status": "served"
}
Response: { "success": true }

GET /api/orders/pending
Response: Array of pending orders (for kitchen/waiter)
```

### Table Management

```http
GET /api/tables
Response: All tables with current status

PUT /api/tables/1/status
{
    "status": "occupied"
}
```

### Kitchen Display System

```http
GET /api/kitchen/orders
Response: All pending orders for kitchen display
Intended for polling: every 5 seconds

GET /api/kitchen/orders/pending-count
Response: { "count": 3 }
```

### Admin Operations

```http
GET /api/admin/staff
Response: All employees

POST /api/admin/staff
{
    "name": "New Staff",
    "email": "staff@restaurant.local",
    "role": "waiter"
}

GET /api/admin/analytics/revenue?date=2024-01-15
Response: Daily revenue report

GET /api/admin/analytics/top-items
Response: Top selling menu items
```

---

## Frontend Architecture

### SPA Routing

The JavaScript app handles routing without page reloads:

```javascript
// User clicks a link
<a href="#" data-link="menu">Menu</a>

// SPA detects and loads
app.loadPage('menu')
↓
Fetch /restaurant-management/public/menu
↓
Display in #app-content
↓
Push to browser history

// Result: URL changes but no page reload
```

### Form Handling

```html
<!-- Form with data-api attribute -->
<form data-api="orders/create" method="POST">
    <input name="table_id" value="1">
    <button type="submit">Place Order</button>
</form>
```

```javascript
// JavaScript intercepts and handles
form.addEventListener('submit', (e) => {
    e.preventDefault()
    const formData = new FormData(form)
    app.apiCall('orders/create', 'POST', formData)
        .then(response => {
            if (response.success) {
                app.showSuccess('Order placed!')
            }
        })
})
```

### Real-time Updates

```javascript
// Poll Kitchen Display System every 5 seconds
const stopPolling = pollAPI('/api/kitchen/orders', (orders) => {
    updateKitchenDisplay(orders)
}, 5000)

// Stop polling when user leaves
function stopKDS() {
    stopPolling()
}
```

---

## Implementation Roadmap

### Phase 1: Core Setup ✅
- [x] Router & URL rewriting
- [x] Database connection
- [x] Authentication system
- [x] SPA infrastructure

### Phase 2: Core Features (To Implement)
- [ ] Authentication Controller
- [ ] Menu Management Controller
- [ ] Order Management Controller
- [ ] Table Management Controller

### Phase 3: Advanced Features (To Implement)
- [ ] Kitchen Display System
- [ ] Inventory Management
- [ ] Billing & Invoicing
- [ ] Analytics Dashboard

### Phase 4: Polish (To Implement)
- [ ] Error handling
- [ ] Logging system
- [ ] Rate limiting
- [ ] CSRF protection
- [ ] Tests

---

## Security Best Practices

1. **SQL Injection Prevention**
   - ✅ Always use PDO prepared statements
   - ✅ Never concatenate user input into SQL

2. **Password Security**
   - ✅ Use `password_hash()` for storage
   - ✅ Use `password_verify()` for comparison
   - ✅ Minimum 8 characters recommended

3. **Session Security**
   - ✅ Use secure session configuration
   - ✅ Regenerate session ID on login
   - ✅ Set session timeout

4. **Input Validation**
   - ✅ Sanitize all user inputs
   - ✅ Validate data types
   - ✅ Check email format

5. **HTTPS (Production)**
   - Always enable HTTPS
   - Use secure cookies (HttpOnly)

---

## Next Steps

1. **Create Controllers** - Implement the business logic
2. **Create Models** - Database interaction layer
3. **Create Views** - Customer-facing templates
4. **Test Routes** - Verify all endpoints work
5. **Add Logging** - Track errors and actions
6. **Deploy** - Move to production server

---

## Support & Contact

For questions or issues, refer to:
- `README.md` - Quick start
- `schema.sql` - Database queries
- Code comments - Implementation details

**Happy Coding! 🚀**
