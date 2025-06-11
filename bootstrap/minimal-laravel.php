<?php

/**
 * Minimal Laravel Bootstrap for Development
 * This file provides basic Laravel functionality without Composer dependencies
 */

// Set up basic paths
define('LARAVEL_START', microtime(true));
$basePath = __DIR__;

// Create basic application structure
if (!file_exists($basePath . '/vendor/autoload.php')) {
    
    // Basic PSR-4 autoloader
    spl_autoload_register(function ($class) {
        $basePath = __DIR__;
        
        // Handle App namespace
        if (strpos($class, 'App\\') === 0) {
            $file = $basePath . '/app/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
        
        // Handle basic Laravel classes - simplified
        $basicClasses = [
            'Illuminate\\Http\\Request' => __DIR__ . '/bootstrap/basic-request.php',
            'Illuminate\\Http\\Response' => __DIR__ . '/bootstrap/basic-response.php',
            'Illuminate\\Support\\Facades\\Route' => __DIR__ . '/bootstrap/basic-route.php',
        ];
        
        if (isset($basicClasses[$class])) {
            if (file_exists($basicClasses[$class])) {
                require_once $basicClasses[$class];
            }
        }
    });

    // Load environment variables manually
    if (file_exists($basePath . '/.env')) {
        $envFile = file_get_contents($basePath . '/.env');
        $lines = explode("\n", $envFile);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) continue;
            
            $parts = explode('=', $line, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1], '"\'');
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }

    return false; // Indicate Composer is not available
}

// If Composer exists, use it
require_once $basePath . '/vendor/autoload.php';
return true; // Indicate Composer is available
?>
