<?php

/**
 * Working Laravel Bootstrap
 * Complete Laravel-like environment without Composer dependencies
 */

// Start session and basic setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('LARAVEL_START', microtime(true));
define('BASE_PATH', __DIR__ . '/..');

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) return;
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, '"\'');
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

loadEnv(BASE_PATH . '/.env');

// Database connection with SQLite support
function getDbConnection() {
    $connection = $_ENV['DB_CONNECTION'] ?? 'sqlite';
    
    if ($connection === 'sqlite') {
        $database = $_ENV['DB_DATABASE'] ?? 'database/clinic_management.sqlite';
        $dbPath = BASE_PATH . '/' . $database;
        
        // Create database directory if it doesn't exist
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        try {
            $pdo = new PDO("sqlite:$dbPath");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            return null;
        }
    }
    
    // MySQL connection fallback
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $dbname = $_ENV['DB_DATABASE'] ?? 'clinic_management';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

// Create database tables if they don't exist
function createTables() {
    $db = getDbConnection();
    if (!$db) return false;
    
    // Users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(255) UNIQUE,
        email VARCHAR(255) UNIQUE,
        password VARCHAR(255),
        first_name VARCHAR(255),
        last_name VARCHAR(255),
        role VARCHAR(50) DEFAULT 'patient',
        phone VARCHAR(20),
        address TEXT,
        is_active BOOLEAN DEFAULT 1,
        avatar VARCHAR(255),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Patients table
    $db->exec("CREATE TABLE IF NOT EXISTS patients (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        student_employee_no VARCHAR(255) UNIQUE,
        first_name VARCHAR(255),
        middle_name VARCHAR(255),
        last_name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(20),
        address TEXT,
        age INTEGER,
        sex VARCHAR(10),
        campus VARCHAR(100),
        college_office VARCHAR(100),
        course_year_designation VARCHAR(255),
        emergency_contact_name VARCHAR(255),
        emergency_contact_relation VARCHAR(100),
        emergency_contact_phone VARCHAR(20),
        blood_type VARCHAR(10),
        allergies TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    
    // Appointments table
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER,
        doctor_id INTEGER,
        appointment_date DATE,
        appointment_time TIME,
        purpose TEXT,
        status VARCHAR(50) DEFAULT 'scheduled',
        notes TEXT,
        created_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id),
        FOREIGN KEY (doctor_id) REFERENCES users(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");
    
    // Queue tickets table
    $db->exec("CREATE TABLE IF NOT EXISTS queue_tickets (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER,
        queue_number VARCHAR(20),
        service_type VARCHAR(100),
        priority INTEGER DEFAULT 0,
        status VARCHAR(50) DEFAULT 'waiting',
        window_number INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        called_at DATETIME,
        completed_at DATETIME,
        FOREIGN KEY (patient_id) REFERENCES patients(id)
    )");
    
    // Medical records table
    $db->exec("CREATE TABLE IF NOT EXISTS medical_records (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER,
        doctor_id INTEGER,
        transaction_type VARCHAR(100),
        date_time DATETIME,
        height DECIMAL(5,2),
        weight DECIMAL(5,2),
        heart_rate INTEGER,
        respiratory_rate INTEGER,
        temperature DECIMAL(4,2),
        blood_pressure VARCHAR(20),
        pain_scale INTEGER,
        symptoms TEXT,
        diagnosis VARCHAR(255),
        treatment TEXT,
        notes TEXT,
        created_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (patient_id) REFERENCES patients(id),
        FOREIGN KEY (doctor_id) REFERENCES users(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");
    
    return true;
}

// Authentication helpers
function auth() {
    return $_SESSION['user'] ?? null;
}

function isAuthenticated() {
    return isset($_SESSION['user']);
}

function hasRole($role) {
    return isAuthenticated() && $_SESSION['user']['role'] === $role;
}

function login($user) {
    $_SESSION['user'] = $user;
    $_SESSION['authenticated'] = true;
}

function logout() {
    session_destroy();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Request helpers
function request($key = null, $default = null) {
    if ($key === null) {
        return array_merge($_GET, $_POST);
    }
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

// Response helpers
function redirect($url) {
    header("Location: $url");
    exit;
}

function back() {
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    redirect($referer);
}

function json($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Validation
function validate($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? null;
        $fieldRules = explode('|', $rule);
        
        foreach ($fieldRules as $fieldRule) {
            if ($fieldRule === 'required' && empty($value)) {
                $errors[$field] = "The $field field is required.";
                break;
            }
            
            if ($fieldRule === 'email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = "The $field must be a valid email address.";
                break;
            }
            
            if (preg_match('/min:(\d+)/', $fieldRule, $matches) && strlen($value) < $matches[1]) {
                $errors[$field] = "The $field must be at least {$matches[1]} characters.";
                break;
            }
            
            if (preg_match('/max:(\d+)/', $fieldRule, $matches) && strlen($value) > $matches[1]) {
                $errors[$field] = "The $field may not be greater than {$matches[1]} characters.";
                break;
            }
        }
    }
    
    return $errors;
}

// Flash messages
function flash($key, $message = null) {
    if ($message === null) {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    
    $_SESSION['flash'][$key] = $message;
}

// CSRF protection
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
}

function verifyCsrf() {
    $token = request('_token');
    return $token && hash_equals($_SESSION['csrf_token'], $token);
}

// Base Model class
class BaseModel {
    protected static $table;
    protected static $fillable = [];
    protected $attributes = [];
    
    public function __construct($attributes = []) {
        $this->attributes = $attributes;
    }
    
    public function __get($key) {
        return $this->attributes[$key] ?? null;
    }
    
    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }
    
    public static function all() {
        $db = getDbConnection();
        if (!$db) return [];
        
        try {
            $stmt = $db->query("SELECT * FROM " . static::$table . " ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    public static function find($id) {
        $db = getDbConnection();
        if (!$db) return null;
        
        try {
            $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
    
    public function save() {
        $db = getDbConnection();
        if (!$db) return false;
        
        $fillable = array_intersect_key($this->attributes, array_flip(static::$fillable));
        
        try {
            if (isset($this->attributes['id']) && $this->attributes['id']) {
                // Update
                $fields = implode(' = ?, ', array_keys($fillable)) . ' = ?';
                $stmt = $db->prepare("UPDATE " . static::$table . " SET $fields, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                return $stmt->execute([...array_values($fillable), $this->attributes['id']]);
            } else {
                // Insert
                $fields = implode(', ', array_keys($fillable));
                $placeholders = str_repeat('?, ', count($fillable) - 1) . '?';
                $stmt = $db->prepare("INSERT INTO " . static::$table . " ($fields) VALUES ($placeholders)");
                if ($stmt->execute(array_values($fillable))) {
                    $this->attributes['id'] = $db->lastInsertId();
                    return true;
                }
            }
        } catch (PDOException $e) {
            return false;
        }
        
        return false;
    }
    
    public static function where($column, $operator, $value = null) {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        
        $db = getDbConnection();
        if (!$db) return [];
        
        try {
            $stmt = $db->prepare("SELECT * FROM " . static::$table . " WHERE $column $operator ? ORDER BY created_at DESC");
            $stmt->execute([$value]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

// Load simple models
require_once BASE_PATH . '/bootstrap/simple-models.php';

// Initialize database
createTables();

// Create default admin user if none exists
$db = getDbConnection();
if ($db) {
    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        $adminUser = new User([
            'username' => 'admin',
            'email' => 'admin@clinic.edu',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'role' => 'admin',
            'is_active' => 1
        ]);
        $adminUser->save();
        
        // Create a sample doctor
        $doctor = new User([
            'username' => 'dr.smith',
            'email' => 'dr.smith@clinic.edu',
            'password' => password_hash('doctor123', PASSWORD_DEFAULT),
            'first_name' => 'Dr. Sarah',
            'last_name' => 'Smith',
            'role' => 'doctor',
            'is_active' => 1
        ]);
        $doctor->save();
        
        echo "✅ Default users created: admin@clinic.edu / admin123, dr.smith@clinic.edu / doctor123\n";
    }
}

echo "✅ Laravel Bootstrap loaded successfully!\n";
echo "Database connection: " . (getDbConnection() ? "✅ Connected (SQLite)" : "❌ Failed") . "\n";
echo "Environment: " . ($_ENV['APP_ENV'] ?? 'local') . "\n";
echo "Debug mode: " . ($_ENV['APP_DEBUG'] ?? 'false') . "\n";

?>
