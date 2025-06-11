<?php

// Simple demo bootstrap file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Basic routing
$request_uri = $_SERVER['REQUEST_URI'];
$request_method = $_SERVER['REQUEST_METHOD'];

// Parse URL
$path = parse_url($request_uri, PHP_URL_PATH);
$query = parse_url($request_uri, PHP_URL_QUERY);

// Simple routing
switch ($path) {
    case '/':
    case '/login':
        include 'demo/login.php';
        break;
    
    case '/dashboard':
        include 'demo/dashboard.php';
        break;
    
    case '/kiosk':
        include 'demo/kiosk.php';
        break;
    
    case '/mobile':
        include 'demo/mobile.php';
        break;
    
    case '/patients':
        include 'demo/patients.php';
        break;
    
    case '/appointments':
        include 'demo/appointments.php';
        break;
    
    case '/queue':
        include 'demo/queue.php';
        break;
    
    default:
        http_response_code(404);
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>404 - Page Not Found</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 text-center">
                        <h1>404</h1>
                        <p>Page not found</p>
                        <a href="/" class="btn btn-primary">Go Home</a>
                    </div>
                </div>
            </div>
        </body>
        </html>';
        break;
}
