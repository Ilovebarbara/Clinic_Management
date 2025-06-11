<?php
/**
 * University Clinic Management System
 * Public Entry Point (Laravel-like structure)
 * 
 * Clean, production-ready clinic management system
 */

// Load the core system from parent directory
require_once dirname(__DIR__) . '/minimal-system.php';

// Load application routing from parent directory
require_once dirname(__DIR__) . '/app.php';

// Initialize the application
try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Initialize database by calling db() function
    db(); // This will create tables if they don't exist
    
    // Handle the request
    $router->handle();
    
} catch (Exception $e) {
    // Handle any errors gracefully
    http_response_code(500);
    
    if (defined('APP_DEBUG') && APP_DEBUG) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "System temporarily unavailable. Please try again later.";
    }
      // Log the error
    error_log("Clinic System Error: " . $e->getMessage());
}
