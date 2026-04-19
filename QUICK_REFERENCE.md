# Restaurant Management System - Quick Reference Guide

## 📌 Essential Commands & Code Snippets

---

## 🗄️ Database Commands

### Import Schema
```bash
# Via command line
mysql -u root -p restaurant_management < schema.sql

# Via phpMyAdmin
1. Go to http://localhost/phpmyadmin
2. Select restaurant_management database
3. Import → Select schema.sql → Go
```

### Connect to Database
```bash
mysql -u root -p restaurant_management
```

### Check Database
```sql
-- List tables
SHOW TABLES;

-- Check users
SELECT * FROM users;

-- Check orders
SELECT * FROM orders;

-- Check menu items
SELECT * FROM menu_items;
```

---

## 🚀 Basic Routing

### URL Structure
```
No query strings:              /page-name
With segments:                 /controller/action/id
API endpoints:                 /api/resource/action

Examples:
/dashboard                     → views/dashboard.php
/waiter/orders                 → views/waiter/orders.php
/api/menu/items                → MenuController::items()
/api/orders/1/status           → OrderController::updateStatus()
```

### .htaccess Rewriting
```
Request: http://localhost/restaurant-management/public/menu
↓
.htaccess detects no file/directory
↓
Rewrites to: index.php?route=menu
↓
Router parses and loads content
```

---

## 🔐 Authentication

### Login Route
```
POST /api/auth/login
```

### Register Route
```
POST /api/auth/register
```

### Logout Route
```
POST /api/auth/logout
```

### Session Access
```php
// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $userRole = $_SESSION['role'];
}

// Set session (in controller)
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['name'] = $user['name'];

// Destroy session (logout)
session_destroy();
```

---

## 💾 Database Helper Functions

### Fetch All Records
```php
$users = fetchAll("SELECT * FROM users WHERE status = ?", ['active']);
```

### Fetch Single Record
```php
$user = fetchOne("SELECT * FROM users WHERE id = ?", [1]);
```

### Execute Query
```php
$stmt = executeQuery(
    "INSERT INTO users (name, email) VALUES (?, ?)",
    ['John Doe', 'john@example.com']
);
```

### Check Record Exists
```php
if (recordExists('users', 'email', 'john@example.com')) {
    echo "User exists";
}
```

### Get Last Insert ID
```php
$newId = getLastInsertId();
```

---

## 🎨 Frontend - SPA Methods

### Load a Page
```javascript
app.loadPage('dashboard');
app.loadPage('waiter/orders');
app.loadPage('admin/staff');
```

### Make API Call
```javascript
// GET request
app.apiCall('menu/items', 'GET').then(response => {
    console.log(response.data);
});

// POST request
app.apiCall('orders/create', 'POST', {
    table_id: 1,
    items: [{ menu_item_id: 5, quantity: 2 }]
}).then(response => {
    if (response.success) {
        app.showSuccess('Order created!');
    }
});

// PUT request
app.apiCall('orders/1/status', 'PUT', {
    status: 'served'
});
```

### Show Notifications
```javascript
app.showSuccess('Operation successful!');
app.showError('Something went wrong!');
app.showNotification('Message', 'info');
```

### Handle Forms
```html
<!-- Automatic API submission -->
<form data-api="orders/create" method="POST">
    <input type="hidden" name="table_id" value="1">
    <button type="submit">Create Order</button>
</form>

<!-- Form will automatically be sent to /api/orders/create -->
```

### Poll for Real-Time Updates (KDS)
```javascript
// Start polling
const stopPolling = pollAPI('/api/kitchen/orders', (orders) => {
    console.log('New orders:', orders);
}, 5000); // Poll every 5 seconds

// Stop polling when needed
function stopKDS() {
    stopPolling();
}
```

---

## 📋 Creating a New Feature

### Step 1: Create Model
**File**: `app/models/Feature.php`
```php
<?php
class Feature {
    public function __construct() { }
    
    public function getAll() {
        return fetchAll("SELECT * FROM feature_table");
    }
    
    public function getById($id) {
        return fetchOne("SELECT * FROM feature_table WHERE id = ?", [$id]);
    }
    
    public function create($data) {
        executeQuery(
            "INSERT INTO feature_table (name, description) VALUES (?, ?)",
            [$data['name'], $data['description']]
        );
        return getLastInsertId();
    }
}
?>
```

### Step 2: Create Controller
**File**: `app/controllers/FeatureController.php`
```php
<?php
class FeatureController {
    public function index() {
        $model = new Feature();
        $data = $model->getAll();
        return [
            'success' => true,
            'data' => $data
        ];
    }
    
    public function create() {
        $model = new Feature();
        $id = $model->create($_POST);
        return [
            'success' => true,
            'id' => $id,
            'message' => 'Feature created'
        ];
    }
}
?>
```

### Step 3: Create View
**File**: `app/views/feature/index.php`
```php
<h1>Features</h1>
<div id="features-container">
    <!-- Content loads here -->
</div>

<script>
    // Load data when page loads
    app.apiCall('feature/index', 'GET').then(response => {
        if (response.success) {
            const container = document.getElementById('features-container');
            container.innerHTML = JSON.stringify(response.data);
        }
    });
</script>
```

### Step 4: Access
```
View:  http://localhost/restaurant-management/public/feature
API:   http://localhost/restaurant-management/public/api/feature/index
```

---

## 🔒 Security Patterns

### SQL Injection Prevention
```php
// ❌ WRONG - Never do this
$sql = "SELECT * FROM users WHERE email = '" . $email . "'";

// ✅ CORRECT - Always use prepared statements
$user = fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
```

### Password Hashing
```php
// Create hash
$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Verify password
if (password_verify($_POST['password'], $storedHash)) {
    // Password is correct
}
```

### Input Sanitization
```php
// Escape HTML
$safe = sanitize($userInput);

// Validate email
if (isValidEmail($email)) {
    // Valid email
}
```

### Role-Based Access
```php
// Check single role
if (hasRole(ROLE_ADMIN)) {
    // Admin only
}

// Check multiple roles
if (hasAnyRole([ROLE_ADMIN, ROLE_WAITER])) {
    // Admin or Waiter
}
```

---

## 📊 Common Database Queries

### Get Daily Revenue
```sql
SELECT 
    DATE(order_time) as date,
    SUM(total_amount) as revenue,
    COUNT(*) as orders
FROM orders
WHERE status = 'paid'
GROUP BY DATE(order_time)
ORDER BY date DESC;
```

### Get Top Selling Items
```sql
SELECT 
    mi.name,
    SUM(oi.quantity) as sold,
    SUM(oi.subtotal) as revenue
FROM order_items oi
JOIN menu_items mi ON oi.menu_item_id = mi.id
GROUP BY mi.id
ORDER BY sold DESC
LIMIT 10;
```

### Get Pending Orders
```sql
SELECT 
    o.id,
    o.order_number,
    t.table_number,
    o.order_time,
    COUNT(oi.id) as items
FROM orders o
JOIN tables t ON o.table_id = t.id
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE o.status IN ('pending', 'preparing')
GROUP BY o.id
ORDER BY o.order_time ASC;
```

### Get Table Occupancy
```sql
SELECT 
    t.table_number,
    t.capacity,
    t.status,
    o.order_number,
    o.order_time
FROM tables t
LEFT JOIN orders o ON t.id = o.table_id 
    AND o.status IN ('pending', 'preparing', 'served')
ORDER BY t.table_number;
```

### Get Low Inventory
```sql
SELECT 
    mi.name,
    i.ingredient_name,
    i.quantity_used,
    i.minimum_stock,
    i.reorder_level
FROM inventory i
JOIN menu_items mi ON i.menu_item_id = mi.id
WHERE i.quantity_used < i.reorder_level
ORDER BY i.reorder_level DESC;
```

---

## 🧪 Testing Routes

### Test Login
```bash
curl -X POST http://localhost/restaurant-management/public/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@restaurant.local","password":"password"}'
```

### Test Menu Items
```bash
curl http://localhost/restaurant-management/public/api/menu/items
```

### Test Create Order
```bash
curl -X POST http://localhost/restaurant-management/public/api/orders/create \
  -H "Content-Type: application/json" \
  -d '{
    "table_id": 1,
    "items": [{"menu_item_id": 1, "quantity": 2}]
  }'
```

---

## 📱 Frontend HTML Templates

### Navigation Link
```html
<a href="#" data-link="page-name">Page Name</a>
```

### Form with API
```html
<form data-api="endpoint/here" method="POST">
    <input type="text" name="fieldname" required>
    <button type="submit">Submit</button>
</form>
```

### Loading Indicator
```html
<div id="loader">
    <div class="spinner"></div>
</div>
```

### Card Component
```html
<div class="card">
    <div class="card-header">
        <h3>Title</h3>
    </div>
    <div class="card-body">
        Content here
    </div>
</div>
```

### Notification
```html
<div class="alert alert-success">
    Success message
</div>

<div class="alert alert-error">
    Error message
</div>
```

### Status Badge
```html
<span class="badge badge-green">Available</span>
<span class="badge badge-red">Occupied</span>
<span class="badge badge-orange">Pending</span>
```

---

## 🚀 Performance Tips

### Database Optimization
```php
// Use LIMIT for large datasets
fetchAll("SELECT * FROM orders LIMIT 50");

// Add WHERE conditions
fetchAll("SELECT * FROM orders WHERE status = ? AND created_at > ?", 
    ['pending', date('Y-m-d')]);

// Use appropriate indexes
CREATE INDEX idx_status ON orders(status);
```

### Frontend Optimization
```javascript
// Debounce search
const search = debounce((query) => {
    app.apiCall('search', 'GET', { q: query });
}, 500);

// Throttle scroll
window.addEventListener('scroll', throttle(() => {
    // Load more on scroll
}, 1000));
```

---

## 💡 Debugging

### Check Errors
```php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check last error
$error = error_get_last();
```

### Log Errors
```php
file_put_contents(
    'logs/error.log',
    date('Y-m-d H:i:s') . ': ' . $message . "\n",
    FILE_APPEND
);
```

### Browser Console
```javascript
// Open browser DevTools (F12)
// Check Console tab for JavaScript errors
// Check Network tab for API calls
console.log('Debug:', data);
```

---

## 📞 Quick Troubleshooting

| Issue | Solution |
|-------|----------|
| 404 errors | Check .htaccess is in public/ folder |
| Database connection error | Verify credentials in config/database.php |
| Session not working | Check PHP session.save_path permissions |
| Routes not loading | Ensure mod_rewrite is enabled in Apache |
| API returns 401 | User not logged in, use login endpoint first |
| Styles not loading | Check APP_URL constant matches actual URL |

---

## 🎓 File Locations Reference

| What | Where |
|------|-------|
| Database config | `config/database.php` |
| App constants | `config/config.php` |
| Main router | `public/index.php` |
| SPA JavaScript | `public/js/app.js` |
| Utility functions | `public/js/utils.js` |
| CSS styling | `public/css/style.css` |
| URL rewriting | `public/.htaccess` |
| Database schema | `schema.sql` |
| Models folder | `app/models/` |
| Controllers folder | `app/controllers/` |
| Views folder | `app/views/` |

---

## 📚 File Examples

### config/database.php Usage
```php
require 'config/database.php';

// Now you can use:
$users = fetchAll("SELECT * FROM users");
$user = fetchOne("SELECT * FROM users WHERE id = ?", [1]);
executeQuery("UPDATE users SET status = ? WHERE id = ?", ['active', 1]);
```

### config/config.php Usage
```php
require 'config/config.php';

// Use constants:
if (hasRole(ROLE_ADMIN)) {
    echo "Welcome " . sanitize($_GET['name']);
}
```

---

## ✅ Checklist for New Implementation

- [ ] Create model with database methods
- [ ] Create controller with API endpoints
- [ ] Create view with HTML/JavaScript
- [ ] Test route with browser/curl
- [ ] Add error handling
- [ ] Add input validation
- [ ] Add security checks
- [ ] Add comments
- [ ] Test all scenarios

---

**Remember**: Always reference the full documentation and use these snippets as starting points!

*Happy Coding! 🚀*
