<?php
/**
 * University Clinic Management System
 * Main Entry Point
 * 
 * Clean, production-ready clinic management system
 */

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load the core system
require_once __DIR__ . '/minimal-system.php';

// Load application routing
require_once __DIR__ . '/app.php';

// Initialize the application
try {
    // Initialize database by calling db() function
    db(); // This will create tables if they don't exist
    
    // Handle the request
    if (isset($router)) {
        $router->handle();
    } else {
        // Fallback routing if router not available
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = trim($path, '/');
        
        if (empty($path) || $path === 'index.php') {
            include __DIR__ . '/demo/dashboard.php';
        } elseif (file_exists(__DIR__ . "/demo/{$path}.php")) {
            include __DIR__ . "/demo/{$path}.php";
        } else {
            http_response_code(404);
            echo "404 - Page not found";
        }
    }
    
} catch (Exception $e) {
    // Handle any errors gracefully
    http_response_code(500);
    
    $debug = $_ENV['APP_DEBUG'] ?? false;
    if ($debug) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "System temporarily unavailable. Please try again later.";
    }
    
    // Log the error
    error_log("Clinic System Error: " . $e->getMessage());
}
