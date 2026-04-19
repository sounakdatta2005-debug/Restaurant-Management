<?php
/**
 * Restaurant Management System - Main Router
 * Core PHP, MySQL (PDO), Vanilla JavaScript SPA
 */

// Error handling
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Enable output compression
ob_start();

// Start session
session_start();

// Define constants
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('APP_PATH', ROOT_PATH . '/app');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('VIEW_PATH', APP_PATH . '/views');
define('CONTROLLER_PATH', APP_PATH . '/controllers');
define('MODEL_PATH', APP_PATH . '/models');
define('MIDDLEWARE_PATH', APP_PATH . '/middleware');

// Load configuration
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';

// ============================================
// ROUTER CLASS
// ============================================
class Router {
    private $route;
    private $method;
    private $segments = [];
    private $isAPI = false;
    private $response = [];
    private $basePath = '';

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        $this->parseRoute();
    }

    /**
     * Parse the URI and extract route segments
     */
    private function parseRoute() {
        // Prefer explicit route parameter, then fall back to the request URI.
        $route = isset($_GET['route']) ? trim($_GET['route'], '/') : '';

        if ($route === '') {
            $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
            $requestUri = str_replace('\\', '/', $requestUri);

            if ($this->basePath !== '' && strpos($requestUri, $this->basePath) === 0) {
                $requestUri = substr($requestUri, strlen($this->basePath));
            }

            $route = trim($requestUri, '/');
        }
        
        // Check if it's an API endpoint
        if (strpos($route, 'api/') === 0) {
            $this->isAPI = true;
            $route = substr($route, 4); // Remove 'api/' prefix
        }

        $this->route = $route;
        
        // Split route into segments
        $this->segments = array_filter(explode('/', $route));
    }

    /**
     * Route the request to appropriate controller and action
     */
    public function dispatch() {
        // Extract controller and action from segments
        $controller = isset($this->segments[0]) ? ucfirst($this->segments[0]) : 'Dashboard';
        $action = isset($this->segments[1]) ? $this->segments[1] : 'index';

        // Check if user is authenticated for certain routes
        $publicRoutes = ['login', 'register', 'auth/login', 'auth/register'];
        $routeKey = strtolower($this->route);
        $controllerActionKey = strtolower($controller . '/' . $action);
        $isPublicRoute = in_array($routeKey, $publicRoutes, true) || in_array($controllerActionKey, $publicRoutes, true);

        if (!$isPublicRoute && !$this->isUserAuthenticated()) {
            if ($this->isAPI) {
                $this->jsonResponse(['success' => false, 'message' => 'Unauthorized', 'code' => 401], 401);
            } else {
                header('Location: ' . $this->basePath . '/login');
            }
            exit;
        }

        try {
            if ($this->isAPI) {
                header('Content-Type: application/json; charset=utf-8');
                $this->handleAPIRoute($controller, $action);
            } else {
                header('Content-Type: text/html; charset=utf-8');
                $this->handleViewRoute($controller, $action);
            }
        } catch (Exception $e) {
            if ($this->isAPI) {
                $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
            } else {
                $this->renderError($e->getMessage());
            }
        }
    }

    /**
     * Handle view routes (page requests)
     */
    private function handleViewRoute($controller, $action) {
        $viewFile = VIEW_PATH . '/' . strtolower($controller) . '/' . $action . '.php';

        // Check if view exists
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: $controller/$action");
        }

        // Load layout
        $this->loadLayout($viewFile);
    }

    /**
     * Handle API routes (JSON responses)
     */
    private function handleAPIRoute($controller, $action) {
        $controllerFile = CONTROLLER_PATH . '/' . $controller . 'Controller.php';

        // Check if controller exists
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller not found: $controller");
        }

        require_once $controllerFile;
        $controllerClass = $controller . 'Controller';

        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class not found: $controllerClass");
        }

        $controllerInstance = new $controllerClass();

        // Check if method exists
        if (!method_exists($controllerInstance, $action)) {
            throw new Exception("Method not found: $action in $controllerClass");
        }

        // Call controller method and get response
        $response = $controllerInstance->$action();
        
        $this->jsonResponse($response);
    }

    /**
     * Load main layout with content
     */
    private function loadLayout($contentView) {
        $headerFile = VIEW_PATH . '/layouts/header.php';
        $footerFile = VIEW_PATH . '/layouts/footer.php';

        if (!file_exists($headerFile) || !file_exists($footerFile)) {
            require_once $contentView;
            return;
        }

        require_once $headerFile;
        require_once $contentView;
        require_once $footerFile;
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Check if user is authenticated
     */
    private function isUserAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Render error page
     */
    private function renderError($message) {
        http_response_code(404);
        echo "<!DOCTYPE html>";
        echo "<html><head><title>Error</title></head>";
        echo "<body><h1>Error</h1><p>$message</p></body>";
        echo "</html>";
        exit;
    }
}

// ============================================
// INITIALIZE ROUTER
// ============================================
$router = new Router();
$router->dispatch();
?>
