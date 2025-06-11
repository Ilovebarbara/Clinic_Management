<?php

/**
 * Minimal Working Laravel System
 * A simple clinic management system that actually works
 */

// Basic setup
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);

// Environment setup
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = 'database/clinic.sqlite';
$_ENV['APP_ENV'] = 'local';

// Define debug constant
define('APP_DEBUG', true);

// Create database directory
$dbDir = BASE_PATH . '/database';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

// SQLite connection
function db() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dbPath = BASE_PATH . '/' . $_ENV['DB_DATABASE'];
        try {
            $pdo = new PDO("sqlite:$dbPath");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE,
                email TEXT UNIQUE,
                password TEXT,
                first_name TEXT,
                last_name TEXT,
                role TEXT DEFAULT 'patient',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");
              $pdo->exec("CREATE TABLE IF NOT EXISTS patients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                student_employee_no TEXT UNIQUE,
                first_name TEXT,
                middle_name TEXT,
                last_name TEXT,
                email TEXT,
                phone TEXT,
                address TEXT,
                age INTEGER,
                sex TEXT,
                campus TEXT,
                college_office TEXT,
                course_year_designation TEXT,
                emergency_contact_name TEXT,
                emergency_contact_relation TEXT,
                emergency_contact_phone TEXT,
                blood_type TEXT,
                allergies TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )");
            
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    return $pdo;
}

// Auth helpers
function auth() {
    return $_SESSION['user'] ?? null;
}

function login($username, $password) {
    $db = db();
    if (!$db) return false;
    
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    
    return false;
}

// Create default admin if none exists
function setupDefaultUsers() {
    $db = db();
    if (!$db) return false;
    
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        // Create admin user
        $stmt = $db->prepare("INSERT INTO users (username, email, password, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'admin',
            'admin@clinic.edu',
            password_hash('admin123', PASSWORD_DEFAULT),
            'System',
            'Administrator',
            'admin'
        ]);
        
        // Create doctor user
        $stmt->execute([
            'dr.smith',
            'dr.smith@clinic.edu',
            password_hash('doctor123', PASSWORD_DEFAULT),
            'Dr. Sarah',
            'Smith',
            'doctor'
        ]);
        
        // Create nurse user
        $stmt->execute([
            'nurse.jones',
            'nurse.jones@clinic.edu',
            password_hash('nurse123', PASSWORD_DEFAULT),
            'Emily',
            'Jones',
            'nurse'        ]);
        
        return true;
    }
    
    return false;
}

?>
