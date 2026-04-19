# Restaurant Management System - Architecture Diagrams

## 1. Request Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                          USER INTERACTION                           │
│  (Browser) Clicks Link → JavaScript detects → Fetch API calls       │
└─────────────────────────────────────────────────────────────────────┘
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│                      URL REWRITING (.htaccess)                      │
│  /menu  →  /index.php?route=menu                                    │
│  /api/orders/create  →  /index.php?route=api/orders/create          │
└─────────────────────────────────────────────────────────────────────┘
                               ↓
┌─────────────────────────────────────────────────────────────────────┐
│                    ROUTER (public/index.php)                        │
│  Parses route → Determines controller/action → Routes request       │
└─────────────────────────────────────────────────────────────────────┘
                           ↙   ↖
                      /           \
                     /               \
                    ↙                 ↖
         ┌──────────────────┐   ┌──────────────────┐
         │   IS API CALL?   │   │   IS VIEW?       │
         └──────────────────┘   └──────────────────┘
                ↓                        ↓
    ┌──────────────────────┐   ┌── ──────────────────┐
    │  Load Controller     │   │  Load View          │
    │  Execute Method      │   │  Include Layout     │
    │  Return JSON         │   │  Return HTML        │
    └──────────────────────┘   └─────────────────────┘
                ↓                        ↓
    ┌──────────────────────┐   ┌─────────────────────┐
    │  Model Query PDO     │   │  Display on Page    │
    │  Database Operation  │   │  No Reload          │
    └──────────────────────┘   └─────────────────────┘
                ↓                        ↓
    ┌──────────────────────┐   ┌─────────────────────┐
    │  Return to JS        │   │  Fade Effect        │
    │  JSON Response       │   │  Update URL         │
    └──────────────────────┘   └─────────────────────┘
                ↓                        ↓
         ┌──────────────────────────────────────┐
         │   JavaScript Processes Response      │
         │   Update DOM / Show Notification     │
         └──────────────────────────────────────┘
                        ↓
         ┌──────────────────────────────────────┐
         │   User Sees Updated Content          │
         │   Without Page Reload (SPA Effect)   │
         └──────────────────────────────────────┘
```

---

## 2. Database Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                       MySQL Database                            │
│                 restaurant_management                           │
└─────────────────────────────────────────────────────────────────┘
        │
        ├──────────────────────────────────┬────────────────────┐
        │                                  │                    │
    ┌───┴────┐                      ┌─────┴──┐         ┌────────┴────┐
    │ users  │                      │ tables │         │ menu_items  │
    ├────────┤                      ├────────┤         ├─────────────┤
    │ id     ├─────────────┬────────┤ id     │         │ id          │
    │ name   │             │        │ number │         │ name        │
    │ email  │             │        │ status │         │ price       │
    │ role   │             │        │        │         │ category    │
    └────────┘             │        └────────┘         └─────────────┘
        │                  │                                  │
        │                  │                                  │
        ├──────┬───────────┴──────────┐                      │
        │      │                      │                      │
    ┌───┴──────┴──┐          ┌────────┴─────┐               │
    │   orders    │          │ reservations │               │
    ├─────────────┤          ├──────────────┤               │
    │ id          │          │ id           │               │
    │ table_id────┼──────────│ table_id     │               │
    │ customer_id │          │ customer_id  │               │
    │ waiter_id   │          │ reserv_date  │               │
    │ total_amt   │          │ guests       │               │
    │ status      │          │ status       │               │
    └─────────────┘          └──────────────┘               │
        │                                                   │
        │                                                   │
    ┌───┴──────────────┐                                   │
    │  order_items     │                                   │
    ├──────────────────┤                                   │
    │ id               │                                   │
    │ order_id─────────┤                                   │
    │ menu_item_id─────┼───────────────────────────────────┤
    │ quantity         │
    │ subtotal         │
    │ special_requests │
    │ status           │
    └──────────────────┘
        │
        └──────────────┐
                       │
                  ┌────┴────────────┐
                  │  inventory      │
                  ├─────────────────┤
                  │ id              │
                  │ menu_item_id────┤
                  │ ingredient      │
                  │ qty_used        │
                  │ reorder_level   │
                  └─────────────────┘

Additional Tables:
┌──────────────┐    ┌─────────────────────┐    ┌──────────────────┐
│  invoices    │    │  activity_logs      │    │  reservations    │
├──────────────┤    ├─────────────────────┤    ├──────────────────┤
│ id           │    │ id                  │    │ id               │
│ order_id     │    │ user_id             │    │ table_id         │
│ invoice_no   │    │ action              │    │ customer_name    │
│ total        │    │ entity_type         │    │ customer_phone   │
│ tax          │    │ details (JSON)      │    │ reservation_date │
│ discount     │    │ ip_address          │    │ duration         │
│ payment_sts  │    │ created_at          │    │ guests           │
└──────────────┘    └─────────────────────┘    └──────────────────┘
```

---

## 3. MVC Architecture

```
┌────────────────────────────────────────────────────────────────────┐
│                     MODEL-VIEW-CONTROLLER                          │
└────────────────────────────────────────────────────────────────────┘

MODELS (app/models/)
├── User.php
│   ├── getAll()
│   ├── getById($id)
│   ├── create($data)
│   ├── update($id, $data)
│   └── delete($id)
├── Order.php
│   ├── getAll()
│   ├── getPending()
│   ├── create($data)
│   └── updateStatus($id, $status)
├── MenuItem.php
│   ├── getAll()
│   ├── getByCategory($category)
│   └── getAvailable()
└── [Other Models...]

         ↓ data flows ↑

CONTROLLERS (app/controllers/)
├── AuthController.php
│   ├── login()       → calls User model
│   ├── register()    → calls User model
│   └── logout()
├── OrderController.php
│   ├── index()       → calls Order model
│   ├── create()      → calls Order & OrderItem models
│   └── updateStatus()
├── MenuController.php
│   ├── index()       → calls MenuItem model
│   └── getByCategory()
└── [Other Controllers...]

         ↓ renders ↑

VIEWS (app/views/)
├── customer/
│   ├── menu.php       ← displays menu from controller
│   ├── orders.php
│   └── reservations.php
├── waiter/
│   ├── dashboard.php
│   └── orders.php
├── admin/
│   ├── analytics.php
│   └── staff.php
├── layouts/
│   ├── header.php
│   └── footer.php
└── [Other Views...]

         ↓ request ↑

ROUTER (public/index.php)
├── Parses URL
├── Routes to Controller
├── Controller uses Model
├── Model queries Database
├── Controller renders View
└── Response sent to user
```

---

## 4. API & Frontend Interaction

```
┌────────────────────────────────────────────────────────────────┐
│                     FRONTEND (JavaScript)                      │
│  • SPA with dynamic routing                                     │
│  • Handles all user interactions                                │
│  • Uses Fetch API for backend communication                     │
├────────────────────────────────────────────────────────────────┤
│ public/js/app.js                                                │
│  ├── Routing logic (app.loadPage)                               │
│  ├── API calls (app.apiCall)                                    │
│  ├── Form handling (form submission)                            │
│  ├── Notifications (success/error)                              │
│  └── Polling for real-time updates                              │
│                                                                  │
│ public/js/utils.js                                              │
│  ├── formatCurrency()                                           │
│  ├── formatDateTime()                                           │
│  ├── validateEmail()                                            │
│  ├── pollAPI() - for KDS                                        │
│  └── exportToCSV()                                              │
└────────────────────────────────────────────────────────────────┘
                            ↕ Fetch API
        ┌──────────────────────────────────────────────────┐
        │              HTTP REQUESTS                       │
        ├──────────────────────────────────────────────────┤
        │ GET  /api/menu/items                             │
        │ POST /api/orders/create                          │
        │ PUT  /api/orders/1/status                        │
        │ GET  /api/kitchen/orders                         │
        └──────────────────────────────────────────────────┘
                            ↕
┌────────────────────────────────────────────────────────────────┐
│                     BACKEND (PHP)                              │
│  • public/index.php (Router)                                    │
│  • app/controllers/ (Business Logic)                            │
│  • app/models/ (Data Layer)                                     │
│  • config/ (Configuration)                                      │
├────────────────────────────────────────────────────────────────┤
│ Request Flow:                                                   │
│ 1. Router receives request                                      │
│ 2. Routes to appropriate Controller                             │
│ 3. Controller instantiates Model                                │
│ 4. Model queries Database                                       │
│ 5. Model returns data to Controller                             │
│ 6. Controller formats response as JSON                          │
│ 7. Response sent back to Frontend                               │
└────────────────────────────────────────────────────────────────┘
                            ↕ PDO
        ┌──────────────────────────────────────────────────┐
        │           MySQL Database                         │
        │  • Secure prepared statements                    │
        │  • ACID transactions                             │
        │  • Foreign key relationships                     │
        │  • Proper indexing                               │
        └──────────────────────────────────────────────────┘
```

---

## 5. Authentication Flow

```
┌─────────────────────────────────────────────────────────────┐
│           AUTHENTICATION & AUTHORIZATION FLOW               │
└─────────────────────────────────────────────────────────────┘

User Enters Credentials
         ↓
  app.apiCall('auth/login', 'POST', {email, password})
         ↓
POST /api/auth/login
         ↓
Router → AuthController::login()
         ↓
  User model queries database
         ↓
Password verification with password_verify()
         ↓
    Password matches? ──NO──→ Return error
    │
    YES
    │
    ↓
Set $_SESSION variables:
├── user_id
├── role
├── name
└── email
    │
    ↓
Return JSON response with success
    │
    ↓
JavaScript receives response
    │
    ↓
Store user info (optional localStorage)
    │
    ↓
Redirect to dashboard
    │
    ↓
Now user can access protected routes:
├── Check session exists (middleware)
├── Check user role
├── Render appropriate dashboard
└── All API calls work (session persists)

LOGOUT Flow:
User clicks Logout
    ↓
POST /api/auth/logout
    ↓
session_destroy()
    ↓
Redirect to login page
    ↓
Next request → 401 Unauthorized
```

---

## 6. Order Processing Flow

```
┌─────────────────────────────────────────────────────────────┐
│              ORDER LIFECYCLE IN RESTAURANT                  │
└─────────────────────────────────────────────────────────────┘

CUSTOMER places order
    ↓
POST /api/orders/create
    ↓
OrderController::create()
    ├── Create order record (status: pending)
    ├── Add each item to order_items
    ├── Deduct inventory quantities
    ├── Calculate total amount
    └── Trigger inventory alerts if low
    ↓
Order appears in KITCHEN display
    ↓
KITCHEN receives order (polling /api/kitchen/orders)
    ├── See pending items
    ├── Prepare food
    └── Update item status to "ready"
    ↓
WAITER sees order ready
    ↓
WAITER serves order to table
    ├── PUT /api/orders/1/status → "served"
    ├── Update order_items status
    └── Update table status → "occupied"
    ↓
CUSTOMER finishes eating
    ↓
WAITER requests bill
    ├── Calculate final total
    ├── Apply discounts if any
    ├── Generate invoice
    └── Present to customer
    ↓
CUSTOMER pays
    ↓
PUT /api/orders/1/status → "paid"
    ├── Create invoice record
    ├── Update payment status
    ├── Update table status → "available"
    └── Log transaction
    ↓
Order complete
    ↓
ADMIN views analytics
    ├── Revenue reports
    ├── Top-selling items
    ├── Table occupancy
    └── Inventory status

CANCELLATION (anytime before served):
    ├── PUT /api/orders/1/status → "cancelled"
    ├── Restore inventory quantities
    ├── Free up table
    └── Log cancellation
```

---

## 7. Role-Based Access Control

```
┌─────────────────────────────────────────────────────────────┐
│           ROLE-BASED ACCESS CONTROL (RBAC)                  │
└─────────────────────────────────────────────────────────────┘

┌────────────┐          ┌──────────┐          ┌──────────┐
│   ADMIN    │          │ WAITER   │          │ KITCHEN  │
│            │          │          │          │          │
├────────────┤          ├──────────┤          ├──────────┤
│ Dashboard  │          │Dashboard │          │Dashboard │
│ Staff Mgmt │          │Orders    │          │Orders    │
│ Menu Mgmt  │          │Tables    │          │Pending   │
│ Analytics  │          │Billing   │          │Inventory │
│ Reports    │          │Profiles  │          │Status    │
│ Invoices   │          │          │          │Update    │
└────────────┘          └──────────┘          └──────────┘
     │                       │                      │
     │                       │                      │
     ├──────────────────────┼──────────────────────┤
     │                      │                      │
     ├─ CUSTOMER ─────────────────────────────────┤
     │   • Browse menu                              │
     │   • Place orders                             │
     │   • Book tables                              │
     │   • View order status                        │
     │   • Manage profile                           │
     └──────────────────────────────────────────────┘

Access Control Check:
   Request with userRole
         ↓
   Check hasRole(userRole)?
         ├─ NO → Return 401 Unauthorized
         │
         └─ YES → Allow access
```

---

## 8. Real-Time Kitchen Display System (KDS)

```
┌──────────────────────────────────────────────────────────────┐
│         KITCHEN DISPLAY SYSTEM - Real-time Polling            │
└──────────────────────────────────────────────────────────────┘

Kitchen Staff Views Orders Page
         ↓
JavaScript executes pollAPI()
         ↓
┌─ POLLING LOOP (every 5 seconds) ─┐
│                                   │
│  fetch GET /api/kitchen/orders    │
│  ↓                                │
│  displayOrders(data)              │
│  ↓                                │
│  Update DOM with new/updated data │
│  ↓                                │
│  Sound alert for new orders       │
│  (optional)                       │
│                                   │
│  Wait 5 seconds...                │
│  ↓                                │
└─ REPEAT ────────────────────────┘

Kitchen updates item status:
PUT /api/kitchen/orders/item/1/status?status=ready
     ↓
OrderItemController::updateStatus()
     ↓
All waiter dashboards polling /api/waiter/orders
     ↓
See updated status in real-time
     ↓
Waiter serves the order

Benefits:
✓ No page refresh needed
✓ Staff doesn't miss orders
✓ Real-time status updates
✓ Reduces confusion
✓ Improves efficiency
```

---

## 9. File Structure Tree

```
restaurant-management/
│
├── public/                          ← Web root (Apache DocumentRoot)
│   ├── index.php                    ← MAIN ENTRY POINT (Router)
│   ├── .htaccess                    ← URL rewriting
│   │
│   ├── js/
│   │   ├── app.js                   ← SPA engine
│   │   └── utils.js                 ← Helpers
│   │
│   ├── css/
│   │   └── style.css                ← Complete styling
│   │
│   └── assets/                      ← Images, docs
│
├── app/
│   ├── controllers/
│   │   ├── AuthController.php       ← Authentication
│   │   ├── MenuController.php       ← Menu operations
│   │   ├── OrderController.php      ← Order operations
│   │   ├── TableController.php      ← Table operations
│   │   ├── AdminController.php      ← Admin features
│   │   ├── WaiterController.php     ← Waiter features
│   │   ├── KitchenController.php    ← Kitchen Display
│   │   └── InventoryController.php  ← Inventory
│   │
│   ├── models/
│   │   ├── User.php                 ← User data layer
│   │   ├── Order.php                ← Order data layer
│   │   ├── MenuItem.php             ← Menu item data layer
│   │   ├── Table.php                ← Table data layer
│   │   ├── OrderItem.php            ← Order item data layer
│   │   ├── Inventory.php            ← Inventory data layer
│   │   ├── Invoice.php              ← Invoice data layer
│   │   └── Reservation.php          ← Reservation data layer
│   │
│   ├── middleware/
│   │   └── Auth.php                 ← Auth middleware
│   │
│   └── views/
│       ├── layouts/
│       │   ├── header.php           ← Navigation
│       │   └── footer.php           ← Footer
│       ├── customer/                ← Customer views
│       ├── waiter/                  ← Waiter views
│       ├── kitchen/                 ← Kitchen views
│       ├── admin/                   ← Admin views
│       ├── login.php                ← Login page
│       └── register.php             ← Registration
│
├── config/
│   ├── database.php                 ← PDO connection
│   └── config.php                   ← Constants
│
├── schema.sql                        ← Database schema
├── README.md                         ← Quick start
├── DOCUMENTATION.md                 ← Full docs
├── PROJECT_SUMMARY.md               ← Overview
└── QUICK_REFERENCE.md               ← Code snippets
```

---

## 10. Data Flow Visualization

```
┌─────────────────────────────────────────────────────────────┐
│                    COMPLETE DATA FLOW                       │
└─────────────────────────────────────────────────────────────┘

USER INTERACTION
       ↓
   HTML/CSS/JS Browser
       ↓
   app.js (SPA Router)
       ↓
   Fetch API
       ↓
   HTTP Request
       ↓
   public/index.php (PHP Router)
       ↓
   Parse URL & Route
       ↓
   Load Controller
       ↓
   Instantiate Model
       ↓
   config/database.php (PDO)
       ↓
   MySQL Query
       ↓
   Database
       ├── SELECT query results
       ├── INSERT/UPDATE/DELETE operations
       └── Transaction management
       ↓
   Return data to Model
       ↓
   Model processes data
       ↓
   Send to Controller
       ↓
   Controller formats JSON
       ↓
   Return Response
       ↓
   HTTP Response (JSON)
       ↓
   JavaScript processes
       ↓
   Update DOM
       ↓
   Display to User
       ↓
   SPA feels like modern app!
```

---

**These diagrams represent the complete architecture of your Restaurant Management System!**

Each layer is modular and can be extended independently.
