<?php
class AuthController {
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Method not allowed', 'code' => 405];
        }

        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($email === '' || $password === '') {
            return ['success' => false, 'message' => 'Email and password are required', 'code' => 400];
        }

        $user = fetchOne("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1", [$email]);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid email or password', 'code' => 401];
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        return [
            'success' => true,
            'message' => 'Login successful',
            'code' => 200,
            'redirect' => 'dashboard',
            'data' => [
                'user_id' => (int)$user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ];
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['success' => false, 'message' => 'Method not allowed', 'code' => 405];
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : ROLE_CUSTOMER;

        if ($name === '' || $email === '' || $password === '') {
            return ['success' => false, 'message' => 'Name, email, and password are required', 'code' => 400];
        }

        if (!isValidEmail($email)) {
            return ['success' => false, 'message' => 'Invalid email format', 'code' => 400];
        }

        if (strlen($password) < MIN_PASSWORD_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters', 'code' => 400];
        }

        $allowedRoles = [ROLE_ADMIN, ROLE_WAITER, ROLE_KITCHEN, ROLE_CUSTOMER];
        if (!in_array($role, $allowedRoles, true)) {
            $role = ROLE_CUSTOMER;
        }

        if (recordExists('users', 'email', $email)) {
            return ['success' => false, 'message' => 'Email already exists', 'code' => 409];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        executeQuery(
            'INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)',
            [$name, $email, $passwordHash, $role, 'active']
        );

        return [
            'success' => true,
            'message' => 'Registration successful',
            'code' => 201,
            'redirect' => 'login',
        ];
    }

    public function logout() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
        }

        return [
            'success' => true,
            'message' => 'Logout successful',
            'code' => 200,
            'redirect' => 'login',
        ];
    }
}
?>
