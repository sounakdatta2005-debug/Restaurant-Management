<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo APP_URL; ?>/assets/favicon.ico">
</head>
<body>
    <!-- Navigation Bar -->
    <header>
        <nav class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="font-weight: bold; font-size: 18px; color: var(--primary-color);">
                    🍽️ <?php echo APP_NAME; ?>
                </div>
                <div style="display: flex; gap: 0;">
                    <?php
                    $userRole = getUserRole();
                    if ($userRole === ROLE_ADMIN):
                    ?>
                        <a href="#" data-link="dashboard" class="<?php echo ($userRole === ROLE_ADMIN) ? 'active' : ''; ?>">Dashboard</a>
                        <a href="#" data-link="admin/staff">Staff</a>
                        <a href="#" data-link="admin/menu">Menu</a>
                        <a href="#" data-link="admin/analytics">Analytics</a>
                    <?php elseif ($userRole === ROLE_WAITER): ?>
                        <a href="#" data-link="waiter/dashboard" class="active">Dashboard</a>
                        <a href="#" data-link="waiter/orders">Orders</a>
                        <a href="#" data-link="waiter/tables">Tables</a>
                    <?php elseif ($userRole === ROLE_KITCHEN): ?>
                        <a href="#" data-link="kitchen/orders" class="active">Pending Orders</a>
                        <a href="#" data-link="kitchen/inventory">Inventory</a>
                    <?php else: ?>
                        <a href="#" data-link="menu" class="active">Menu</a>
                        <a href="#" data-link="reservations">Reservations</a>
                        <a href="#" data-link="orders">My Orders</a>
                    <?php endif; ?>
                    <a href="#" onclick="logout(); return false;">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Loading Indicator -->
    <div id="loader" style="display: none;">
        <div class="spinner"></div>
    </div>

    <!-- Main Content -->
    <main class="container">
        <div id="app-content">
            <!-- Content loads here dynamically -->
        </div>
    </main>

    <!-- Scripts -->
    <script src="<?php echo APP_URL; ?>/js/utils.js"></script>
    <script src="<?php echo APP_URL; ?>/js/app.js"></script>
    <script>
        /**
         * Logout function
         */
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                app.apiCall('auth/logout', 'POST').then(response => {
                    if (response.success) {
                        window.location.href = '<?php echo APP_URL; ?>/login';
                    }
                });
            }
        }
    </script>
</body>
</html>
