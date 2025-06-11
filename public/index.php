<?php
/**
 * University Clinic Management System
 * Main Application Entry Point
 * 
 * Clean, production-ready clinic management system
 */

// Load the core system
require_once __DIR__ . '/minimal-system.php';

// Load application routing
require_once __DIR__ . '/app.php';

// Initialize the application
try {
    // Start session
    session_start();
    
    // Initialize database
    initializeDatabase();
    
    // Handle the request
    $router->handle();
    
} catch (Exception $e) {
    // Handle any errors gracefully
    http_response_code(500);
    
    if ($_ENV['APP_DEBUG'] ?? false) {
        echo "Error: " . $e->getMessage();
    } else {
        echo "System temporarily unavailable. Please try again later.";
    }
    
    // Log the error
    error_log("Clinic System Error: " . $e->getMessage());
}
