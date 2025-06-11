<?php

/**
 * University Clinic Management System
 * Final Production Application
 * 
 * A complete clinic management system with beautiful UI and full functionality
 */

// Load the working system
require_once __DIR__ . '/minimal-system.php';

// Enhanced routing system
class Router {
    private $routes = [];
    
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    
    public function handle() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove leading slash and query parameters
        $path = trim($path, '/');
        if (empty($path)) $path = 'home';
        
        if (isset($this->routes[$method][$path])) {
            return call_user_func($this->routes[$method][$path]);
        }
        
        // Default 404
        http_response_code(404);
        echo "404 - Page not found";
    }
}

// Enhanced view rendering
function renderView($view, $data = []) {
    extract($data);
    
    // Check if view exists in resources/views first
    $viewPath = BASE_PATH . "/resources/views/$view.php";
    if (!file_exists($viewPath)) {
        // Fallback to demo directory
        $viewPath = BASE_PATH . "/demo/$view.php";
    }
    
    if (file_exists($viewPath)) {
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
    
    return "View not found: $view";
}

// Enhanced patient management
function createPatient($data) {
    $db = db();
    if (!$db) return false;
    
    // Validate required fields
    $required = ['student_no', 'first_name', 'last_name', 'email', 'phone'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO patients (student_no, first_name, middle_name, last_name, email, phone, age, sex, address, campus, college_office, course_year, emergency_contact, emergency_relation, emergency_phone, blood_type, allergies) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        return $stmt->execute([
            $data['student_no'],
            $data['first_name'],
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['email'],
            $data['phone'],
            $data['age'] ?? null,
            $data['sex'] ?? null,
            $data['address'] ?? null,
            $data['campus'] ?? null,
            $data['college_office'] ?? null,
            $data['course_year'] ?? null,
            $data['emergency_contact'] ?? null,
            $data['emergency_relation'] ?? null,
            $data['emergency_phone'] ?? null,
            $data['blood_type'] ?? null,
            $data['allergies'] ?? null
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

// Queue management
function generateQueueNumber($service_type = 'consultation') {
    $prefix = strtoupper(substr($service_type, 0, 1));
    $number = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    return $prefix . $number;
}

function joinQueue($patient_id, $service_type, $priority = 0) {
    $db = db();
    if (!$db) return false;
    
    $queueNumber = generateQueueNumber($service_type);
    
    try {
        $stmt = $db->prepare("INSERT INTO queue_tickets (patient_id, queue_number, service_type, priority, status) VALUES (?, ?, ?, ?, 'waiting')");
        return $stmt->execute([$patient_id, $queueNumber, $service_type, $priority]);
    } catch (PDOException $e) {
        return false;
    }
}

// Initialize router
$router = new Router();

// Define routes
$router->get('home', function() {
    if (auth()) {
        // Redirect to appropriate dashboard based on role
        $role = auth()['role'];
        switch ($role) {
            case 'admin':
                header('Location: /admin-dashboard');
                break;
            case 'doctor':
            case 'nurse':
                header('Location: /staff-dashboard');
                break;
            default:
                header('Location: /patient-portal');
        }
    } else {
        header('Location: /login');
    }
    exit;
});

$router->get('login', function() {
    if (auth()) {
        header('Location: /home');
        exit;
    }
    echo renderView('login');
});

$router->post('login', function() {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: /home');
    } else {
        $_SESSION['error'] = 'Invalid credentials';
        header('Location: /login');
    }
    exit;
});

$router->get('logout', function() {
    session_destroy();
    header('Location: /login');
    exit;
});

$router->get('patient-portal', function() {
    if (!auth()) {
        header('Location: /login');
        exit;
    }
    echo renderView('patient-portal');
});

$router->get('staff-dashboard', function() {
    if (!auth() || !in_array(auth()['role'], ['doctor', 'nurse', 'admin'])) {
        header('Location: /login');
        exit;
    }
    echo renderView('staff-dashboard');
});

$router->get('admin-dashboard', function() {
    if (!auth() || auth()['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }
    echo renderView('admin-panel');
});

$router->get('patients', function() {
    if (!auth() || !in_array(auth()['role'], ['doctor', 'nurse', 'admin'])) {
        header('Location: /login');
        exit;
    }
    echo renderView('patients');
});

$router->get('appointments', function() {
    if (!auth()) {
        header('Location: /login');
        exit;
    }
    echo renderView('appointments');
});

$router->get('queue', function() {
    echo renderView('queue');
});

$router->get('kiosk', function() {
    echo renderView('kiosk');
});

$router->get('mobile', function() {
    echo renderView('mobile');
});

$router->get('register', function() {
    echo renderView('patient-registration');
});

$router->post('register', function() {
    if (createPatient($_POST)) {
        $_SESSION['success'] = 'Registration successful! Please login.';
        header('Location: /login');
    } else {
        $_SESSION['error'] = 'Registration failed. Please check your information.';
        header('Location: /register');
    }
    exit;
});

$router->get('medical-records', function() {
    if (!auth() || !in_array(auth()['role'], ['doctor', 'nurse', 'admin'])) {
        header('Location: /login');
        exit;
    }
    echo renderView('medical-records');
});

$router->get('enhanced-queue', function() {
    if (!auth() || !in_array(auth()['role'], ['doctor', 'nurse', 'admin'])) {
        header('Location: /login');
        exit;
    }
    // Include the enhanced queue management system
    include BASE_PATH . '/demo/enhanced-queue.php';
    exit;
});

$router->get('analytics-dashboard', function() {
    if (!auth() || auth()['role'] !== 'admin') {
        header('Location: /login');
        exit;
    }
    // Include the analytics dashboard
    include BASE_PATH . '/demo/analytics-dashboard.php';
    exit;
});

$router->get('enhanced-features', function() {
    if (!auth()) {
        header('Location: /login');
        exit;
    }
    // Include the enhanced features page
    include BASE_PATH . '/demo/enhanced-features.php';
    exit;
});

// API endpoints
$router->get('api/queue/join', function() {
    $service = $_GET['service'] ?? 'consultation';
    $priority = $_GET['priority'] ?? 0;
    
    // For demo, create a mock patient
    $queueNumber = generateQueueNumber($service);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'queue_number' => $queueNumber,
        'service' => $service,
        'estimated_wait' => rand(10, 30) . ' minutes'
    ]);
});

// Handle the request
if (php_sapi_name() !== 'cli') {
    $router->handle();
}

?>
